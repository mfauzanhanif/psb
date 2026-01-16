<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Institution;
use App\Models\Registration;
use App\Models\Student;
use App\Models\StudentParent;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $pondok = Institution::where('type', 'pondok')->first();
        $smp = Institution::where('type', 'smp')->first();
        $ma = Institution::where('type', 'ma')->first();

        // Sample students data
        $students = [
            [
                'full_name' => 'Ahmad Fauzi',
                'nik' => '3209010101010001',
                'nisn' => '0012345671',
                'place_of_birth' => 'Cirebon',
                'date_of_birth' => '2012-05-15',
                'gender' => 'male',
                'child_number' => 1,
                'total_siblings' => 3,
                'province' => 'Jawa Barat',
                'regency' => 'Kabupaten Cirebon',
                'district' => 'Weru',
                'village' => 'Mertapada Wetan',
                'address_street' => 'Jl. Masjid Agung No. 10',
                'postal_code' => '45154',
                'status' => 'accepted',
                'destination_institution' => $smp,
            ],
            [
                'full_name' => 'Siti Aisyah',
                'nik' => '3209010101010002',
                'nisn' => '0012345672',
                'place_of_birth' => 'Kuningan',
                'date_of_birth' => '2011-08-20',
                'gender' => 'female',
                'child_number' => 2,
                'total_siblings' => 2,
                'province' => 'Jawa Barat',
                'regency' => 'Kabupaten Kuningan',
                'district' => 'Kuningan',
                'village' => 'Cigugur',
                'address_street' => 'Jl. Raya Cigugur No. 5',
                'postal_code' => '45552',
                'status' => 'accepted',
                'destination_institution' => $ma,
            ],
            [
                'full_name' => 'Muhammad Rizki',
                'nik' => '3209010101010003',
                'nisn' => '0012345673',
                'place_of_birth' => 'Indramayu',
                'date_of_birth' => '2012-03-10',
                'gender' => 'male',
                'child_number' => 1,
                'total_siblings' => 4,
                'province' => 'Jawa Barat',
                'regency' => 'Kabupaten Indramayu',
                'district' => 'Jatibarang',
                'village' => 'Jatibarang Baru',
                'address_street' => 'Jl. Pasar Baru No. 22',
                'postal_code' => '45273',
                'status' => 'accepted',
                'destination_institution' => $smp,
            ],
            [
                'full_name' => 'Fatimah Zahra',
                'nik' => '3209010101010004',
                'nisn' => '0012345674',
                'place_of_birth' => 'Majalengka',
                'date_of_birth' => '2011-11-25',
                'gender' => 'female',
                'child_number' => 3,
                'total_siblings' => 3,
                'province' => 'Jawa Barat',
                'regency' => 'Kabupaten Majalengka',
                'district' => 'Talaga',
                'village' => 'Talaga Kulon',
                'address_street' => 'Jl. Talaga Raya No. 15',
                'postal_code' => '45463',
                'status' => 'accepted',
                'destination_institution' => $ma,
            ],
            [
                'full_name' => 'Abdullah Rahman',
                'nik' => '3209010101010005',
                'nisn' => '0012345675',
                'place_of_birth' => 'Cirebon',
                'date_of_birth' => '2012-07-05',
                'gender' => 'male',
                'child_number' => 2,
                'total_siblings' => 2,
                'province' => 'Jawa Barat',
                'regency' => 'Kota Cirebon',
                'district' => 'Kejaksan',
                'village' => 'Kesenden',
                'address_street' => 'Jl. Kesenden No. 8',
                'postal_code' => '45123',
                'status' => 'draft',
                'destination_institution' => $smp,
            ],
        ];

        foreach ($students as $studentData) {
            $destInstitution = $studentData['destination_institution'];
            unset($studentData['destination_institution']);

            // Create student
            $student = Student::create($studentData);

            // Create registration
            Registration::create([
                'student_id' => $student->id,
                'academic_year_id' => $activeYear->id,
                'destination_institution_id' => $destInstitution?->id,
                'destination_class' => '7',
                'previous_school_level' => 'SD/Sederajat',
                'previous_school_name' => 'SD Negeri ' . rand(1, 10),
                'previous_school_npsn' => '1010' . rand(1000, 9999),
                'previous_school_address' => 'Jl. Pendidikan No. ' . rand(1, 50),
                'funding_source' => 'mandiri',
            ]);

            // Create parent data
            StudentParent::create([
                'student_id' => $student->id,
                'type' => 'father',
                'name' => 'Bapak ' . explode(' ', $studentData['full_name'])[0],
                'nik' => '320901' . rand(1000000000, 9999999999),
                'phone_number' => '0812' . rand(10000000, 99999999),
                'life_status' => 'alive',
            ]);

            StudentParent::create([
                'student_id' => $student->id,
                'type' => 'mother',
                'name' => 'Ibu ' . explode(' ', $studentData['full_name'])[0],
                'nik' => '320901' . rand(1000000000, 9999999999),
                'phone_number' => '0813' . rand(10000000, 99999999),
                'life_status' => 'alive',
            ]);

            // Generate bills for this student (Pondok + Madrasah + Destination)
            $student->generateBills();
        }
    }
}
