<div class="bg-white rounded-xl shadow-xl overflow-hidden border-t-4 border-dat-primary">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-dat-primary to-dat-secondary p-6 md:p-8 text-center text-white">
        <div class="w-20 h-20 bg-white/20 backdrop-blur rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h2 class="text-2xl md:text-3xl font-bold mb-2">Terima Kasih Telah Mendaftar</h2>
        <p class="text-white/90">di Pondok Pesantren Dar Al Tauhid</p>
    </div>

    <div class="p-6 md:p-8">
        {{-- Registration Number Box --}}
        <div class="bg-dat-primary/10 border-2 border-dat-primary rounded-xl p-6 mb-6 text-center">
            <p class="text-sm text-dat-primary mb-1">Nomor Pendaftaran Anda</p>
            <p class="text-4xl md:text-5xl font-bold text-dat-primary tracking-wider mb-2">
                {{ $successData['registration_number'] }}
            </p>
            <p class="text-sm text-gray-600">Simpan nomor ini untuk mengecek status pendaftaran</p>
        </div>

        {{-- Payment Info Box --}}
        <div class="max-w-sm mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-6 font-sans">
            
            {{-- Header Section --}}
            <div class="bg-blue-900 p-6 text-center relative overflow-hidden">
                {{-- Decorative circles --}}
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-blue-800 opacity-50"></div>
                <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 rounded-full bg-blue-700 opacity-30"></div>
                
                <h2 class="text-white text-xl font-bold relative z-10">Informasi Pembayaran</h2>
                <p class="text-blue-100 text-sm mt-1 relative z-10">Silakan transfer ke rekening di bawah ini</p>
            </div>

            {{-- Content Section --}}
            <div class="p-6 space-y-6" x-data="{ copied: false }">
                
                {{-- Kartu Rekening --}}
                <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-xl p-6 text-white shadow-lg relative overflow-hidden transform transition hover:scale-[1.01] duration-300">
                    
                    {{-- Logo BRI Simpel & Icon Wifi --}}
                    <div class="flex justify-between items-start mb-8">
                        <div class="bg-white/10 backdrop-blur-sm px-3 py-1 rounded-lg border border-white/20">
                            <span class="font-bold tracking-wider text-sm">BANK BRI</span>
                        </div>
                        <i class="fas fa-wifi text-white/50 rotate-90"></i>
                    </div>

                    {{-- Nomor Rekening --}}
                    <div class="mb-6">
                        <label class="text-blue-100 text-xs uppercase tracking-wider mb-1 block">Nomor Rekening</label>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-2xl md:text-3xl font-bold tracking-tight text-white drop-shadow-sm">060101000494304</span>
                        </div>
                    </div>

                    {{-- Nama Pemilik --}}
                    <div>
                        <label class="text-blue-100 text-xs uppercase tracking-wider mb-1 block">Atas Nama</label>
                        <p class="font-medium text-lg text-white truncate">YAYASAN DAR AL TAUHID</p>
                    </div>

                    {{-- Background Decoration --}}
                    <div class="absolute -bottom-12 -right-12 w-48 h-48 bg-white opacity-5 rounded-full pointer-events-none"></div>
                </div>

                {{-- Tombol Salin Besar (Action Area) --}}
                <div class="relative group">
                    <button 
                        @click="
                            navigator.clipboard.writeText('060101000494304');
                            copied = true;
                            setTimeout(() => copied = false, 2000);
                        "
                        :class="copied ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-50 hover:bg-blue-50 text-gray-700 hover:text-blue-700 border-gray-200 hover:border-blue-200'"
                        class="w-full border font-semibold py-4 px-4 rounded-xl flex items-center justify-center gap-3 transition-all duration-200 active:scale-95 group shadow-sm">
                        
                        {{-- Icon --}}
                        <span>
                            <i class="text-xl" :class="copied ? 'fas fa-check-circle animate-bounce' : 'far fa-copy'"></i>
                        </span>
                        
                        {{-- Text --}}
                        <span x-text="copied ? 'Berhasil Disalin!' : 'Salin Nomor Rekening'"></span>
                    </button>
                    
                    {{-- Tooltip Feedback (Toast) --}}
                    <div x-show="copied" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-90"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-90"
                        class="absolute left-1/2 transform -translate-x-1/2 -top-12 bg-gray-800 text-white text-xs px-3 py-1.5 rounded-lg whitespace-nowrap shadow-lg z-20">
                        Nomor berhasil disalin!
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 rotate-45 w-2 h-2 bg-gray-800"></div>
                    </div>
                </div>

                {{-- Tombol Konfirmasi WhatsApp --}}
                {{-- Menggunakan data dinamis dari Laravel --}}
                <a href="https://wa.me/6285864737811?text=Assalamualaikum,%20saya%20ingin%20konfirmasi%20pembayaran%20dengan%20Nomor%20Pendaftaran:%20{{ $successData['registration_number'] ?? '' }}%20atas%20nama%20{{ $successData['name'] ?? '' }}" 
                target="_blank" 
                class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3.5 px-4 rounded-xl flex items-center justify-center gap-3 transition-all duration-200 active:scale-95 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                    <i class="fab fa-whatsapp text-2xl"></i>
                    <div class="flex flex-col items-start leading-tight">
                        <span class="text-xs font-medium opacity-90">Konfirmasi Pembayaran</span>
                        <span class="text-base font-bold">0858-6473-7811</span>
                    </div>
                </a>

                {{-- Instruksi Tambahan --}}
                <div class="text-center pt-2">
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i> Mohon simpan bukti transfer Anda untuk konfirmasi.
                    </p>
                </div>

            </div>
        </div>

        {{-- Quick Info --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <span class="block text-xs text-gray-500 uppercase tracking-wide">Nama Santri</span>
                <span class="block text-lg font-bold text-dat-text">{{ $successData['name'] }}</span>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <span class="block text-xs text-gray-500 uppercase tracking-wide">NIK</span>
                <span class="block text-lg font-bold text-dat-text">{{ $successData['nik'] }}</span>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <span class="block text-xs text-gray-500 uppercase tracking-wide">Sekolah Tujuan</span>
                <span class="block text-lg font-bold text-dat-text">{{ $successData['school'] }}</span>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <span class="block text-xs text-gray-500 uppercase tracking-wide">Kelas</span>
                <span
                    class="block text-lg font-bold text-dat-text">{{ $successData['class'] ? 'Kelas ' . $successData['class'] : '-' }}</span>
            </div>
        </div>

        {{-- Data Review Accordion --}}
        <div x-data="{ open: false }" class="mb-6">
            <button @click="open = !open"
                class="w-full flex items-center justify-between bg-gray-100 hover:bg-gray-200 p-4 rounded-lg transition">
                <span class="font-semibold text-dat-text">Lihat Detail Data Pendaftaran</span>
                <svg :class="{ 'rotate-180': open }" class="w-5 h-5 transform transition-transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="open" x-collapse class="mt-4 space-y-4 text-sm">
                {{-- Data Santri --}}
                <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                    <h4 class="font-bold text-dat-primary mb-3">Data Santri</h4>
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
                                {{ $total_siblings }}</strong></p>
                    </div>
                    <div class="mt-3 pt-3 border-t border-green-200">
                        <p class="text-gray-500 mb-1">Alamat:</p>
                        <p><strong>{{ $address_street }}, {{ $village }}, {{ $district }}, {{ $regency }},
                                {{ $province }}</strong></p>
                    </div>
                </div>

                {{-- Data Ayah --}}
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <h4 class="font-bold text-blue-700 mb-3">Data Ayah Kandung</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <p><span class="text-gray-500">Nama:</span> <strong>{{ $father_name }}</strong></p>
                        <p><span class="text-gray-500">Status:</span>
                            <strong>{{ $father_life_status == 'alive' ? 'Masih Hidup' : ($father_life_status == 'deceased' ? 'Sudah Meninggal' : 'Tidak Diketahui') }}</strong>
                        </p>
                        <p><span class="text-gray-500">Pendidikan:</span>
                            <strong>{{ $father_education ?: '-' }}</strong>
                        </p>
                        <p><span class="text-gray-500">Pesantren:</span>
                            <strong>{{ $father_has_pesantren ? $father_pesantren_name : 'Tidak' }}</strong>
                        </p>
                        <p><span class="text-gray-500">Pekerjaan:</span>
                            <strong>{{ $father_job == 'Lainnya' ? $father_job_other : ($father_job ?: '-') }}</strong>
                        </p>
                        <p><span class="text-gray-500">No. WA:</span>
                            <strong>{{ $father_no_whatsapp ? 'Tidak ada' : $father_phone }}</strong>
                        </p>
                    </div>
                </div>

                {{-- Data Ibu --}}
                <div class="bg-pink-50 p-4 rounded-lg border border-pink-100">
                    <h4 class="font-bold text-pink-700 mb-3">Data Ibu Kandung</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <p><span class="text-gray-500">Nama:</span> <strong>{{ $mother_name }}</strong></p>
                        <p><span class="text-gray-500">Status:</span>
                            <strong>{{ $mother_life_status == 'alive' ? 'Masih Hidup' : ($mother_life_status == 'deceased' ? 'Sudah Meninggal' : 'Tidak Diketahui') }}</strong>
                        </p>
                        <p><span class="text-gray-500">Pendidikan:</span>
                            <strong>{{ $mother_education ?: '-' }}</strong>
                        </p>
                        <p><span class="text-gray-500">Pesantren:</span>
                            <strong>{{ $mother_has_pesantren ? $mother_pesantren_name : 'Tidak' }}</strong>
                        </p>
                        <p><span class="text-gray-500">Pekerjaan:</span>
                            <strong>{{ $mother_job == 'Lainnya' ? $mother_job_other : ($mother_job ?: '-') }}</strong>
                        </p>
                        <p><span class="text-gray-500">No. WA:</span>
                            <strong>{{ $mother_no_whatsapp ? 'Tidak ada' : $mother_phone }}</strong>
                        </p>
                    </div>
                </div>

                {{-- Data Wali --}}
                <div class="bg-amber-50 p-4 rounded-lg border border-amber-100">
                    <h4 class="font-bold text-amber-700 mb-3">Data Wali</h4>
                    @if($wali_type == 'father')
                        <p><em>Wali: Ayah Kandung ({{ $father_name }})</em></p>
                    @elseif($wali_type == 'mother')
                        <p><em>Wali: Ibu Kandung ({{ $mother_name }})</em></p>
                    @else
                        <p><em>Wali: {{ $guardian_name }}</em></p>
                    @endif
                </div>

                {{-- Data Sekolah --}}
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
                    <h4 class="font-bold text-purple-700 mb-3">Data Sekolah</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <p><span class="text-gray-500">Sekolah Asal:</span>
                            <strong>{{ $previous_school_name }}</strong>
                        </p>
                        <p><span class="text-gray-500">Jenjang:</span> <strong>{{ $previous_school_level }}</strong>
                        </p>
                        <p><span class="text-gray-500">Sekolah Tujuan:</span>
                            <strong>{{ $successData['school'] }}</strong>
                        </p>
                        <p><span class="text-gray-500">Kelas:</span>
                            <strong>{{ $successData['class'] ? 'Kelas ' . $successData['class'] : '-' }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('registration.pdf', $successData['student_id']) }}" target="_blank"
                class="inline-flex items-center justify-center bg-dat-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-dat-secondary transition shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Download PDF
            </a>
            <a href="/"
                class="inline-flex items-center justify-center bg-dat-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-dat-secondary transition shadow-md">
                Kembali ke Halaman Utama
            </a>
        </div>

        {{-- WhatsApp Send Buttons --}}
        @if(!$father_no_whatsapp && $father_phone || !$mother_no_whatsapp && $mother_phone || ($wali_type === 'other' && !$guardian_no_whatsapp && $guardian_phone))
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-600 mb-4 text-center">Kirim notifikasi WhatsApp ke:</p>
                <div class="flex flex-wrap gap-3 justify-center">
                    {{-- Tombol Kirim ke Ayah --}}
                    @if(!$father_no_whatsapp && $father_phone)
                        <button wire:click="sendWhatsAppTo('father')" wire:loading.attr="disabled"
                            wire:target="sendWhatsAppTo('father')"
                            class="inline-flex items-center bg-green-500 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-green-600 transition disabled:opacity-50 shadow-sm">
                            <span wire:loading.remove wire:target="sendWhatsAppTo('father')">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                </svg>
                                Kirim ke Ayah
                            </span>
                            <span wire:loading wire:target="sendWhatsAppTo('father')" class="flex items-center">
                                <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                    </circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Mengirim...
                            </span>
                        </button>
                    @endif

                    {{-- Tombol Kirim ke Ibu --}}
                    @if(!$mother_no_whatsapp && $mother_phone)
                        <button wire:click="sendWhatsAppTo('mother')" wire:loading.attr="disabled"
                            wire:target="sendWhatsAppTo('mother')"
                            class="inline-flex items-center bg-green-500 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-green-600 transition disabled:opacity-50 shadow-sm">
                            <span wire:loading.remove wire:target="sendWhatsAppTo('mother')">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                </svg>
                                Kirim ke Ibu
                            </span>
                            <span wire:loading wire:target="sendWhatsAppTo('mother')" class="flex items-center">
                                <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                    </circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Mengirim...
                            </span>
                        </button>
                    @endif

                    {{-- Tombol Kirim ke Wali --}}
                    @if($wali_type === 'other' && !$guardian_no_whatsapp && $guardian_phone)
                        <button wire:click="sendWhatsAppTo('guardian')" wire:loading.attr="disabled"
                            wire:target="sendWhatsAppTo('guardian')"
                            class="inline-flex items-center bg-green-500 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-green-600 transition disabled:opacity-50 shadow-sm">
                            <span wire:loading.remove wire:target="sendWhatsAppTo('guardian')">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                </svg>
                                Mengirim...
                            </span>
                        </button>
                    @endif
                </div>

                {{-- Status pesan terkirim --}}
                @if(session('wa_sent_status'))
                    <div
                        class="mt-4 p-3 rounded-lg text-center text-sm {{ session('wa_sent_status.success') ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                        {{ session('wa_sent_status.message') }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>