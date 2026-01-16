<h3 class="text-lg md:text-xl font-bold text-dat-text mb-6 pb-2 border-b border-gray-100">Data Sekolah</h3>

<!-- DATA SEKOLAH ASAL (FIRST) -->
<div class="mb-6">
    <h4 class="font-semibold text-dat-primary mb-3 uppercase text-sm tracking-wide">Data Sekolah Asal</h4>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-gray-50 p-4 rounded-lg">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jenjang Sekolah Asal <span
                    class="text-red-500">*</span></label>
            <select wire:model="previous_school_level"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
                <option value="">-- Pilih --</option>
                <option value="SD/Sederajat">SD/Sederajat</option>
                <option value="SMP/Sederajat">SMP/Sederajat</option>
                <option value="SMA/Sederajat">SMA/Sederajat</option>
            </select>
            @error('previous_school_level') <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Sekolah Asal <span
                    class="text-red-500">*</span></label>
            <input type="text" wire:model="previous_school_name"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
            @error('previous_school_name') <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">NPSN (Opsional)</label>
            <input type="number" wire:model="previous_school_npsn"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Sekolah Asal <span
                    class="text-red-500">*</span></label>
            <textarea wire:model="previous_school_address" rows="2"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition"></textarea>
            @error('previous_school_address') <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<!-- SEKOLAH TUJUAN (SECOND) -->
<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Sekolah Formal Tujuan <span
            class="text-red-500">*</span></label>
    <select wire:model.live="destination_institution_id"
        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border text-base md:text-lg transition">
        <option value="">-- Pilih Sekolah --</option>
        @foreach($institutions as $inst)
            <option value="{{ $inst->id }}">{{ $inst->name }}</option>
        @endforeach
    </select>
    @error('destination_institution_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

    {{-- Class Selection - Dynamic based on selected school --}}
    <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Kelas <span
                class="text-red-500">*</span></label>
        <select wire:model.live="destination_class"
            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
            <option value="">-- Pilih Kelas --</option>
            @php
                $selectedInstitution = $institutions->firstWhere('id', $destination_institution_id);
                $institutionType = $selectedInstitution ? $selectedInstitution->type : null;
            @endphp
            @if(!$institutionType || in_array($institutionType, ['smp', 'mts']))
                <option value="7">Kelas 7 (SMP/MTs)</option>
                <option value="8">Kelas 8 (SMP/MTs)</option>
                <option value="9">Kelas 9 (SMP/MTs)</option>
            @endif
            @if(!$institutionType || $institutionType === 'ma')
                <option value="10">Kelas 10 (MA)</option>
                <option value="11">Kelas 11 (MA)</option>
                <option value="12">Kelas 12 (MA)</option>
            @endif
        </select>
        @error('destination_class') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    @if($estimatedFees > 0)
        <div class="mt-3 p-4 bg-green-50 text-dat-primary rounded-lg border border-green-200">
            <span class="font-semibold">Estimasi Total Biaya Awal:</span>
            <span class="text-xl md:text-2xl font-bold ml-2">Rp
                {{ number_format($estimatedFees, 0, ',', '.') }}</span>
            <p class="text-xs mt-1 text-green-600">*Biaya mencakup Pendaftaran Pondok, Madrasah Dar Al Tauhid,
                dan Sekolah Formal Pilihan.</p>
        </div>
    @endif
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Siapa yang membiayai? <span
            class="text-red-500">*</span></label>
    <select wire:model="funding_source"
        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-dat-primary focus:border-dat-primary p-3 border transition">
        <option value="">-- Pilih --</option>
        <option value="Orang Tua">Orang Tua</option>
        <option value="Wali">Wali</option>
        <option value="Sendiri">Ditanggung Sendiri</option>
        <option value="Lainnya">Lainnya</option>
    </select>
    @error('funding_source') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>