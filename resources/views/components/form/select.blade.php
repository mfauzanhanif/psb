@props([
    'name',
    'label' => null,
    'required' => false,
    'disabled' => false,
    'wireModel' => null,
    'wireModelLive' => false,
    'placeholder' => '-- Pilih --',
])

@php
    $wireAttribute = $wireModelLive ? 'wire:model.live' : 'wire:model';
    $selectClasses = 'form-input' . ($disabled ? ' bg-gray-100' : '');
@endphp

<div>
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <select 
            id="{{ $name }}"
            name="{{ $name }}"
            {{ $wireModel ? "$wireAttribute=$wireModel" : '' }}
            @if($disabled) disabled @endif
            {{ $attributes->merge(['class' => $selectClasses]) }}
        >
            <option value="">{{ $placeholder }}</option>
            {{ $slot }}
        </select>

        {{-- Loading indicator for Livewire --}}
        @if($wireModel)
            <div wire:loading wire:target="{{ $wireModel }}" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin h-5 w-5 text-dat-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        @endif
    </div>

    @error($wireModel ?? $name)
        <span class="form-error">{{ $message }}</span>
    @enderror
</div>
