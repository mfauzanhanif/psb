<?php

namespace App\Livewire;

use App\Models\FeeComponent;
use App\Models\AcademicYear;
use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        // Get active academic year
        $activeYear = AcademicYear::where('is_active', true)->first();

        // Fetch all fee components for active year
        $allFees = FeeComponent::with('institution')
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->get();

        // Filter by type column
        $registrationFees = $allFees->where('type', 'yearly');
        $monthlyFees = $allFees->where('type', 'monthly');

        // If no data found using type column, fallback to keyword-based filtering for backwards compatibility
        if ($registrationFees->isEmpty() && $monthlyFees->isEmpty() && $allFees->isNotEmpty()) {
            // Keywords for registration fees (one-time payments)
            $registrationKeywords = ['pendaftaran', 'gedung', 'seragam', 'perlengkapan', 'kitab', 'buku', 'pangkal', 'daftar ulang', 'uang gedung'];
            $registrationFees = $allFees->filter(function ($fee) use ($registrationKeywords) {
                $name = strtolower($fee->name);
                foreach ($registrationKeywords as $keyword) {
                    if (str_contains($name, $keyword)) {
                        return true;
                    }
                }
                return false;
            });

            // Keywords for monthly fees
            $monthlyKeywords = ['spp', 'syahriah', 'makan', 'laundry', 'bulanan', 'listrik', 'uang jajan'];
            $monthlyFees = $allFees->filter(function ($fee) use ($monthlyKeywords) {
                $name = strtolower($fee->name);
                foreach ($monthlyKeywords as $keyword) {
                    if (str_contains($name, $keyword)) {
                        return true;
                    }
                }
                return false;
            });

            // If still no categorization works, show all in registration
            if ($registrationFees->isEmpty() && $monthlyFees->isEmpty()) {
                $registrationFees = $allFees;
            }
        }

        return view('livewire.home', [
            'registrationFees' => $registrationFees,
            'monthlyFees' => $monthlyFees,
            'totalRegistration' => $registrationFees->sum('amount'),
            'totalMonthly' => $monthlyFees->sum('amount'),
        ]);
    }
}
