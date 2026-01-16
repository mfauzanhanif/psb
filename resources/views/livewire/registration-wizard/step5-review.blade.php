<h3 class="text-lg md:text-xl font-bold text-dat-text mb-6 pb-2 border-b border-gray-100">Review Data
    Pendaftaran</h3>

{{-- Global Error Messages --}}
@error('global')
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                </path>
            </svg>
            <p class="text-sm text-red-700 font-medium">{{ $message }}</p>
        </div>
    </div>
@enderror

{{-- Registration Code Preview --}}
<div class="bg-dat-primary/10 border-2 border-dat-primary rounded-xl p-4 mb-6 text-center">
    <p class="text-sm text-dat-primary mb-1">Preview Kode Pendaftaran</p>
    <p class="text-3xl font-bold text-dat-primary tracking-wider">
        @php
            $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
            $yearPrefix = $activeYear ? substr(explode('/', $activeYear->name)[0], -2) : substr(date('Y'), -2);
            $lastStudent = \App\Models\Student::where('registration_number', 'like', "{$yearPrefix}%")
                ->where('registration_number', 'regexp', '^[0-9]{6}$')
                ->orderByRaw('CAST(registration_number AS UNSIGNED) DESC')
                ->first();
            $nextNumber = $lastStudent ? ((int) substr($lastStudent->registration_number, 2)) + 1 : 1;
            $previewCode = $yearPrefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        @endphp
        {{ $previewCode }}
    </p>
</div>

