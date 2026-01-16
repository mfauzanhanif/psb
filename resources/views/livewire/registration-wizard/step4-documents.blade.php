<h3 class="text-lg md:text-xl font-bold text-dat-text mb-6 pb-2 border-b border-gray-100">Upload Dokumen
</h3>
<p class="text-sm text-gray-500 mb-4">Format yang diperbolehkan: JPG, PNG, PDF. Maksimal 5MB per file.</p>

{{-- Phone Validation Error --}}
@error('phone_required')
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

@if(count($fileSizeErrors) > 0)
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                </path>
            </svg>
            <p class="text-sm text-red-700 font-medium">File yang diupload melebihi batas maksimal 5MB. Harap
                upload ulang file yang lebih kecil.</p>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
    {{-- KK Upload --}}
    <label for="kk_file"
        class="border-2 border-dashed border-gray-300 rounded-xl p-4 md:p-6 flex flex-col items-center justify-center bg-gray-50 hover:bg-green-50 hover:border-dat-primary transition cursor-pointer">
        <div class="w-12 h-12 bg-dat-primary/10 rounded-full flex items-center justify-center mb-3 pointer-events-none">
            <svg class="w-6 h-6 text-dat-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
        </div>
        <span class="block text-sm font-medium text-gray-700 mb-2 text-center pointer-events-none">Kartu Keluarga (KK)
            <span class="text-red-500">*</span></span>
        <span class="text-xs text-gray-500 mb-2 pointer-events-none">Klik untuk memilih file</span>
        <input type="file" wire:model="kk_file" id="kk_file" class="hidden">
        <div wire:loading wire:target="kk_file" class="mt-2 text-xs text-dat-primary">Mengupload...</div>
        @error('kk_file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        @if($kk_file) <span class="text-dat-primary text-xs mt-2">✓ {{ $kk_file->getClientOriginalName() }}</span>
        @endif
    </label>

    {{-- Akta Upload --}}
    <label for="akta_file"
        class="border-2 border-dashed border-gray-300 rounded-xl p-4 md:p-6 flex flex-col items-center justify-center bg-gray-50 hover:bg-green-50 hover:border-dat-primary transition cursor-pointer">
        <div class="w-12 h-12 bg-dat-primary/10 rounded-full flex items-center justify-center mb-3 pointer-events-none">
            <svg class="w-6 h-6 text-dat-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
        </div>
        <span class="block text-sm font-medium text-gray-700 mb-2 text-center pointer-events-none">Akta Kelahiran <span
                class="text-red-500">*</span></span>
        <span class="text-xs text-gray-500 mb-2 pointer-events-none">Klik untuk memilih file</span>
        <input type="file" wire:model="akta_file" id="akta_file" class="hidden">
        <div wire:loading wire:target="akta_file" class="mt-2 text-xs text-dat-primary">Mengupload...</div>
        @error('akta_file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        @if($akta_file) <span class="text-dat-primary text-xs mt-2">✓ {{ $akta_file->getClientOriginalName() }}</span>
        @endif
    </label>

    {{-- KTP Ayah Upload --}}
    <label for="ktp_ayah_file"
        class="border-2 border-dashed border-gray-300 rounded-xl p-4 md:p-6 flex flex-col items-center justify-center bg-gray-50 hover:bg-gray-100 hover:border-gray-400 transition cursor-pointer">
        <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mb-3 pointer-events-none">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884.896 1.6 2 1.6 1.104 0 2-.716 2-1.6M12 12v3m0 0l-2-2m2 2l2-2">
                </path>
            </svg>
        </div>
        <span class="block text-sm font-medium text-gray-700 mb-2 text-center pointer-events-none">KTP Ayah</span>
        <span class="text-xs text-gray-500 mb-2 pointer-events-none">Klik untuk memilih file</span>
        <input type="file" wire:model="ktp_ayah_file" id="ktp_ayah_file" class="hidden">
        <div wire:loading wire:target="ktp_ayah_file" class="mt-2 text-xs text-gray-500">Mengupload...</div>
        @error('ktp_ayah_file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        @if($ktp_ayah_file) <span class="text-dat-primary text-xs mt-2">✓
        {{ $ktp_ayah_file->getClientOriginalName() }}</span> @endif
    </label>

    {{-- KTP Ibu Upload --}}
    <label for="ktp_ibu_file"
        class="border-2 border-dashed border-gray-300 rounded-xl p-4 md:p-6 flex flex-col items-center justify-center bg-gray-50 hover:bg-gray-100 hover:border-gray-400 transition cursor-pointer">
        <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mb-3 pointer-events-none">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884.896 1.6 2 1.6 1.104 0 2-.716 2-1.6M12 12v3m0 0l-2-2m2 2l2-2">
                </path>
            </svg>
        </div>
        <span class="block text-sm font-medium text-gray-700 mb-2 text-center pointer-events-none">KTP Ibu</span>
        <span class="text-xs text-gray-500 mb-2 pointer-events-none">Klik untuk memilih file</span>
        <input type="file" wire:model="ktp_ibu_file" id="ktp_ibu_file" class="hidden">
        <div wire:loading wire:target="ktp_ibu_file" class="mt-2 text-xs text-gray-500">Mengupload...</div>
        @error('ktp_ibu_file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        @if($ktp_ibu_file) <span class="text-dat-primary text-xs mt-2">✓
        {{ $ktp_ibu_file->getClientOriginalName() }}</span> @endif
    </label>

    {{-- KTP Wali Upload - Only if wali_type is 'other' --}}
    @if($wali_type === 'other')
        <label for="ktp_wali_file"
            class="border-2 border-dashed border-gray-300 rounded-xl p-4 md:p-6 flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 hover:border-blue-400 transition cursor-pointer">
            <div class="w-12 h-12 bg-blue-200 rounded-full flex items-center justify-center mb-3 pointer-events-none">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884.896 1.6 2 1.6 1.104 0 2-.716 2-1.6M12 12v3m0 0l-2-2m2 2l2-2">
                    </path>
                </svg>
            </div>
            <span class="block text-sm font-medium text-gray-700 mb-2 text-center pointer-events-none">KTP Wali</span>
            <span class="text-xs text-gray-500 mb-2 pointer-events-none">Klik untuk memilih file</span>
            <input type="file" wire:model="ktp_wali_file" id="ktp_wali_file" class="hidden">
            <div wire:loading wire:target="ktp_wali_file" class="mt-2 text-xs text-blue-500">Mengupload...</div>
            @error('ktp_wali_file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            @if($ktp_wali_file) <span class="text-dat-primary text-xs mt-2">✓
            {{ $ktp_wali_file->getClientOriginalName() }}</span> @endif
        </label>
    @endif

    {{-- Ijazah/SKL Upload (Optional) --}}
    <label for="ijazah_file"
        class="border-2 border-dashed border-gray-300 rounded-xl p-4 md:p-6 flex flex-col items-center justify-center bg-gray-50 hover:bg-gray-100 hover:border-gray-400 transition cursor-pointer">
        <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mb-3 pointer-events-none">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
        </div>
        <span class="block text-sm font-medium text-gray-700 mb-2 text-center pointer-events-none">Ijazah / SKL
            (Opsional)</span>
        <span class="text-xs text-gray-500 mb-2 pointer-events-none">Klik untuk memilih file</span>
        <input type="file" wire:model="ijazah_file" id="ijazah_file" class="hidden">
        <div wire:loading wire:target="ijazah_file" class="mt-2 text-xs text-gray-500">Mengupload...</div>
        @error('ijazah_file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        @if($ijazah_file) <span class="text-dat-primary text-xs mt-2">✓
        {{ $ijazah_file->getClientOriginalName() }}</span> @endif
    </label>

    {{-- NISN Upload (Optional) --}}
    <label for="nisn_file"
        class="border-2 border-dashed border-gray-300 rounded-xl p-4 md:p-6 flex flex-col items-center justify-center bg-gray-50 hover:bg-gray-100 hover:border-gray-400 transition cursor-pointer">
        <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mb-3 pointer-events-none">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 2H7a2 2 0 00-2 2v17a2 2 0 002 2z">
                </path>
            </svg>
        </div>
        <span class="block text-sm font-medium text-gray-700 mb-2 text-center pointer-events-none">Print NISN
            (Opsional)</span>
        <span class="text-xs text-gray-500 mb-2 pointer-events-none">Klik untuk memilih file</span>
        <input type="file" wire:model="nisn_file" id="nisn_file" class="hidden">
        <div wire:loading wire:target="nisn_file" class="mt-2 text-xs text-gray-500">Mengupload...</div>
        @error('nisn_file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        @if($nisn_file) <span class="text-dat-primary text-xs mt-2">✓ {{ $nisn_file->getClientOriginalName() }}</span>
        @endif
    </label>

    {{-- KIP/KIS/PKH Upload (Optional) --}}
    <label for="kip_file"
        class="border-2 border-dashed border-gray-300 rounded-xl p-4 md:p-6 flex flex-col items-center justify-center bg-gray-50 hover:bg-gray-100 hover:border-gray-400 transition cursor-pointer">
        <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mb-3 pointer-events-none">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                </path>
            </svg>
        </div>
        <span class="block text-sm font-medium text-gray-700 mb-2 text-center pointer-events-none">Kartu KIP/KIS/PKH
            (Opsional)</span>
        <span class="text-xs text-gray-500 mb-2 pointer-events-none">Klik untuk memilih file</span>
        <input type="file" wire:model="kip_file" id="kip_file" class="hidden">
        <div wire:loading wire:target="kip_file" class="mt-2 text-xs text-gray-500">Mengupload...</div>
        @error('kip_file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        @if($kip_file) <span class="text-dat-primary text-xs mt-2">✓ {{ $kip_file->getClientOriginalName() }}</span>
        @endif
    </label>
</div>