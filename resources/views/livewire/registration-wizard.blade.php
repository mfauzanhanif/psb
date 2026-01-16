<div class="max-w-4xl mx-auto py-8 px-4">
    @if($isSuccess)
        @include('livewire.registration-wizard.success')
    @else
        @include('livewire.registration-wizard.step-indicator')

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border-t-4 border-dat-primary">
            <div class="p-4 sm:p-6 md:p-8">
                @if($currentStep == 1)
                    @include('livewire.registration-wizard.step1-santri')
                @elseif($currentStep == 2)
                    @include('livewire.registration-wizard.step2-parents')
                @elseif($currentStep == 3)
                    @include('livewire.registration-wizard.step3-school')
                @elseif($currentStep == 4)
                    @include('livewire.registration-wizard.step4-documents')
                @elseif($currentStep == 5)
                    @include('livewire.registration-wizard.step5-review')
                @endif
            </div>

            @include('livewire.registration-wizard.navigation')
        </div>
    @endif
</div>