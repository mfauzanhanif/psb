<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number',
        'full_name',
        'nik',
        'nisn',
        'place_of_birth',
        'date_of_birth',
        'gender',
        'child_number',
        'total_siblings',
        'address_street',
        'village',
        'district',
        'regency',
        'province',
        'postal_code',
        'status',
    ];

    /**
     * Boot method to handle model events.
     */
    protected static function booted(): void
    {
        static::creating(function ($student) {
            if (empty($student->registration_number)) {
                $student->registration_number = self::generateRegistrationNumber();
            }
        });

        // Bills are generated manually after registration is created
        // See CreateStudent::afterCreate() and RegistrationWizard
    }

    /**
     * Generate a unique registration number.
     * Format: YYXXXX (e.g., 250001 for first registration in 2025)
     */
    public static function generateRegistrationNumber(): string
    {
        return \DB::transaction(function () {
            $activeYear = AcademicYear::where('is_active', true)->first();

            if ($activeYear) {
                $parts = explode('/', $activeYear->name);
                $year = $parts[0] ?? date('Y');
            } else {
                $year = date('Y');
            }

            // Get last 2 digits of year
            $yearPrefix = substr($year, -2);

            $lastStudent = self::where('registration_number', 'like', "{$yearPrefix}%")
                ->where('registration_number', 'regexp', '^[0-9]{6}$')
                ->lockForUpdate()
                ->orderByRaw('CAST(registration_number AS UNSIGNED) DESC')
                ->first();

            if (!$lastStudent) {
                $number = 1;
            } else {
                $lastNumber = (int) substr($lastStudent->registration_number, 2);
                $number = $lastNumber + 1;
            }

            return $yearPrefix . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Generate bills for the student based on their registration.
     * Creates separate bills for each institution: Pondok, Madrasah, and destination institution.
     */
    public function generateBills(): void
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return;
        }

        // Get all institutions needed for billing
        $registration = $this->registration;
        $pondok = Institution::where('type', 'pondok')->first();
        $madrasah = Institution::where('type', 'madrasah')->first();

        $institutions = collect();

        // 1. Pondok fees (all students)
        if ($pondok) {
            $institutions->push($pondok);
        }

        // 2. Madrasah fees (all students)
        if ($madrasah) {
            $institutions->push($madrasah);
        }

        // 3. Destination institution fees (SMP/MA if selected)
        if ($registration && $registration->destination_institution_id) {
            $destInstitution = Institution::find($registration->destination_institution_id);
            // Only add if different from pondok/madrasah
            if ($destInstitution && !in_array($destInstitution->type, ['pondok', 'madrasah'])) {
                $institutions->push($destInstitution);
            }
        }

        // Create separate bill for each institution
        foreach ($institutions as $institution) {
            // Skip if bill already exists for this institution
            if ($this->bills()->where('institution_id', $institution->id)->exists()) {
                continue;
            }

            $feeComponents = FeeComponent::where('institution_id', $institution->id)
                ->where('academic_year_id', $activeYear->id)
                ->get();

            if ($feeComponents->isEmpty()) {
                continue;
            }

            $totalAmount = $feeComponents->sum('amount');

            // Build description
            $descriptions = $feeComponents->map(function ($fee) {
                return $fee->name . ': Rp ' . number_format($fee->amount, 0, ',', '.');
            })->implode("\n");

            Bill::create([
                'student_id' => $this->id,
                'institution_id' => $institution->id,
                'amount' => $totalAmount,
                'remaining_amount' => $totalAmount,
                'status' => 'unpaid',
                'description' => $descriptions,
            ]);
        }
    }

    /**
     * Regenerate all bills for this student.
     * Recalculates amounts based on current fee components.
     */
    public function regenerateBills(): void
    {
        foreach ($this->bills as $bill) {
            $bill->recalculateAmount();
        }
    }

    public function parents(): HasMany
    {
        return $this->hasMany(StudentParent::class);
    }

    public function registration(): HasOne
    {
        return $this->hasOne(Registration::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get total amount paid by this student (sum of all transactions).
     */
    public function getTotalPaid(): float
    {
        return (float) $this->transactions()->sum('amount');
    }

    /**
     * Get total bill amount for this student.
     */
    public function getTotalBillAmount(): float
    {
        return (float) $this->bills()->sum('amount');
    }

    /**
     * Get remaining amount to be paid.
     */
    public function getTotalRemaining(): float
    {
        return max(0, $this->getTotalBillAmount() - $this->getTotalPaid());
    }

    /**
     * Get payment status: 'unpaid', 'partial', or 'paid'.
     */
    public function getPaymentStatus(): string
    {
        $totalBill = $this->getTotalBillAmount();
        $totalPaid = $this->getTotalPaid();

        if ($totalPaid <= 0) {
            return 'unpaid';
        }

        if ($totalPaid >= $totalBill) {
            return 'paid';
        }

        return 'partial';
    }

    /**
     * Get total undistributed amount (paid but not yet transferred to institutions).
     */
    public function getUndistributedAmount(): float
    {
        $totalPaid = $this->getTotalPaid();
        $totalDistributed = FundTransfer::where('student_id', $this->id)->sum('amount');

        return max(0, $totalPaid - (float) $totalDistributed);
    }
}
