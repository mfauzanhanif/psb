<!-- Rincian Biaya Section (Pricing Cards) -->
<section id="biaya" class="py-16 bg-gray-50/50 scroll-mt-20" x-data="{ pricingType: 'pendaftaran' }">

    @php
        // Calculate package totals
        $pondokFees = $registrationFees->filter(
            fn($f) => $f->institution && in_array($f->institution->type, ['pondok']),
        );
        $madrasahFees = $registrationFees->filter(
            fn($f) => $f->institution && in_array($f->institution->type, ['madrasah']),
        );
        $smpFees = $registrationFees->filter(fn($f) => $f->institution && $f->institution->type === 'smp');
        $maFees = $registrationFees->filter(fn($f) => $f->institution && $f->institution->type === 'ma');

        // Monthly fees
        $pondokMonthly = $monthlyFees->filter(fn($f) => $f->institution && in_array($f->institution->type, ['pondok']));
        $madrasahMonthly = $monthlyFees->filter(
            fn($f) => $f->institution && in_array($f->institution->type, ['madrasah']),
        );
        $smpMonthly = $monthlyFees->filter(fn($f) => $f->institution && $f->institution->type === 'smp');
        $maMonthly = $monthlyFees->filter(fn($f) => $f->institution && $f->institution->type === 'ma');

        // Package totals - Registration
        $package1Reg = $pondokFees->sum('amount') + $madrasahFees->sum('amount');
        $package2Reg = $package1Reg + $smpFees->sum('amount');
        $package3Reg = $package1Reg + $maFees->sum('amount');

        // Package totals - Monthly
        $package1Monthly = $pondokMonthly->sum('amount') + $madrasahMonthly->sum('amount');
        $package2Monthly = $package1Monthly + $smpMonthly->sum('amount');
        $package3Monthly = $package1Monthly + $maMonthly->sum('amount');

        // Combined fees for packages
        $package1FeesReg = $pondokFees->merge($madrasahFees);
        $package2FeesReg = $pondokFees->merge($madrasahFees)->merge($smpFees);
        $package3FeesReg = $pondokFees->merge($madrasahFees)->merge($maFees);

        $package1FeesMon = $pondokMonthly->merge($madrasahMonthly);
        $package2FeesMon = $pondokMonthly->merge($madrasahMonthly)->merge($smpMonthly);
        $package3FeesMon = $pondokMonthly->merge($madrasahMonthly)->merge($maMonthly);
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-16 animate-on-scroll">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                Rincian Biaya
            </h2>
        </div>

        <!-- Modern Segmented Toggle -->
        <div class="flex justify-center mb-12 animate-on-scroll">
            <div class="bg-white p-1.5 rounded-full border border-gray-200 shadow-sm inline-flex relative">
                <button @click="pricingType = 'pendaftaran'"
                    class="px-8 py-2.5 rounded-full text-sm font-bold transition-all duration-300 relative z-10"
                    :class="pricingType === 'pendaftaran'
                        ?
                        'bg-dat-primary text-white shadow-md' :
                        'text-gray-500 hover:text-gray-900'">
                    Pendaftaran
                </button>
                <button @click="pricingType = 'syahriah'"
                    class="px-8 py-2.5 rounded-full text-sm font-bold transition-all duration-300 relative z-10"
                    :class="pricingType === 'syahriah'
                        ?
                        'bg-dat-primary text-white shadow-md' :
                        'text-gray-500 hover:text-gray-900'">
                    Syahriah
                </button>
            </div>
        </div>

        <!-- Pricing Cards Grid -->
        <div class="grid md:grid-cols-3 gap-6 lg:gap-8 animate-on-scroll">

            <!-- Card 1: Pondok + Madrasah -->
            <div
                class="bg-white rounded-3xl p-6 md:p-10 border border-gray-100 shadow-xl hover:shadow-2xl transition-all duration-300 flex flex-col items-center text-center group relative overflow-hidden">
                <div
                    class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-dat-primary/60 to-dat-secondary/60">
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-6">Pondok & Madrasah</h3>

                <div class="mb-8">
                    <div x-show="pricingType === 'pendaftaran'" x-transition>
                        <span class="text-sm text-gray-500 font-medium block mb-1">Total Biaya</span>
                        <div class="text-4xl font-extrabold text-dat-primary">
                            Rp {{ number_format($package1Reg, 0, ',', '.') }}
                        </div>
                    </div>
                    <div x-show="pricingType === 'syahriah'" x-transition x-cloak>
                        <span class="text-sm text-gray-500 font-medium block mb-1">Total Biaya</span>
                        <div class="text-4xl font-extrabold text-dat-primary">
                            Rp {{ number_format($package1Monthly, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="w-full space-y-4 text-left">
                    <div x-show="pricingType === 'pendaftaran'" x-transition>
                        @forelse($package1FeesReg as $fee)
                            <div class="flex items-end justify-between text-sm py-1">
                                <span class="text-gray-700 font-medium bg-white z-10 pr-2">{{ $fee->name }}</span>
                                <div class="flex-grow border-b border-dashed border-gray-200 mb-1 mx-1"></div>
                                <span class="text-gray-900 font-bold bg-white z-10 pl-2">Rp
                                    {{ number_format($fee->amount, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm text-center py-4">Tidak ada rincian biaya</p>
                        @endforelse
                    </div>
                    <div x-show="pricingType === 'syahriah'" x-transition x-cloak>
                        @forelse($package1FeesMon as $fee)
                            <div class="flex items-end justify-between text-sm py-1">
                                <span class="text-gray-700 font-medium bg-white z-10 pr-2">{{ $fee->name }}</span>
                                <div class="flex-grow border-b border-dashed border-gray-200 mb-1 mx-1"></div>
                                <span class="text-gray-900 font-bold bg-white z-10 pl-2">Rp
                                    {{ number_format($fee->amount, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm text-center py-4">Tidak ada rincian biaya</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Card 2: Pondok + Madrasah + SMP -->
            <div
                class="bg-white rounded-3xl p-6 md:p-10 border border-gray-100 shadow-xl hover:shadow-2xl transition-all duration-300 flex flex-col items-center text-center group relative overflow-hidden">
                <div
                    class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-dat-primary/80 to-dat-secondary/80">
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-6">Pondok + Madrasah + SMP</h3>

                <div class="mb-8">
                    <div x-show="pricingType === 'pendaftaran'" x-transition>
                        <span class="text-sm text-gray-500 font-medium block mb-1">Total Biaya</span>
                        <div class="text-4xl font-extrabold text-dat-primary">
                            Rp {{ number_format($package2Reg, 0, ',', '.') }}
                        </div>
                    </div>
                    <div x-show="pricingType === 'syahriah'" x-transition x-cloak>
                        <span class="text-sm text-gray-500 font-medium block mb-1">Total Biaya</span>
                        <div class="text-4xl font-extrabold text-dat-primary">
                            Rp {{ number_format($package2Monthly, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="w-full space-y-4 text-left">
                    <div x-show="pricingType === 'pendaftaran'" x-transition>
                        @forelse($package2FeesReg as $fee)
                            <div class="flex items-end justify-between text-sm py-1">
                                <span class="text-gray-700 font-medium bg-white z-10 pr-2">{{ $fee->name }}</span>
                                <div class="flex-grow border-b border-dashed border-gray-200 mb-1 mx-1"></div>
                                <span class="text-gray-900 font-bold bg-white z-10 pl-2">Rp
                                    {{ number_format($fee->amount, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm text-center py-4">Tidak ada rincian biaya</p>
                        @endforelse
                    </div>
                    <div x-show="pricingType === 'syahriah'" x-transition x-cloak>
                        @forelse($package2FeesMon as $fee)
                            <div class="flex items-end justify-between text-sm py-1">
                                <span class="text-gray-700 font-medium bg-white z-10 pr-2">{{ $fee->name }}</span>
                                <div class="flex-grow border-b border-dashed border-gray-200 mb-1 mx-1"></div>
                                <span class="text-gray-900 font-bold bg-white z-10 pl-2">Rp
                                    {{ number_format($fee->amount, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm text-center py-4">Tidak ada rincian biaya</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Card 3: Pondok + Madrasah + MA -->
            <div
                class="bg-white rounded-3xl p-6 md:p-10 border border-gray-100 shadow-xl hover:shadow-2xl transition-all duration-300 flex flex-col items-center text-center group relative overflow-hidden">
                <div
                    class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-dat-primary/60 to-dat-secondary/60">
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-6">Pondok + Madrasah + MA</h3>

                <div class="mb-8">
                    <div x-show="pricingType === 'pendaftaran'" x-transition>
                        <span class="text-sm text-gray-500 font-medium block mb-1">Total Biaya</span>
                        <div class="text-4xl font-extrabold text-dat-primary">
                            Rp {{ number_format($package3Reg, 0, ',', '.') }}
                        </div>
                    </div>
                    <div x-show="pricingType === 'syahriah'" x-transition x-cloak>
                        <span class="text-sm text-gray-500 font-medium block mb-1">Total Biaya</span>
                        <div class="text-4xl font-extrabold text-dat-primary">
                            Rp {{ number_format($package3Monthly, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="w-full space-y-4 text-left">
                    <div x-show="pricingType === 'pendaftaran'" x-transition>
                        @forelse($package3FeesReg as $fee)
                            <div class="flex items-end justify-between text-sm py-1">
                                <span class="text-gray-700 font-medium bg-white z-10 pr-2">{{ $fee->name }}</span>
                                <div class="flex-grow border-b border-dashed border-gray-200 mb-1 mx-1"></div>
                                <span class="text-gray-900 font-bold bg-white z-10 pl-2">Rp
                                    {{ number_format($fee->amount, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm text-center py-4">Tidak ada rincian biaya</p>
                        @endforelse
                    </div>
                    <div x-show="pricingType === 'syahriah'" x-transition x-cloak>
                        @forelse($package3FeesMon as $fee)
                            <div class="flex items-end justify-between text-sm py-1">
                                <span class="text-gray-700 font-medium bg-white z-10 pr-2">{{ $fee->name }}</span>
                                <div class="flex-grow border-b border-dashed border-gray-200 mb-1 mx-1"></div>
                                <span class="text-gray-900 font-bold bg-white z-10 pl-2">Rp
                                    {{ number_format($fee->amount, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm text-center py-4">Tidak ada rincian biaya</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
