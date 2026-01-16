<div class="bg-gray-50 px-4 sm:px-6 md:px-8 py-4 flex justify-between items-center border-t">
    @if($currentStep > 1)
        <button wire:click="decreaseStep" @click="window.scrollTo({top: 0, behavior: 'smooth'})" wire:loading.attr="disabled"
            class="flex items-center bg-white border border-gray-300 text-gray-700 px-4 md:px-5 py-2.5 rounded-lg font-medium hover:bg-gray-100 transition disabled:opacity-50">
            <svg class="w-4 h-4 mr-1 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="hidden sm:inline">Kembali</span>
        </button>
    @else
        <div></div>
    @endif

    @if($currentStep < $totalSteps)
        <button wire:click="increaseStep" @click="window.scrollTo({top: 0, behavior: 'smooth'})" wire:loading.attr="disabled" wire:target="increaseStep"
            class="flex items-center bg-dat-primary text-white px-5 md:px-6 py-2.5 rounded-lg font-medium hover:bg-dat-secondary transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
            <span wire:loading.remove wire:target="increaseStep">
                Lanjut
                <svg class="w-4 h-4 ml-1 md:ml-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </span>
            <span wire:loading wire:target="increaseStep" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Memproses...
            </span>
        </button>
    @else
        <button wire:click="submit" @click="window.scrollTo({top: 0, behavior: 'smooth'})" wire:loading.attr="disabled" wire:target="submit"
            class="flex items-center bg-dat-primary text-white px-5 md:px-6 py-2.5 rounded-lg font-medium hover:bg-dat-secondary transition shadow-md disabled:opacity-50">
            <span wire:loading.remove wire:target="submit">
                <svg class="w-4 h-4 mr-1 md:mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Kirim Pendaftaran
            </span>
            <span wire:loading wire:target="submit" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
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
</div>