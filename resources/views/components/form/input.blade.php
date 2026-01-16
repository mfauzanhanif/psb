@props([
    'name',
    'label' => null,
    'type' => 'text',
    'placeholder' => '',
    'required' => false,
    'maxlength' => null,
    'minlength' => null,
    'pattern' => null,
    'min' => null,
    'max' => null,
    'oninput' => null,
    'disabled' => false,
    'wireModel' => null,
    'wireModelLive' => false,
])

@php
    $wireAttribute = $wireModelLive ? 'wire:model.live' : 'wire:model';
    $inputClasses = 'form-input' . ($disabled ? ' bg-gray-100' : '');
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

    <input 
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $wireModel ? "$wireAttribute=$wireModel" : '' }}
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($maxlength) maxlength="{{ $maxlength }}" @endif
        @if($minlength) minlength="{{ $minlength }}" @endif
        @if($pattern) pattern="{{ $pattern }}" @endif
        @if($min) min="{{ $min }}" @endif
        @if($max) max="{{ $max }}" @endif
        @if($oninput) oninput="{{ $oninput }}" @endif
        @if($disabled) disabled @endif
        {{ $attributes->merge(['class' => $inputClasses]) }}
    >

    @error($wireModel ?? $name)
        <span class="form-error">{{ $message }}</span>
    @enderror
</div>
