<div class="mb-8 overflow-x-auto pb-2">
    <div class="flex items-center justify-between relative min-w-[400px]">
        <div class="absolute w-full top-1/2 transform -translate-y-1/2 flex items-center px-4">
            <div class="w-full bg-gray-200 h-1 rounded"></div>
        </div>

        @foreach(range(1, 5) as $step)
            <div class="relative z-10 flex flex-col items-center">
                <div
                    class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 shadow {{ $currentStep >= $step ? 'bg-dat-primary text-white' : 'bg-white text-gray-400 border-2 border-gray-200' }}">
                    @if($currentStep > $step)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @else
                        {{ $step }}
                    @endif
                </div>
                <span
                    class="mt-2 text-xs font-medium whitespace-nowrap {{ $currentStep >= $step ? 'text-dat-primary' : 'text-gray-400' }}">
                    @if($step == 1) Santri
                    @elseif($step == 2) Orang Tua
                    @elseif($step == 3) Sekolah
                    @elseif($step == 4) Dokumen
                    @elseif($step == 5) Review
                    @endif
                </span>
            </div>
        @endforeach
    </div>
</div>