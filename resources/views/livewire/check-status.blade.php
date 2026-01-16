<div class="max-w-4xl mx-auto py-8 md:py-12 px-4 sm:px-6 lg:px-8">

    @if (!$isVerified)
        <!-- Login Form -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden max-w-md mx-auto border-t-4 border-dat-primary">
            <div class="bg-gradient-to-r from-dat-primary to-dat-secondary p-6 text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                </div>
                <h2 class="text-xl md:text-2xl font-bold text-white">Cek Status Santri</h2>
                <p class="text-green-100 mt-2 text-sm md:text-base">Masuk untuk melihat status pendaftaran dan tagihan.
                </p>
            </div>

            <div class="p-6 md:p-8">
                <form wire:submit.prevent="check">
                    <div class="mb-6">
                        <label class="block text-dat-text text-sm font-semibold mb-2" for="reg_number">
                            Nomor Pendaftaran
                        </label>
                        <input wire:model="registration_number"
                            class="shadow-sm border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-dat-primary focus:border-dat-primary transition"
                            id="reg_number" type="text" placeholder="contoh: 230001">
                        @error('registration_number')
                            <span class="text-red-500 text-xs italic mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    @error('login_failed')
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4" role="alert">
                            <span class="block sm:inline">{{ $message }}</span>
                        </div>
                    @enderror

                    <button type="submit" wire:loading.attr="disabled" wire:target="check"
                        class="bg-dat-primary hover:bg-dat-secondary text-white font-bold py-3 px-4 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-dat-primary focus:ring-offset-2 transition duration-150 ease-in-out shadow-md disabled:opacity-50 flex items-center justify-center">
                        <span wire:loading.remove wire:target="check">Cek Status</span>
                        <span wire:loading wire:target="check" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Memeriksa...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    @else
        <!-- Dashboard -->
        <div class="space-y-6">

            <!-- Header -->
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-4 md:p-6 rounded-xl shadow-md border-l-4 border-dat-primary gap-4">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 md:w-14 md:h-14 bg-dat-primary/10 rounded-full flex items-center justify-center mr-3 md:mr-4 flex-shrink-0">
                        <svg class="w-6 h-6 md:w-7 md:h-7 text-dat-primary" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-lg md:text-2xl font-bold text-dat-text truncate">{{ $student->full_name }}</h2>
                        <p class="text-xs md:text-sm text-gray-500 truncate">{{ $student->registration_number }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2 md:space-x-3 w-full sm:w-auto justify-between sm:justify-end">
                    @php
                        $statusColors = [
                            'draft' => 'bg-gray-100 text-gray-700 border-gray-300',
                            'verified' => 'bg-amber-50 text-amber-700 border-amber-300',
                            'accepted' => 'bg-green-50 text-green-700 border-green-300',
                            'rejected' => 'bg-red-50 text-red-700 border-red-300',
                        ];
                        $statusLabel = [
                            'draft' => 'Menunggu',
                            'verified' => 'Terverifikasi',
                            'accepted' => 'Diterima',
                            'rejected' => 'Ditolak',
                        ];
                    @endphp
                    <span
                        class="px-3 py-1.5 inline-flex text-xs md:text-sm leading-5 font-semibold rounded-full border {{ $statusColors[$student->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $statusLabel[$student->status] ?? ucfirst($student->status) }}
                    </span>
                    <button wire:click="logout" class="text-gray-400 hover:text-red-500 p-2 transition" title="Keluar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="bg-green-50 border border-green-200 text-dat-primary px-4 py-3 rounded-lg flex items-center"
                    role="alert">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ session('message') }}</span>
                </div>
            @endif

            <!-- Tabs Navigation -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex" aria-label="Tabs">
                        <button wire:click="$set('activeTab', 'biodata')"
                            class="{{ $activeTab === 'biodata' ? 'border-dat-primary text-dat-primary bg-green-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }} flex-1 whitespace-nowrap py-3 md:py-4 px-1 border-b-2 font-medium text-xs md:text-sm transition">
                            <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="hidden sm:inline">Biodata</span>
                        </button>
                        <button wire:click="$set('activeTab', 'finance')"
                            class="{{ $activeTab === 'finance' ? 'border-dat-primary text-dat-primary bg-green-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }} flex-1 whitespace-nowrap py-3 md:py-4 px-1 border-b-2 font-medium text-xs md:text-sm transition">
                            <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <span class="hidden sm:inline">Keuangan</span>
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-4 md:p-6">
                    @if ($activeTab === 'biodata')
                        @include('livewire.check-status.biodata-tab')
                    @endif

                    @if ($activeTab === 'finance')
                        @include('livewire.check-status.payment-tab')
                    @endif
                </div>
            </div>
        </div>

    @endif

</div>
