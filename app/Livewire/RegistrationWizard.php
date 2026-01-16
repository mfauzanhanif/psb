<?php

namespace App\Livewire;

use App\Events\StudentRegistered;
use App\Models\AcademicYear;
use App\Models\Bill;
use App\Models\FeeComponent;
use App\Models\Institution;
use App\Models\Registration;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\StudentParent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\WithFileUploads;

class RegistrationWizard extends Component
{
    use WithFileUploads;

    public $currentStep = 1;
    public $totalSteps = 5;
    public $isSuccess = false;
    public $successData = [];

    // Step 1: Data Santri
    public $full_name;
    public $nik;
    public $nisn;
    public $place_of_birth;
    public $date_of_birth;
    public $gender = 'male';
    public $child_number;
    public $total_siblings;

    // Address - with IDs for cascading
    public $province_id;
    public $regency_id;
    public $district_id;
    public $village_id;
    public $province;
    public $regency;
    public $district;
    public $village;
    public $address_street;
    public $postal_code;

    // Dropdown data (cached)
    public $provinces = [];
    public $regencies = [];
    public $districts = [];
    public $villages = [];

    // Step 2: Data Orang Tua/Wali
    // Father
    public $father_name;
    public $father_life_status = 'alive';
    public $father_nik;
    public $father_place_of_birth;
    public $father_date_of_birth;
    public $father_education;
    public $father_has_pesantren = false;
    public $father_pesantren_name;
    public $father_job;
    public $father_job_other;
    public $father_income;
    public $father_phone;

    // Mother
    public $mother_name;
    public $mother_life_status = 'alive';
    public $mother_nik;
    public $mother_place_of_birth;
    public $mother_date_of_birth;
    public $mother_education;
    public $mother_has_pesantren = false;
    public $mother_pesantren_name;
    public $mother_job;
    public $mother_job_other;
    public $mother_income;
    public $mother_phone;

    // Wali selection
    public $wali_type = 'father'; // 'father', 'mother', 'other'

    // Guardian (only if wali_type == 'other')
    public $guardian_name;
    public $guardian_life_status = 'alive';
    public $guardian_nik;
    public $guardian_place_of_birth;
    public $guardian_date_of_birth;
    public $guardian_education;
    public $guardian_has_pesantren = false;
    public $guardian_pesantren_name;
    public $guardian_job;
    public $guardian_job_other;
    public $guardian_income;
    public $guardian_phone;

    // Step 3: Pilihan Sekolah
    public $previous_school_level;
    public $previous_school_name;
    public $previous_school_npsn;
    public $previous_school_address;
    public $destination_institution_id;
    public $destination_class;
    public $funding_source;
    public $estimatedFees = 0;

    // WhatsApp checkbox flags
    public $father_no_whatsapp = false;
    public $mother_no_whatsapp = false;
    public $guardian_no_whatsapp = false;

    // File size error tracking
    public $fileSizeErrors = [];

    // Step 4: Upload Dokumen
    public $kk_file;
    public $akta_file;
    public $ktp_ayah_file;
    public $ktp_ibu_file;
    public $ktp_wali_file;
    public $ijazah_file;
    public $nisn_file;
    public $kip_file;

    public function mount()
    {
        // Fetch provinces on component mount
        $this->loadProvinces();
    }

