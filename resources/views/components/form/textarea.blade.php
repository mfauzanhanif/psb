@props([
    'name',
    'label' => null,
    'placeholder' => '',
    'required' => false,
    'rows' => 3,
    'disabled' => false,
    'wireModel' => null,
])

<div>
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <textarea 
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if($wireModel) wire:model="{{ $wireModel }}" @endif
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($disabled) disabled @endif
        {{ $attributes->merge(['class' => 'form-input']) }}
    >{{ $slot }}</textarea>

    @error($wireModel ?? $name)
        <span class="form-error">{{ $message }}</span>
    @enderror
</div>
