<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RegistrationPdfController extends Controller
{
    public function download(Student $student)
    {
        // Load relationships with eager loading for better performance
        $student->load([
            'parents',
            'registration.destinationInstitution',
            'registration.academicYear',
        ]);

        $registration = $student->registration;
        $father = $student->parents->where('type', 'father')->first();
        $mother = $student->parents->where('type', 'mother')->first();
        $guardian = $student->parents->where('type', 'guardian')->first();

        // Determine wali type for proper display
        $waliType = $registration->wali_type ?? 'father';

        $pdf = Pdf::loadView('pdf.forms.registration', [
            'student' => $student,
            'registration' => $registration,
            'father' => $father,
            'mother' => $mother,
            'guardian' => $guardian,
            'waliType' => $waliType,
        ]);

        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("Pendaftaran-{$student->registration_number}.pdf");
    }
}
