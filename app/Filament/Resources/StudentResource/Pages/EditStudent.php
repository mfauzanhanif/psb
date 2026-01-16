<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\StudentDocument;
use App\Models\StudentParent;
use Filament\Resources\Pages\EditRecord;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $student = $this->record;

        // Load parent data
        $father = $student->parents->where('type', 'father')->first();
        $mother = $student->parents->where('type', 'mother')->first();
        $guardian = $student->parents->where('type', 'guardian')->first();

        if ($father) {
            $data['father_name'] = $father->name;
            $data['father_life_status'] = $father->life_status;
            $data['father_nik'] = $father->nik;
            $data['father_place_of_birth'] = $father->place_of_birth;
            $data['father_date_of_birth'] = $father->date_of_birth;
            $data['father_education'] = $father->education;
            $data['father_has_pesantren'] = !empty($father->pesantren_education);
            $data['father_pesantren_name'] = $father->pesantren_education;
            $data['father_job'] = $father->job;
            $data['father_income'] = $father->income;
            $data['father_phone'] = $father->phone_number;
        }

        if ($mother) {
            $data['mother_name'] = $mother->name;
            $data['mother_life_status'] = $mother->life_status;
            $data['mother_nik'] = $mother->nik;
            $data['mother_place_of_birth'] = $mother->place_of_birth;
            $data['mother_date_of_birth'] = $mother->date_of_birth;
            $data['mother_education'] = $mother->education;
            $data['mother_has_pesantren'] = !empty($mother->pesantren_education);
            $data['mother_pesantren_name'] = $mother->pesantren_education;
            $data['mother_job'] = $mother->job;
            $data['mother_income'] = $mother->income;
            $data['mother_phone'] = $mother->phone_number;
        }

        if ($guardian) {
            $data['wali_type'] = 'other';
            $data['guardian_name'] = $guardian->name;
            $data['guardian_life_status'] = $guardian->life_status;
            $data['guardian_nik'] = $guardian->nik;
            $data['guardian_place_of_birth'] = $guardian->place_of_birth;
            $data['guardian_date_of_birth'] = $guardian->date_of_birth;
            $data['guardian_education'] = $guardian->education;
            $data['guardian_has_pesantren'] = !empty($guardian->pesantren_education);
            $data['guardian_pesantren_name'] = $guardian->pesantren_education;
            $data['guardian_job'] = $guardian->job;
            $data['guardian_income'] = $guardian->income;
            $data['guardian_phone'] = $guardian->phone_number;
        } else {
            $data['wali_type'] = 'father'; // Default
        }

        // Load document data
        foreach ($student->documents as $doc) {
            $data['doc_' . $doc->type] = $doc->file_path;
        }

        // Load registration data
        $registration = $student->registration;
        if ($registration) {
            $data['previous_school_level'] = $registration->previous_school_level;
            $data['previous_school_name'] = $registration->previous_school_name;
            $data['previous_school_npsn'] = $registration->previous_school_npsn;
            $data['previous_school_address'] = $registration->previous_school_address;
            $data['destination_institution_id'] = $registration->destination_institution_id;
            $data['destination_class'] = $registration->destination_class;
            $data['funding_source'] = $registration->funding_source;
        }

        // Load territory codes from names for cascading dropdowns
        if ($student->province) {
            $provinceCode = \App\Services\WilayahService::findProvinceCode($student->province);
            $data['province_code'] = $provinceCode;

            if ($provinceCode && $student->regency) {
                $regencyCode = \App\Services\WilayahService::findRegencyCode($provinceCode, $student->regency);
                $data['regency_code'] = $regencyCode;

                if ($regencyCode && $student->district) {
                    $districtCode = \App\Services\WilayahService::findDistrictCode($regencyCode, $student->district);
                    $data['district_code'] = $districtCode;

                    if ($districtCode && $student->village) {
                        $villageCode = \App\Services\WilayahService::findVillageCode($districtCode, $student->village);
                        $data['village_code'] = $villageCode;
                    }
                }
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store parent and document data for afterSave
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
            'funding_source' => $data['funding_source'] ?? 'Mandiri',
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

    protected function afterSave(): void
    {
        $student = $this->record;

        // Update parents
        foreach ($this->parentData as $type => $parentInfo) {
            $existing = $student->parents()->where('type', $type)->first();

            if ($parentInfo && !empty($parentInfo['name'])) {
                $parentData = [
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
                ];

                if ($existing) {
                    $existing->update($parentData);
                } else {
                    StudentParent::create($parentData);
                }
            } elseif ($existing && $type === 'guardian') {
                // Remove guardian if wali_type changed from 'other'
                $existing->delete();
            }
        }

        // Update documents
        foreach ($this->documentData as $type => $filePath) {
            $existing = $student->documents()->where('type', $type)->first();

            if ($filePath) {
                if ($existing) {
                    $existing->update(['file_path' => $filePath]);
                } else {
                    StudentDocument::create([
                        'student_id' => $student->id,
                        'type' => $type,
                        'file_path' => $filePath,
                        'status' => 'pending',
                    ]);
                }
            }
        }

        // Update registration
        $registration = $student->registration;
        if ($registration) {
            $registration->update($this->registrationData);
        }
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
