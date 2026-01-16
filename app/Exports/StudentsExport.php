<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected Collection $students;

    public function __construct(Collection $students)
    {
        $this->students = $students;
    }

    public function collection()
    {
        return $this->students->load(['registration.destinationInstitution', 'parents']);
    }

    public function headings(): array
    {
        return [
            'No. Pendaftaran',
            'Nama Lengkap',
            'NIK',
            'NISN',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Alamat',
            'Desa/Kelurahan',
            'Kecamatan',
            'Kabupaten/Kota',
            'Provinsi',
            'Nama Ayah',
            'No. WA Ayah',
            'Nama Ibu',
            'No. WA Ibu',
            'Sekolah Tujuan',
            'Status',
        ];
    }

    public function map($student): array
    {
        $father = $student->parents->firstWhere('type', 'father');
        $mother = $student->parents->firstWhere('type', 'mother');

        return [
            $student->registration_number,
            $student->full_name,
            $student->nik,
            $student->nisn,
            $student->gender === 'male' ? 'Laki-laki' : 'Perempuan',
            $student->place_of_birth,
            $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d/m/Y') : '',
            $student->address_street,
            $student->village,
            $student->district,
            $student->regency,
            $student->province,
            $father?->name,
            $father?->phone_number,
            $mother?->name,
            $mother?->phone_number,
            $student->registration?->destinationInstitution?->name,
            match ($student->status) {
                'draft' => 'Menunggu',
                'verified' => 'Terverifikasi',
                'accepted' => 'Diterima',
                'rejected' => 'Ditolak',
                default => $student->status,
            },
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