<div class="space-y-4 text-sm">
    {{-- Data Santri --}}
    <div class="bg-green-50 p-4 rounded-lg border border-green-100">
        <h4 class="font-bold text-dat-primary mb-3 flex items-center text-base">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Data Santri
        </h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <p><span class="text-gray-500">Nama:</span> <strong>{{ $full_name }}</strong></p>
            <p><span class="text-gray-500">NIK:</span> <strong>{{ $nik }}</strong></p>
            <p><span class="text-gray-500">NISN:</span> <strong>{{ $nisn ?: '-' }}</strong></p>
            <p><span class="text-gray-500">TTL:</span> <strong>{{ $place_of_birth }},
                    {{ \Carbon\Carbon::parse($date_of_birth)->format('d M Y') }}</strong></p>
            <p><span class="text-gray-500">Jenis Kelamin:</span>
                <strong>{{ $gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</strong>
            </p>
            <p><span class="text-gray-500">Anak ke:</span> <strong>{{ $child_number }} dari
                    {{ $total_siblings }} bersaudara</strong></p>
        </div>
        <div class="mt-3 pt-3 border-t border-green-200">
            <p class="text-gray-500 mb-1">Alamat:</p>
            <p><strong>{{ $address_street }}</strong></p>
            <p>{{ $village }}, {{ $district }}, {{ $regency }}, {{ $province }} {{ $postal_code }}</p>
        </div>
    </div>

    {{-- Data Ayah Kandung --}}
    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
        <h4 class="font-bold text-blue-700 mb-3 text-base">Data Ayah Kandung</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <p><span class="text-gray-500">Nama:</span> <strong>{{ $father_name }}</strong></p>
            <p><span class="text-gray-500">Status:</span>
                <strong>{{ $father_life_status == 'alive' ? 'Masih Hidup' : ($father_life_status == 'deceased' ? 'Sudah Meninggal' : 'Tidak Diketahui') }}</strong>
            </p>
            <p><span class="text-gray-500">NIK:</span> <strong>{{ $father_nik ?: '-' }}</strong></p>
            <p><span class="text-gray-500">TTL:</span>
                <strong>{{ $father_place_of_birth ?: '-' }}{{ $father_date_of_birth ? ', ' . \Carbon\Carbon::parse($father_date_of_birth)->format('d M Y') : '' }}</strong>
            </p>
            <p><span class="text-gray-500">Pendidikan:</span> <strong>{{ $father_education ?: '-' }}</strong>
            </p>
            <p><span class="text-gray-500">Pesantren:</span>
                <strong>{{ $father_has_pesantren ? $father_pesantren_name : 'Tidak' }}</strong>
            </p>
            <p><span class="text-gray-500">Pekerjaan:</span>
                <strong>{{ $father_job == 'Lainnya' ? $father_job_other : ($father_job ?: '-') }}</strong>
            </p>
            <p><span class="text-gray-500">Penghasilan:</span> <strong>{{ $father_income ?: '-' }}</strong></p>
            <p><span class="text-gray-500">No. WhatsApp:</span>
                <strong>{{ $father_no_whatsapp ? 'Tidak ada' : ($father_phone ?: '-') }}</strong>
            </p>
        </div>
    </div>

    {{-- Data Ibu Kandung --}}
    <div class="bg-pink-50 p-4 rounded-lg border border-pink-100">
        <h4 class="font-bold text-pink-700 mb-3 text-base">Data Ibu Kandung</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <p><span class="text-gray-500">Nama:</span> <strong>{{ $mother_name }}</strong></p>
            <p><span class="text-gray-500">Status:</span>
                <strong>{{ $mother_life_status == 'alive' ? 'Masih Hidup' : ($mother_life_status == 'deceased' ? 'Sudah Meninggal' : 'Tidak Diketahui') }}</strong>
            </p>
            <p><span class="text-gray-500">NIK:</span> <strong>{{ $mother_nik ?: '-' }}</strong></p>
            <p><span class="text-gray-500">TTL:</span>
                <strong>{{ $mother_place_of_birth ?: '-' }}{{ $mother_date_of_birth ? ', ' . \Carbon\Carbon::parse($mother_date_of_birth)->format('d M Y') : '' }}</strong>
            </p>
            <p><span class="text-gray-500">Pendidikan:</span> <strong>{{ $mother_education ?: '-' }}</strong>
            </p>
            <p><span class="text-gray-500">Pesantren:</span>
                <strong>{{ $mother_has_pesantren ? $mother_pesantren_name : 'Tidak' }}</strong>
            </p>
            <p><span class="text-gray-500">Pekerjaan:</span>
                <strong>{{ $mother_job == 'Lainnya' ? $mother_job_other : ($mother_job ?: '-') }}</strong>
            </p>
            <p><span class="text-gray-500">Penghasilan:</span> <strong>{{ $mother_income ?: '-' }}</strong></p>
            <p><span class="text-gray-500">No. WhatsApp:</span>
                <strong>{{ $mother_no_whatsapp ? 'Tidak ada' : ($mother_phone ?: '-') }}</strong>
            </p>
        </div>
    </div>

    {{-- Data Wali --}}
    <div class="bg-amber-50 p-4 rounded-lg border border-amber-100">
        <h4 class="font-bold text-amber-700 mb-3 text-base">Data Wali</h4>
        @if($wali_type == 'father')
            <p class="text-gray-600 mb-2"><em>Wali: Ayah Kandung</em></p>
            <p><span class="text-gray-500">Nama:</span> <strong>{{ $father_name }}</strong></p>
            <p><span class="text-gray-500">No. WhatsApp:</span>
                <strong>{{ $father_no_whatsapp ? 'Tidak ada' : ($father_phone ?: '-') }}</strong>
            </p>
        @elseif($wali_type == 'mother')
            <p class="text-gray-600 mb-2"><em>Wali: Ibu Kandung</em></p>
            <p><span class="text-gray-500">Nama:</span> <strong>{{ $mother_name }}</strong></p>
            <p><span class="text-gray-500">No. WhatsApp:</span>
                <strong>{{ $mother_no_whatsapp ? 'Tidak ada' : ($mother_phone ?: '-') }}</strong>
            </p>
        @else
            <p class="text-gray-600 mb-2"><em>Wali: Lainnya</em></p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <p><span class="text-gray-500">Nama:</span> <strong>{{ $guardian_name }}</strong></p>
                <p><span class="text-gray-500">Status:</span>
                    <strong>{{ $guardian_life_status == 'alive' ? 'Masih Hidup' : ($guardian_life_status == 'deceased' ? 'Sudah Meninggal' : 'Tidak Diketahui') }}</strong>
                </p>
                <p><span class="text-gray-500">NIK:</span> <strong>{{ $guardian_nik ?: '-' }}</strong></p>
                <p><span class="text-gray-500">TTL:</span>
                    <strong>{{ $guardian_place_of_birth ?: '-' }}{{ $guardian_date_of_birth ? ', ' . \Carbon\Carbon::parse($guardian_date_of_birth)->format('d M Y') : '' }}</strong>
                </p>
                <p><span class="text-gray-500">Pendidikan:</span> <strong>{{ $guardian_education ?: '-' }}</strong>
                </p>
                <p><span class="text-gray-500">Pesantren:</span>
                    <strong>{{ $guardian_has_pesantren ? $guardian_pesantren_name : 'Tidak' }}</strong>
                </p>
                <p><span class="text-gray-500">Pekerjaan:</span>
                    <strong>{{ $guardian_job == 'Lainnya' ? $guardian_job_other : ($guardian_job ?: '-') }}</strong>
                </p>
                <p><span class="text-gray-500">Penghasilan:</span> <strong>{{ $guardian_income ?: '-' }}</strong>
                </p>
                <p><span class="text-gray-500">No. WhatsApp:</span>
                    <strong>{{ $guardian_no_whatsapp ? 'Tidak ada' : ($guardian_phone ?: '-') }}</strong>
                </p>
            </div>
        @endif
    </div>

    {{-- Data Sekolah --}}
    <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
        <h4 class="font-bold text-purple-700 mb-3 text-base">Data Sekolah</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3">
            <p><span class="text-gray-500">Sekolah Asal:</span> <strong>{{ $previous_school_name }}</strong></p>
            <p><span class="text-gray-500">Jenjang:</span> <strong>{{ $previous_school_level }}</strong></p>
            <p><span class="text-gray-500">NPSN:</span> <strong>{{ $previous_school_npsn ?: '-' }}</strong></p>
            <p class="sm:col-span-2"><span class="text-gray-500">Alamat Sekolah:</span>
                <strong>{{ $previous_school_address }}</strong>
            </p>
        </div>
        <div class="pt-3 border-t border-purple-200">
            @php $chosenSchool = $institutions->firstWhere('id', $destination_institution_id); @endphp
            <p><span class="text-gray-500">Sekolah Formal Tujuan:</span>
                <strong>{{ $chosenSchool ? $chosenSchool->name : '-' }}</strong>
                @if($destination_class) - <strong>Kelas {{ $destination_class }}</strong>@endif
            </p>
            <p><span class="text-gray-500">Sumber Pembiayaan:</span> <strong>{{ $funding_source }}</strong></p>
        </div>
    </div>
</div>

<div class="mt-6 p-4 bg-amber-50 rounded-lg border border-amber-200">
    <label class="flex items-start">
        <input type="checkbox" required class="form-checkbox h-5 w-5 text-dat-primary rounded mt-0.5">
        <span class="ml-3 text-sm text-gray-700">Saya menyatakan bahwa data yang saya isi adalah benar dan
            dapat dipertanggungjawabkan.</span>
    </label>
</div>