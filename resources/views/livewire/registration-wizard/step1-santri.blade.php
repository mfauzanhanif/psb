<h3 class="text-lg md:text-xl font-bold text-dat-text mb-6 pb-2 border-b border-gray-100">Data Calon Santri
</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span
                class="text-red-500">*</span></label>
        <input type="text" wire:model="full_name"
            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
        @error('full_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">NIK <span class="text-red-500">*</span></label>
        <input type="text" wire:model="nik" maxlength="16" minlength="16" pattern="[0-9]{16}"
            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)"
            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition"
            placeholder="16 digit NIK">
        @error('nik') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">NISN</label>
        <input type="text" wire:model="nisn" maxlength="10" minlength="10" pattern="[0-9]{10}"
            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition"
            placeholder="10 digit NISN">
        @error('nisn') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir <span
                class="text-red-500">*</span></label>
        <input type="text" wire:model="place_of_birth"
            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
        @error('place_of_birth') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span
                class="text-red-500">*</span></label>
        <input type="date" wire:model="date_of_birth"
            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
        @error('date_of_birth') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span
                class="text-red-500">*</span></label>
        <select wire:model="gender"
            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
            <option value="male">Laki-laki</option>
            <option value="female">Perempuan</option>
        </select>
        @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Anak Ke- <span
                    class="text-red-500">*</span></label>
            <input type="number" wire:model="child_number" min="1"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
            @error('child_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dari ... Bersaudara <span
                    class="text-red-500">*</span></label>
            <input type="number" wire:model="total_siblings" min="1"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
            @error('total_siblings') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="md:col-span-2 mt-4">
        <h4 class="font-semibold text-dat-primary mb-3">Alamat Lengkap</h4>
    </div>

    <!-- Provinsi -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
        <div class="relative">
            <select wire:model.live="province_id"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
                <option value="">-- Pilih Provinsi --</option>
                @foreach($provinces as $key => $province)
                    @if(is_array($province))
                        <option value="{{ $province['code'] }}">{{ $province['name'] }}</option>
                    @else
                        <option value="{{ $key }}">{{ $province }}</option>
                    @endif
                @endforeach
            </select>
            <div wire:loading wire:target="province_id" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin h-5 w-5 text-dat-primary" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </div>
        </div>
        @error('province') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <!-- Kabupaten/Kota -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kabupaten / Kota <span
                class="text-red-500">*</span></label>
        <div class="relative">
            <select wire:model.live="regency_id" {{ empty($regencies) ? 'disabled' : '' }}
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition {{ empty($regencies) ? 'bg-gray-100' : '' }}">
                <option value="">-- Pilih Kabupaten/Kota --</option>
                @foreach($regencies as $key => $regency)
                    @if(is_array($regency))
                        <option value="{{ $regency['code'] }}">{{ $regency['name'] }}</option>
                    @else
                        <option value="{{ $key }}">{{ $regency }}</option>
                    @endif
                @endforeach
            </select>
            <div wire:loading wire:target="regency_id" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin h-5 w-5 text-dat-primary" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </div>
        </div>
        @error('regency') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <!-- Kecamatan -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan <span
                class="text-red-500">*</span></label>
        <div class="relative">
            <select wire:model.live="district_id" {{ empty($districts) ? 'disabled' : '' }}
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition {{ empty($districts) ? 'bg-gray-100' : '' }}">
                <option value="">-- Pilih Kecamatan --</option>
                @foreach($districts as $key => $district)
                    @if(is_array($district))
                        <option value="{{ $district['code'] }}">{{ $district['name'] }}</option>
                    @else
                        <option value="{{ $key }}">{{ $district }}</option>
                    @endif
                @endforeach
            </select>
            <div wire:loading wire:target="district_id" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin h-5 w-5 text-dat-primary" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </div>
        </div>
        @error('district') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <!-- Desa/Kelurahan -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Desa / Kelurahan <span
                class="text-red-500">*</span></label>
        <div class="relative">
            <select wire:model.live="village_id" {{ empty($villages) ? 'disabled' : '' }}
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition {{ empty($villages) ? 'bg-gray-100' : '' }}">
                <option value="">-- Pilih Desa/Kelurahan --</option>
                @foreach($villages as $key => $village)
                    @if(is_array($village))
                        <option value="{{ $village['code'] }}">{{ $village['name'] }}</option>
                    @else
                        <option value="{{ $key }}">{{ $village }}</option>
                    @endif
                @endforeach
            </select>
            <div wire:loading wire:target="village_id" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin h-5 w-5 text-dat-primary" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </div>
        </div>
        @error('village') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <!-- Jalan/Blok/RT/RW -->
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Jalan / Blok / RT / RW <span
                class="text-red-500">*</span></label>
        <textarea wire:model="address_street" rows="2"
            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition"
            placeholder="Contoh: Jl. Merdeka No. 10 RT 01/RW 02"></textarea>
        @error('address_street') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
</div>