    public function loadProvinces()
    {
        $this->provinces = Cache::remember('api_provinces', 3600, function () {
            try {
                /** @var \Illuminate\Http\Client\Response $response */
                $response = Http::timeout(10)->get('https://wilayah.id/api/provinces.json');
                return $response->successful() ? ($response->json()['data'] ?? []) : [];
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    public function updatedProvinceId($value)
    {
        // Reset dependent fields
        $this->reset(['regency_id', 'district_id', 'village_id', 'regency', 'district', 'village']);
        $this->regencies = [];
        $this->districts = [];
        $this->villages = [];

        if ($value) {
            // Handle both array of objects and key-value formats
            $provinces = $this->provinces;
            if (isset($provinces[$value])) {
                // Key-value format: ['11' => 'ACEH']
                $this->province = is_array($provinces[$value]) ? $provinces[$value]['name'] : $provinces[$value];
            } else {
                // Array of objects format: [{'code': '11', 'name': 'ACEH'}]
                $selected = collect($provinces)->firstWhere('code', $value);
                $this->province = $selected['name'] ?? null;
            }

            // Fetch regencies from API with caching
            $this->regencies = Cache::remember("api_regencies_{$value}", 3600, function () use ($value) {
                try {
                    /** @var \Illuminate\Http\Client\Response $response */
                    $response = Http::timeout(10)->get("https://wilayah.id/api/regencies/{$value}.json");
                    return $response->successful() ? ($response->json()['data'] ?? []) : [];
                } catch (\Exception $e) {
                    return [];
                }
            });
        }
    }

    public function updatedRegencyId($value)
    {
        // Reset dependent fields
        $this->reset(['district_id', 'village_id', 'district', 'village']);
        $this->districts = [];
        $this->villages = [];

        if ($value) {
            // Handle both array of objects and key-value formats
            $regencies = $this->regencies;
            if (isset($regencies[$value])) {
                // Key-value format
                $this->regency = is_array($regencies[$value]) ? $regencies[$value]['name'] : $regencies[$value];
            } else {
                // Array of objects format
                $selected = collect($regencies)->firstWhere('code', $value);
                $this->regency = $selected['name'] ?? null;
            }

            // Fetch districts from API with caching
            $this->districts = Cache::remember("api_districts_{$value}", 3600, function () use ($value) {
                try {
                    /** @var \Illuminate\Http\Client\Response $response */
                    $response = Http::timeout(10)->get("https://wilayah.id/api/districts/{$value}.json");
                    return $response->successful() ? ($response->json()['data'] ?? []) : [];
                } catch (\Exception $e) {
                    return [];
                }
            });
        }
    }

    public function updatedDistrictId($value)
    {
        // Reset dependent fields
        $this->reset(['village_id', 'village']);
        $this->villages = [];

        if ($value) {
            // Handle both array of objects and key-value formats
            $districts = $this->districts;
            if (isset($districts[$value])) {
                // Key-value format
                $this->district = is_array($districts[$value]) ? $districts[$value]['name'] : $districts[$value];
            } else {
                // Array of objects format
                $selected = collect($districts)->firstWhere('code', $value);
                $this->district = $selected['name'] ?? null;
            }

            // Fetch villages from API with caching
            $this->villages = Cache::remember("api_villages_{$value}", 3600, function () use ($value) {
                try {
                    /** @var \Illuminate\Http\Client\Response $response */
                    $response = Http::timeout(10)->get("https://wilayah.id/api/villages/{$value}.json");
                    return $response->successful() ? ($response->json()['data'] ?? []) : [];
                } catch (\Exception $e) {
                    return [];
                }
            });
        }
    }

    public function updatedVillageId($value)
    {
        if ($value) {
            // Handle both array of objects and key-value formats
            $villages = $this->villages;
            if (isset($villages[$value])) {
                // Key-value format
                $this->village = is_array($villages[$value]) ? $villages[$value]['name'] : $villages[$value];
            } else {
                // Array of objects format
                $selected = collect($villages)->firstWhere('code', $value);
                $this->village = $selected['name'] ?? null;
            }
            // Optional: Auto fill postal code if available in DB
            // $this->postal_code = $selected['postal_code'] ?? null;
        }
    }

    public function render()
    {
        return view('livewire.registration-wizard', [
            'institutions' => Institution::whereIn('type', ['smp', 'ma', 'mts'])->get(),
        ]);
    }

    public function increaseStep()
    {
        $this->validateStep($this->currentStep);
        $this->currentStep++;
    }

    public function decreaseStep()
    {
        $this->currentStep--;
    }

    public function updatedDestinationInstitutionId()
    {
        $this->destination_class = null; // Reset class when institution changes
        $this->calculateEstimatedFees();
    }

    /**
     * Get available classes based on selected institution type
     */
    public function getAvailableClasses(): array
    {
        if (!$this->destination_institution_id) {
            return [];
        }

        $institution = Institution::find($this->destination_institution_id);
        if (!$institution) {
            return [];
        }

        if ($institution->type === 'smp') {
            return ['7' => 'Kelas 7', '8' => 'Kelas 8', '9' => 'Kelas 9'];
        } elseif ($institution->type === 'ma') {
            return ['10' => 'Kelas 10', '11' => 'Kelas 11', '12' => 'Kelas 12'];
        }

        return [];
    }

    /**
     * Check if current step is valid (all required fields filled)
     */
    public function isStepValid($step): bool
    {
        if ($step == 1) {
            return !empty($this->full_name)
                && !empty($this->nik) && strlen($this->nik) == 16
                && !empty($this->place_of_birth)
                && !empty($this->date_of_birth)
                && !empty($this->gender)
                && !empty($this->child_number)
                && !empty($this->total_siblings)
                && !empty($this->province)
                && !empty($this->regency)
                && !empty($this->district)
                && !empty($this->village)
                && !empty($this->address_street);
        } elseif ($step == 2) {
            $valid = !empty($this->father_name)
                && !empty($this->mother_name)
                && !empty($this->wali_type);

            // Check WhatsApp requirements - at least one must have WhatsApp
            $hasAtLeastOnePhone = false;

            if (!$this->father_no_whatsapp && !empty($this->father_phone)) {
                $hasAtLeastOnePhone = true;
            }
            if (!$this->mother_no_whatsapp && !empty($this->mother_phone)) {
                $hasAtLeastOnePhone = true;
            }
            if ($this->wali_type === 'other' && !$this->guardian_no_whatsapp && !empty($this->guardian_phone)) {
                $hasAtLeastOnePhone = true;
            }

            if (!$hasAtLeastOnePhone) {
                $valid = false;
            }

            if ($this->wali_type === 'other') {
                $valid = $valid && !empty($this->guardian_name);
            }

            return $valid;
        } elseif ($step == 3) {
            $valid = !empty($this->previous_school_level)
                && !empty($this->previous_school_name)
                && !empty($this->previous_school_address)
                && !empty($this->destination_institution_id)
                && !empty($this->funding_source);

            // Check if class is required (SMP or MA)
            $institution = Institution::find($this->destination_institution_id);
            if ($institution && in_array($institution->type, ['smp', 'ma'])) {
                $valid = $valid && !empty($this->destination_class);
            }

            return $valid;
        } elseif ($step == 4) {
            // KTP files are optional, but at least KK and Akta are required
            $hasRequiredFiles = $this->kk_file && $this->akta_file;
            $noFileSizeErrors = empty($this->fileSizeErrors);
            return $hasRequiredFiles && $noFileSizeErrors;
        }

        return true;
    }

    /**
     * Check file size and track errors
     */
    public function checkFileSize($propertyName): void
    {
        $file = $this->{$propertyName};
        if ($file && $file->getSize() > 5120 * 1024) {
            $this->fileSizeErrors[$propertyName] = true;
        } else {
            unset($this->fileSizeErrors[$propertyName]);
        }
    }

    public function updatedKkFile()
    {
        $this->checkFileSize('kk_file');
    }
    public function updatedAktaFile()
    {
        $this->checkFileSize('akta_file');
    }
    public function updatedKtpAyahFile()
    {
        $this->checkFileSize('ktp_ayah_file');
    }
    public function updatedKtpIbuFile()
    {
        $this->checkFileSize('ktp_ibu_file');
    }
    public function updatedKtpWaliFile()
    {
        $this->checkFileSize('ktp_wali_file');
    }
    public function updatedIjazahFile()
    {
        $this->checkFileSize('ijazah_file');
    }
    public function updatedNisnFile()
    {
        $this->checkFileSize('nisn_file');
    }
    public function updatedKipFile()
    {
        $this->checkFileSize('kip_file');
    }

    public function calculateEstimatedFees()
    {
        $this->estimatedFees = 0;
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear)
            return;

        // 1. Mandatory Fees (Pondok & Madrasah Dar Al Tauhid)
        $mandatoryTotal = FeeComponent::where('academic_year_id', $activeYear->id)
            ->whereHas('institution', function ($query) {
                $query->whereIn('type', ['pondok', 'madrasah']);
            })
            ->sum('amount');

        $this->estimatedFees += $mandatoryTotal;

        // 2. Formal School Fees
        if ($this->destination_institution_id) {
            $formalInstitution = Institution::find($this->destination_institution_id);
            if ($formalInstitution && $formalInstitution->type !== 'mts_external') {
                $formalTotal = FeeComponent::where('academic_year_id', $activeYear->id)
                    ->where('institution_id', $formalInstitution->id)
                    ->sum('amount');
                $this->estimatedFees += $formalTotal;
            }
        }
    }

    public function validateStep($step)
    {
        if ($step == 1) {
            $this->validate([
                'full_name' => 'required|string',
                'nik' => 'required|numeric|digits:16|unique:students,nik',
                'nisn' => 'nullable|numeric|digits:10',
                'place_of_birth' => 'required|string',
                'date_of_birth' => 'required|date',
                'gender' => 'required|in:male,female',
                'child_number' => 'required|numeric',
                'total_siblings' => 'required|numeric',
                'province_id' => 'required|string',
                'regency_id' => 'required|string',
                'district_id' => 'required|string',
                'village_id' => 'required|string',
                'province' => 'required|string',
                'regency' => 'required|string',
                'district' => 'required|string',
                'village' => 'required|string',
                'address_street' => 'required|string',
            ]);
        } elseif ($step == 2) {
            $rules = [
                'father_name' => 'required|string',
                'mother_name' => 'required|string',
                'wali_type' => 'required|in:father,mother,other',
            ];

            // If wali is 'other', require guardian name
            if ($this->wali_type === 'other') {
                $rules['guardian_name'] = 'required|string';
            }

            // At least one phone must be available
            $hasAtLeastOnePhone = false;
            if (!$this->father_no_whatsapp && !empty($this->father_phone)) {
                $hasAtLeastOnePhone = true;
            }
            if (!$this->mother_no_whatsapp && !empty($this->mother_phone)) {
                $hasAtLeastOnePhone = true;
            }
            if ($this->wali_type === 'other' && !$this->guardian_no_whatsapp && !empty($this->guardian_phone)) {
                $hasAtLeastOnePhone = true;
            }

            if (!$hasAtLeastOnePhone) {
                $this->addError('phone_required', 'Minimal satu nomor WhatsApp (Ayah/Ibu/Wali) harus tersedia.');
                throw new \Illuminate\Validation\ValidationException(validator([], []));
            }

            // NIK Validation for parents (Optional but must be 16 digits)
            $rules['father_nik'] = 'nullable|numeric|digits:16';
            $rules['mother_nik'] = 'nullable|numeric|digits:16';
            $rules['guardian_nik'] = 'nullable|numeric|digits:16';

            $this->validate($rules);
        } elseif ($step == 3) {
            $rules = [
                'previous_school_level' => 'required',
                'previous_school_name' => 'required',
                'previous_school_address' => 'required',
                'destination_institution_id' => 'required|exists:institutions,id',
                'funding_source' => 'required',
            ];

            // Require destination_class for SMP and MA
            $institution = Institution::find($this->destination_institution_id);
            if ($institution && in_array($institution->type, ['smp', 'ma'])) {
                $rules['destination_class'] = 'required|in:7,8,9,10,11,12';
            }

            $this->validate($rules);
        } elseif ($step == 4) {
            $rules = [
                'kk_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'akta_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'ktp_ayah_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'ktp_ibu_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'ijazah_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'nisn_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'kip_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ];

            // KTP Wali required only if wali_type is 'other'
            if ($this->wali_type === 'other') {
                $rules['ktp_wali_file'] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120';
            }

            $this->validate($rules);
        }
    }

    public function submit()
    {
        // Skip file validation here since files were already validated in step 4
        // Re-validating would fail because temporary files may not exist anymore

        // Check for active academic year first
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            $this->addError('global', 'Pendaftaran ditutup (Tidak ada Tahun Ajaran Aktif). Silakan hubungi admin.');
            return;
        }

        try {
            DB::transaction(function () use ($activeYear) {
                // 1. Create Student
                $student = Student::create([
                    'full_name' => $this->full_name,
                    'nik' => $this->nik,
                    'nisn' => $this->nisn,
                    'place_of_birth' => $this->place_of_birth,
                    'date_of_birth' => $this->date_of_birth,
                    'gender' => $this->gender,
                    'child_number' => $this->child_number,
                    'total_siblings' => $this->total_siblings,
                    'address_street' => $this->address_street,
                    'village' => $this->village,
                    'district' => $this->district,
                    'regency' => $this->regency,
                    'province' => $this->province,
                    'postal_code' => $this->postal_code,
                    'status' => 'draft',
                ]);

                // 2. Create Father
                if ($this->father_name) {
                    $fatherJob = $this->father_job === 'Lainnya' ? $this->father_job_other : $this->father_job;
                    StudentParent::create([
                        'student_id' => $student->id,
                        'type' => 'father',
                        'name' => $this->father_name,
                        'life_status' => $this->father_life_status,
                        'nik' => $this->father_nik,
                        'place_of_birth' => $this->father_place_of_birth,
                        'date_of_birth' => $this->father_date_of_birth,
                        'education' => $this->father_education,
                        'pesantren_education' => $this->father_has_pesantren ? $this->father_pesantren_name : null,
                        'job' => $fatherJob,
                        'income' => $this->father_income,
                        'phone_number' => $this->father_phone,
                    ]);
                }

                // 3. Create Mother
                if ($this->mother_name) {
                    $motherJob = $this->mother_job === 'Lainnya' ? $this->mother_job_other : $this->mother_job;
                    StudentParent::create([
                        'student_id' => $student->id,
                        'type' => 'mother',
                        'name' => $this->mother_name,
                        'life_status' => $this->mother_life_status,
                        'nik' => $this->mother_nik,
                        'place_of_birth' => $this->mother_place_of_birth,
                        'date_of_birth' => $this->mother_date_of_birth,
                        'education' => $this->mother_education,
                        'pesantren_education' => $this->mother_has_pesantren ? $this->mother_pesantren_name : null,
                        'job' => $motherJob,
                        'income' => $this->mother_income,
                        'phone_number' => $this->mother_phone,
                    ]);
                }

                // 4. Create Guardian based on wali_type
                if ($this->wali_type === 'father' && $this->father_name) {
                    // Copy father data to guardian
                    $fatherJob = $this->father_job === 'Lainnya' ? $this->father_job_other : $this->father_job;
                    StudentParent::create([
                        'student_id' => $student->id,
                        'type' => 'guardian',
                        'name' => $this->father_name,
                        'life_status' => $this->father_life_status,
                        'nik' => $this->father_nik,
                        'place_of_birth' => $this->father_place_of_birth,
                        'date_of_birth' => $this->father_date_of_birth,
                        'education' => $this->father_education,
                        'pesantren_education' => $this->father_has_pesantren ? $this->father_pesantren_name : null,
                        'job' => $fatherJob,
                        'income' => $this->father_income,
                        'phone_number' => $this->father_phone,
                    ]);
                } elseif ($this->wali_type === 'mother' && $this->mother_name) {
                    // Copy mother data to guardian
                    $motherJob = $this->mother_job === 'Lainnya' ? $this->mother_job_other : $this->mother_job;
                    StudentParent::create([
                        'student_id' => $student->id,
                        'type' => 'guardian',
                        'name' => $this->mother_name,
                        'life_status' => $this->mother_life_status,
                        'nik' => $this->mother_nik,
                        'place_of_birth' => $this->mother_place_of_birth,
                        'date_of_birth' => $this->mother_date_of_birth,
                        'education' => $this->mother_education,
                        'pesantren_education' => $this->mother_has_pesantren ? $this->mother_pesantren_name : null,
                        'job' => $motherJob,
                        'income' => $this->mother_income,
                        'phone_number' => $this->mother_phone,
                    ]);
                } elseif ($this->wali_type === 'other' && $this->guardian_name) {
                    // Use separate guardian data
                    $guardianJob = $this->guardian_job === 'Lainnya' ? $this->guardian_job_other : $this->guardian_job;
                    StudentParent::create([
                        'student_id' => $student->id,
                        'type' => 'guardian',
                        'name' => $this->guardian_name,
                        'life_status' => $this->guardian_life_status,
                        'nik' => $this->guardian_nik,
                        'place_of_birth' => $this->guardian_place_of_birth,
                        'date_of_birth' => $this->guardian_date_of_birth,
                        'education' => $this->guardian_education,
                        'pesantren_education' => $this->guardian_has_pesantren ? $this->guardian_pesantren_name : null,
                        'job' => $guardianJob,
                        'income' => $this->guardian_income,
                        'phone_number' => $this->guardian_phone,
                    ]);
                }

                // 5. Create Registration
                Registration::create([
                    'student_id' => $student->id,
                    'previous_school_level' => $this->previous_school_level,
                    'previous_school_name' => $this->previous_school_name,
                    'previous_school_npsn' => $this->previous_school_npsn,
                    'previous_school_address' => $this->previous_school_address,
                    'destination_institution_id' => $this->destination_institution_id,
                    'destination_class' => $this->destination_class,
                    'academic_year_id' => $activeYear->id,
                    'funding_source' => $this->funding_source,
                ]);

                // 6. Generate bills after registration is created (so destination_institution is available)
                $student->refresh();
                $student->generateBills();

                // 7. Upload Documents
                $documents = [
                    'kk' => $this->kk_file,
                    'akta' => $this->akta_file,
                    'ktp_ayah' => $this->ktp_ayah_file,
                    'ktp_ibu' => $this->ktp_ibu_file,
                    'ktp_wali' => $this->wali_type === 'other' ? $this->ktp_wali_file : null,
                    'ijazah' => $this->ijazah_file,
                    'nisn' => $this->nisn_file,
                    'kip' => $this->kip_file,
                ];

                foreach ($documents as $type => $file) {
                    if ($file) {
                        $path = $file->store('student-documents', 'local');
                        StudentDocument::create([
                            'student_id' => $student->id,
                            'type' => $type,
                            'file_path' => $path,
                            'status' => 'pending',
                        ]);
                    }
                }

                // 8. Broadcast event for real-time dashboard update
                event(new StudentRegistered($student));

                // Prepare success data
                $chosenSchool = Institution::find($this->destination_institution_id);
                $this->successData = [
                    'registration_number' => $student->registration_number,
                    'student_id' => $student->id,
                    'nik' => $student->nik,
                    'name' => $student->full_name,
                    'school' => $chosenSchool ? $chosenSchool->name : '-',
                    'class' => $this->destination_class,
                ];
                $this->isSuccess = true;
            });
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Registration submission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Show user-friendly error message
            $this->addError('global', 'Terjadi kesalahan saat menyimpan data pendaftaran. Silakan coba lagi atau hubungi admin.');
            return;
        }
    }

    protected function generateBills(Student $student)
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear)
            return;

        $formalInstitution = Institution::find($this->destination_institution_id);
        $formalInstitutionId = $formalInstitution?->id;
        $formalInstitutionType = $formalInstitution?->type;

        // Collect all fee components
        $allFees = collect();

        // Loop 1: Mandatory (Pondok & Madrasah Dar Al Tauhid)
        // Exclude destination_institution if it's already pondok/madrasah to avoid duplicates
        $mandatoryFees = FeeComponent::where('academic_year_id', $activeYear->id)
            ->whereHas('institution', function ($query) use ($formalInstitutionId, $formalInstitutionType) {
                $query->whereIn('type', ['pondok', 'madrasah']);
                // If destination is pondok/madrasah, exclude it from mandatory loop (will be added in loop 2)
                if (in_array($formalInstitutionType, ['pondok', 'madrasah'])) {
                    $query->where('id', '!=', $formalInstitutionId);
                }
            })
            ->with('institution')
            ->get();

        $allFees = $allFees->merge($mandatoryFees);

        // Loop 2: Formal School Choice (includes destination if pondok/madrasah)
        if ($formalInstitution && $formalInstitution->type !== 'mts_external') {
            $formalFees = FeeComponent::where('academic_year_id', $activeYear->id)
                ->where('institution_id', $formalInstitution->id)
                ->with('institution')
                ->get();

            $allFees = $allFees->merge($formalFees);
        }

        // Create single aggregated bill if there are fees
        if ($allFees->isNotEmpty()) {
            $totalAmount = $allFees->sum('amount');

            // Build description from components
            $descriptions = $allFees->map(function ($fee) {
                return $fee->institution->name . ' - ' . $fee->name . ': Rp ' . number_format($fee->amount, 0, ',', '.');
            })->implode("\n");

            Bill::create([
                'student_id' => $student->id,
                'amount' => $totalAmount,
                'remaining_amount' => $totalAmount,
                'status' => 'unpaid',
                'description' => $descriptions,
            ]);
        }
    }

    /**
     * Send WhatsApp message to a specific recipient via Fonnte API
     */
    public function sendWhatsAppTo(string $recipient): void
    {
        $fonnteService = app(\App\Services\FonnteService::class);

        // Determine which phone number to use
        $phone = match ($recipient) {
            'father' => $this->father_phone,
            'mother' => $this->mother_phone,
            'guardian' => $this->guardian_phone,
            default => null,
        };

        if (!$phone) {
            session()->flash('wa_sent_status', [
                'success' => false,
                'message' => 'Nomor telepon tidak tersedia.'
            ]);
            return;
        }

        // Build message
        $chosenSchool = Institution::find($this->destination_institution_id);
        $schoolName = $chosenSchool ? $chosenSchool->name : '-';
        $className = $this->destination_class ? "Kelas {$this->destination_class}" : '';

        $message = "Assalamu'alaikum,\n\n";
        $message .= "Pendaftaran santri baru di Pondok Pesantren Dar Al Tauhid telah berhasil.\n\n";
        $message .= "Nomor Pendaftaran: {$this->successData['registration_number']}\n";
        $message .= "Nama Santri: {$this->successData['name']}\n";
        $message .= "Sekolah Tujuan: {$schoolName}";
        if ($className) {
            $message .= " - {$className}";
        }
        $message .= "\n\n";
        $message .= "Silahkan cek status pendaftaran di: " . url('/cek-status') . "\n\n";
        $message .= "Jazakumullahu khairan.";

        // Send via Fonnte API
        $result = $fonnteService->send($phone, $message);

        $recipientName = match ($recipient) {
            'father' => 'Ayah',
            'mother' => 'Ibu',
            'guardian' => 'Wali',
            default => $recipient,
        };

        if ($result['success']) {
            session()->flash('wa_sent_status', [
                'success' => true,
                'message' => "Pesan berhasil dikirim ke {$recipientName}!"
            ]);
        } else {
            session()->flash('wa_sent_status', [
                'success' => false,
                'message' => "Gagal mengirim ke {$recipientName}: {$result['message']}"
            ]);
        }
    }
}
