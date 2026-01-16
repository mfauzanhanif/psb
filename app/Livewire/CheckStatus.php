<?php

namespace App\Livewire;

use App\Models\Student;
use Livewire\Component;

class CheckStatus extends Component
{
    public $registration_number;

    public $isVerified = false;
    public $student = null;
    public $activeTab = 'biodata';

    public function render()
    {
        $bills = $this->isVerified ? $this->student->bills()->get() : collect();

        // Calculate totals
        $totalBill = $bills->sum('amount');
        $remainingAmount = $bills->sum('remaining_amount');
        $totalPaid = $totalBill - $remainingAmount;

        // Determine overall status
        $overallStatus = 'unpaid';
        if ($remainingAmount == 0 && $totalBill > 0) {
            $overallStatus = 'paid';
        } elseif ($totalPaid > 0 && $remainingAmount > 0) {
            $overallStatus = 'partial';
        }

        return view('livewire.check-status', [
            'bills' => $bills,
            'totalBill' => $totalBill,
            'totalPaid' => $totalPaid,
            'remainingAmount' => $remainingAmount,
            'overallStatus' => $overallStatus,
        ]);
    }

    public function check()
    {
        $this->validate([
            'registration_number' => 'required|string',
        ]);

        $student = Student::where('registration_number', $this->registration_number)->first();

        if ($student) {
            $this->student = $student;
            $this->isVerified = true;
        } else {
            $this->addError('login_failed', 'Data tidak ditemukan. Periksa kembali Nomor Pendaftaran Anda.');
        }
    }

    public function logout()
    {
        $this->reset(['registration_number', 'isVerified', 'student', 'activeTab']);
    }
}
