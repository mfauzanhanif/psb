<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\Registration;
use App\Models\StudentDocument;
use App\Models\StudentParent;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract parent and document data before creating student
        // These will be saved after student is created
        $this->parentData = [
            'father' => $this->extractParentData($data, 'father'),
            'mother' => $this->extractParentData($data, 'mother'),
            'guardian' => $data['wali_type'] === 'other' ? $this->extractParentData($data, 'guardian') : null,
        ];
        $this->waliType = $data['wali_type'] ?? 'father';

        $this->documentData = [
            'kk' => $data['doc_kk'] ?? null,
            'akta' => $data['doc_akta'] ?? null,
            'ijazah' => $data['doc_ijazah'] ?? null,
            'photo' => $data['doc_photo'] ?? null,
            'ktp_ayah' => $data['doc_ktp_ayah'] ?? null,
            'ktp_ibu' => $data['doc_ktp_ibu'] ?? null,
            'ktp_wali' => $data['doc_ktp_wali'] ?? null,
        ];

        $this->registrationData = [
            'previous_school_level' => $data['previous_school_level'] ?? null,
            'previous_school_name' => $data['previous_school_name'] ?? null,
            'previous_school_npsn' => $data['previous_school_npsn'] ?? null,
            'previous_school_address' => $data['previous_school_address'] ?? null,
            'destination_institution_id' => $data['destination_institution_id'] ?? null,
            'destination_class' => $data['destination_class'] ?? null,
            'funding_source' => $data['funding_source'] ?? 'Orang Tua',
        ];

        // Remove non-student fields
        $fieldsToRemove = [
            'father_name',
            'father_life_status',
            'father_nik',
            'father_place_of_birth',
            'father_date_of_birth',
            'father_education',
            'father_has_pesantren',
            'father_pesantren_name',
            'father_job',
            'father_job_other',
            'father_income',
            'father_phone',
            'mother_name',
            'mother_life_status',
            'mother_nik',
            'mother_place_of_birth',
            'mother_date_of_birth',
            'mother_education',
            'mother_has_pesantren',
            'mother_pesantren_name',
            'mother_job',
            'mother_job_other',
            'mother_income',
            'mother_phone',
            'guardian_name',
            'guardian_life_status',
            'guardian_nik',
            'guardian_place_of_birth',
            'guardian_date_of_birth',
            'guardian_education',
            'guardian_has_pesantren',
            'guardian_pesantren_name',
            'guardian_job',
            'guardian_job_other',
            'guardian_income',
            'guardian_phone',
            'wali_type',
            'doc_kk',
            'doc_akta',
            'doc_ijazah',
            'doc_photo',
            'doc_ktp_ayah',
            'doc_ktp_ibu',
            'doc_ktp_wali',
            'previous_school_level',
            'previous_school_name',
            'previous_school_npsn',
            'previous_school_address',
            'destination_institution_id',
            'destination_class',
            'funding_source',
            'province_code',
            'regency_code',
            'district_code',
            'village_code',
        ];

        foreach ($fieldsToRemove as $field) {
            unset($data[$field]);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $student = $this->record;

        // Save parents
        foreach ($this->parentData as $type => $parentInfo) {
            if ($parentInfo && !empty($parentInfo['name'])) {
                StudentParent::create([
                    'student_id' => $student->id,
                    'type' => $type,
                    'name' => $parentInfo['name'],
                    'life_status' => $parentInfo['life_status'] ?? 'alive',
                    'nik' => $parentInfo['nik'] ?? null,
                    'place_of_birth' => $parentInfo['place_of_birth'] ?? null,
                    'date_of_birth' => $parentInfo['date_of_birth'] ?? null,
                    'education' => $parentInfo['education'] ?? null,
                    'pesantren_education' => $parentInfo['pesantren_name'] ?? null,
                    'job' => $parentInfo['job'] === 'Lainnya' ? $parentInfo['job_other'] : $parentInfo['job'],
                    'income' => $parentInfo['income'] ?? null,
                    'phone_number' => $parentInfo['phone'] ?? null,
                ]);
            }
        }

        // Save documents
        foreach ($this->documentData as $type => $filePath) {
            if ($filePath) {
                StudentDocument::create([
                    'student_id' => $student->id,
                    'type' => $type,
                    'file_path' => $filePath,
                    'status' => 'pending',
                ]);
            }
        }

        // Save registration data
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        Registration::create([
            'student_id' => $student->id,
            'academic_year_id' => $activeYear?->id,
            'previous_school_level' => $this->registrationData['previous_school_level'],
            'previous_school_name' => $this->registrationData['previous_school_name'],
            'previous_school_npsn' => $this->registrationData['previous_school_npsn'],
            'previous_school_address' => $this->registrationData['previous_school_address'],
            'destination_institution_id' => $this->registrationData['destination_institution_id'],
            'destination_class' => $this->registrationData['destination_class'],
            'funding_source' => $this->registrationData['funding_source'],
        ]);

        // Refresh student to load the registration relationship, then generate bills
        $student->refresh();
        $student->generateBills();
    }

    private function extractParentData(array $data, string $prefix): ?array
    {
        $name = $data[$prefix . '_name'] ?? null;
        if (!$name) {
            return null;
        }

        return [
            'name' => $name,
            'life_status' => $data[$prefix . '_life_status'] ?? 'alive',
            'nik' => $data[$prefix . '_nik'] ?? null,
            'place_of_birth' => $data[$prefix . '_place_of_birth'] ?? null,
            'date_of_birth' => $data[$prefix . '_date_of_birth'] ?? null,
            'education' => $data[$prefix . '_education'] ?? null,
            'has_pesantren' => $data[$prefix . '_has_pesantren'] ?? false,
            'pesantren_name' => $data[$prefix . '_pesantren_name'] ?? null,
            'job' => $data[$prefix . '_job'] ?? null,
            'job_other' => $data[$prefix . '_job_other'] ?? null,
            'income' => $data[$prefix . '_income'] ?? null,
            'phone' => $data[$prefix . '_phone'] ?? null,
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
