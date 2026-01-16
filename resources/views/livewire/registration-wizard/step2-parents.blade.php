<h3 class="text-lg md:text-xl font-bold text-dat-text mb-6 pb-2 border-b border-gray-100">Data Orang Tua /
    Wali</h3>

<!-- AYAH KANDUNG -->
<div class="mb-8">
    <h4 class="font-semibold text-dat-primary mb-3 uppercase text-sm tracking-wide flex items-center">
        <span
            class="w-7 h-7 bg-dat-primary text-white rounded-full flex items-center justify-center text-xs mr-2">1</span>
        Data Ayah Kandung
    </h4>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ayah Kandung <span
                    class="text-red-500">*</span></label>
            <input type="text" wire:model="father_name"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
            @error('father_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status <span
                    class="text-red-500">*</span></label>
            <select wire:model="father_life_status"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
                <option value="alive">Masih Hidup</option>
                <option value="deceased">Sudah Meninggal</option>
                <option value="unknown">Tidak Diketahui</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
            <input type="text" wire:model="father_nik" maxlength="16" pattern="[0-9]{16}"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition"
                placeholder="16 digit NIK">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
            <input type="text" wire:model="father_place_of_birth"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
            <input type="date" wire:model="father_date_of_birth"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
            <select wire:model="father_education"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
                <option value="">-- Pilih Pendidikan --</option>
                <option value="Tidak Sekolah">Tidak Sekolah</option>
                <option value="SD/Sederajat">SD/Sederajat</option>
                <option value="SMP/Sederajat">SMP/Sederajat</option>
                <option value="SMA/Sederajat">SMA/Sederajat</option>
                <option value="D1">D1</option>
                <option value="D2">D2</option>
                <option value="D3">D3</option>
                <option value="S1">S1</option>
                <option value="S2">S2</option>
                <option value="S3">S3</option>
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="flex items-center space-x-2 mb-2">
                <input type="checkbox" wire:model.live="father_has_pesantren"
                    class="rounded border-gray-300 text-dat-primary focus:ring-dat-primary">
                <span class="text-sm font-medium text-gray-700">Ada Pendidikan Pesantren?</span>
            </label>
            @if($father_has_pesantren)
                <input type="text" wire:model="father_pesantren_name"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition"
                    placeholder="Nama Pesantren">
            @endif
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
            <select wire:model.live="father_job"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
                <option value="">-- Pilih Pekerjaan --</option>
                <option value="PNS">PNS</option>
                <option value="TNI/Polri">TNI/Polri</option>
                <option value="Karyawan Swasta">Karyawan Swasta</option>
                <option value="Wiraswasta">Wiraswasta</option>
                <option value="Petani">Petani</option>
                <option value="Buruh">Buruh</option>
                <option value="Pedagang">Pedagang</option>
                <option value="Guru/Dosen">Guru/Dosen</option>
                <option value="Dokter">Dokter</option>
                <option value="Tidak Bekerja">Tidak Bekerja</option>
                <option value="Lainnya">Lainnya</option>
            </select>
            @if($father_job === 'Lainnya')
                <input type="text" wire:model="father_job_other"
                    class="mt-2 w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition"
                    placeholder="Sebutkan pekerjaan">
            @endif
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rata-rata Penghasilan</label>
            <select wire:model="father_income"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
                <option value="">-- Pilih Penghasilan --</option>
                <option value="< 1 Juta">
                    < 1 Juta</option>
                <option value="1 - 3 Juta">1 - 3 Juta</option>
                <option value="3 - 5 Juta">3 - 5 Juta</option>
                <option value="5 - 10 Juta">5 - 10 Juta</option>
                <option value="> 10 Juta">> 10 Juta</option>
            </select>
        </div>
        <div>
            <label class="flex items-center space-x-2 mb-2">
                <input type="checkbox" wire:model.live="father_no_whatsapp"
                    class="rounded border-gray-300 text-dat-primary focus:ring-dat-primary">
                <span class="text-sm text-gray-600">Tidak memiliki nomor WhatsApp</span>
            </label>
            <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp
                @if(!$father_no_whatsapp)<span class="text-red-500">*</span>@endif
            </label>
            <input type="text" wire:model="father_phone" {{ $father_no_whatsapp ? 'disabled' : '' }}
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition {{ $father_no_whatsapp ? 'bg-gray-100' : '' }}"
                placeholder="08xxxxxxxxxx">
            @error('father_phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<!-- IBU KANDUNG -->
<div class="mb-8">
    <h4 class="font-semibold text-dat-primary mb-3 uppercase text-sm tracking-wide flex items-center">
        <span
            class="w-7 h-7 bg-dat-primary text-white rounded-full flex items-center justify-center text-xs mr-2">2</span>
        Data Ibu Kandung
    </h4>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu Kandung <span
                    class="text-red-500">*</span></label>
            <input type="text" wire:model="mother_name"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
            @error('mother_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status <span
                    class="text-red-500">*</span></label>
            <select wire:model="mother_life_status"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
                <option value="alive">Masih Hidup</option>
                <option value="deceased">Sudah Meninggal</option>
                <option value="unknown">Tidak Diketahui</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
            <input type="text" wire:model="mother_nik" maxlength="16" pattern="[0-9]{16}"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition"
                placeholder="16 digit NIK">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
            <input type="text" wire:model="mother_place_of_birth"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
            <input type="date" wire:model="mother_date_of_birth"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
            <select wire:model="mother_education"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
                <option value="">-- Pilih Pendidikan --</option>
                <option value="Tidak Sekolah">Tidak Sekolah</option>
                <option value="SD/Sederajat">SD/Sederajat</option>
                <option value="SMP/Sederajat">SMP/Sederajat</option>
                <option value="SMA/Sederajat">SMA/Sederajat</option>
                <option value="D1">D1</option>
                <option value="D2">D2</option>
                <option value="D3">D3</option>
                <option value="S1">S1</option>
                <option value="S2">S2</option>
                <option value="S3">S3</option>
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="flex items-center space-x-2 mb-2">
                <input type="checkbox" wire:model.live="mother_has_pesantren"
                    class="rounded border-gray-300 text-dat-primary focus:ring-dat-primary">
                <span class="text-sm font-medium text-gray-700">Ada Pendidikan Pesantren?</span>
            </label>
            @if($mother_has_pesantren)
                <input type="text" wire:model="mother_pesantren_name"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition"
                    placeholder="Nama Pesantren">
            @endif
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
            <select wire:model.live="mother_job"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
                <option value="">-- Pilih Pekerjaan --</option>
                <option value="PNS">PNS</option>
                <option value="TNI/Polri">TNI/Polri</option>
                <option value="Karyawan Swasta">Karyawan Swasta</option>
                <option value="Wiraswasta">Wiraswasta</option>
                <option value="Petani">Petani</option>
                <option value="Buruh">Buruh</option>
                <option value="Pedagang">Pedagang</option>
                <option value="Guru/Dosen">Guru/Dosen</option>
                <option value="Dokter">Dokter</option>
                <option value="Tidak Bekerja">Tidak Bekerja</option>
                <option value="Ibu Rumah Tangga">Ibu Rumah Tangga</option>
                <option value="Lainnya">Lainnya</option>
            </select>
            @if($mother_job === 'Lainnya')
                <input type="text" wire:model="mother_job_other"
                    class="mt-2 w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition"
                    placeholder="Sebutkan pekerjaan">
            @endif
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rata-rata Penghasilan</label>
            <select wire:model="mother_income"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
                <option value="">-- Pilih Penghasilan --</option>
                <option value="< 1 Juta">
                    < 1 Juta</option>
                <option value="1 - 3 Juta">1 - 3 Juta</option>
                <option value="3 - 5 Juta">3 - 5 Juta</option>
                <option value="5 - 10 Juta">5 - 10 Juta</option>
                <option value="> 10 Juta">> 10 Juta</option>
            </select>
        </div>
        <div>
            <label class="flex items-center space-x-2 mb-2">
                <input type="checkbox" wire:model.live="mother_no_whatsapp"
                    class="rounded border-gray-300 text-dat-primary focus:ring-dat-primary">
                <span class="text-sm text-gray-600">Tidak memiliki nomor WhatsApp</span>
            </label>
            <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp
                @if(!$mother_no_whatsapp)<span class="text-red-500">*</span>@endif
            </label>
            <input type="text" wire:model="mother_phone" {{ $mother_no_whatsapp ? 'disabled' : '' }}
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition {{ $mother_no_whatsapp ? 'bg-gray-100' : '' }}"
                placeholder="08xxxxxxxxxx">
            @error('mother_phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<!-- WALI SELECTION -->
<div class="mb-6">
    <h4 class="font-semibold text-dat-primary mb-3 uppercase text-sm tracking-wide flex items-center">
        <span
            class="w-7 h-7 bg-dat-primary text-white rounded-full flex items-center justify-center text-xs mr-2">3</span>
        Data Wali<span class="text-red-500 ml-1">*</span>
    </h4>
    <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-4 rounded-r-lg">
        <p class="text-sm text-amber-800">Jika lainnya, harap tunggu sampai formulir untuk wali muncul</p>
    </div>
    <div class="flex flex-wrap gap-4">
        <label class="flex items-center space-x-2 cursor-pointer">
            <input type="radio" name="wali_type" wire:model.live="wali_type" value="father"
                class="text-dat-primary focus:ring-dat-primary">
            <span class="text-sm font-medium text-gray-700">Ayah Kandung</span>
        </label>
        <label class="flex items-center space-x-2 cursor-pointer">
            <input type="radio" name="wali_type" wire:model.live="wali_type" value="mother"
                class="text-dat-primary focus:ring-dat-primary">
            <span class="text-sm font-medium text-gray-700">Ibu Kandung</span>
        </label>
        <label class="flex items-center space-x-2 cursor-pointer">
            <input type="radio" name="wali_type" wire:model.live="wali_type" value="other"
                class="text-dat-primary focus:ring-dat-primary">
            <span class="text-sm font-medium text-gray-700">Lainnya</span>
        </label>
    </div>
    @error('wali_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>

<!-- GUARDIAN FORM (only if wali_type is 'other') -->
@if($wali_type === 'other')
    <div class="mb-6">
        <h4 class="font-semibold text-gray-600 mb-3 uppercase text-sm tracking-wide">Data Wali Lainnya</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-blue-50 p-4 rounded-lg border border-blue-200">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Wali <span
                        class="text-red-500">*</span></label>
                <input type="text" wire:model="guardian_name"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
                @error('guardian_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model="guardian_life_status"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
                    <option value="alive">Masih Hidup</option>
                    <option value="deceased">Sudah Meninggal</option>
                    <option value="unknown">Tidak Diketahui</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                <input type="text" wire:model="guardian_nik" maxlength="16" pattern="[0-9]{16}"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition"
                    placeholder="16 digit NIK">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                <input type="text" wire:model="guardian_place_of_birth"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                <input type="date" wire:model="guardian_date_of_birth"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
                <select wire:model="guardian_education"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
                    <option value="">-- Pilih Pendidikan --</option>
                    <option value="Tidak Sekolah">Tidak Sekolah</option>
                    <option value="SD/Sederajat">SD/Sederajat</option>
                    <option value="SMP/Sederajat">SMP/Sederajat</option>
                    <option value="SMA/Sederajat">SMA/Sederajat</option>
                    <option value="D1">D1</option>
                    <option value="D2">D2</option>
                    <option value="D3">D3</option>
                    <option value="S1">S1</option>
                    <option value="S2">S2</option>
                    <option value="S3">S3</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="flex items-center space-x-2 mb-2">
                    <input type="checkbox" wire:model.live="guardian_has_pesantren"
                        class="rounded border-gray-300 text-dat-primary focus:ring-dat-primary">
                    <span class="text-sm font-medium text-gray-700">Ada Pendidikan Pesantren?</span>
                </label>
                @if($guardian_has_pesantren)
                    <input type="text" wire:model="guardian_pesantren_name"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition"
                        placeholder="Nama Pesantren">
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                <select wire:model.live="guardian_job"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
                    <option value="">-- Pilih Pekerjaan --</option>
                    <option value="PNS">PNS</option>
                    <option value="TNI/Polri">TNI/Polri</option>
                    <option value="Karyawan Swasta">Karyawan Swasta</option>
                    <option value="Wiraswasta">Wiraswasta</option>
                    <option value="Petani">Petani</option>
                    <option value="Buruh">Buruh</option>
                    <option value="Pedagang">Pedagang</option>
                    <option value="Guru/Dosen">Guru/Dosen</option>
                    <option value="Dokter">Dokter</option>
                    <option value="Tidak Bekerja">Tidak Bekerja</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
                @if($guardian_job === 'Lainnya')
                    <input type="text" wire:model="guardian_job_other"
                        class="mt-2 w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition"
                        placeholder="Sebutkan pekerjaan">
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rata-rata Penghasilan</label>
                <select wire:model="guardian_income"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
                    <option value="">-- Pilih Penghasilan --</option>
                    <option value="< 1 Juta">
                        < 1 Juta</option>
                    <option value="1 - 3 Juta">1 - 3 Juta</option>
                    <option value="3 - 5 Juta">3 - 5 Juta</option>
                    <option value="5 - 10 Juta">5 - 10 Juta</option>
                    <option value="> 10 Juta">> 10 Juta</option>
                </select>
            </div>
            <div>
                <label class="flex items-center space-x-2 mb-2">
                    <input type="checkbox" wire:model.live="guardian_no_whatsapp"
                        class="rounded border-gray-300 text-dat-primary focus:ring-dat-primary">
                    <span class="text-sm text-gray-600">Tidak memiliki nomor WhatsApp</span>
                </label>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp
                    @if(!$guardian_no_whatsapp)<span class="text-red-500">*</span>@endif
                </label>
                <input type="text" wire:model="guardian_phone" {{ $guardian_no_whatsapp ? 'disabled' : '' }}
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition {{ $guardian_no_whatsapp ? 'bg-gray-100' : '' }}"
                    placeholder="08xxxxxxxxxx">
                @error('guardian_phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
@endif