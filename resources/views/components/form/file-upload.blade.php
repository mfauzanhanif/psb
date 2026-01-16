@props([
    'name',
    'label' => null,
    'required' => false,
    'accept' => '.jpg,.jpeg,.png,.pdf',
    'maxSize' => '2MB',
    'wireModel' => null,
    'currentFile' => null,
    'icon' => 'document',
])

@php
    $icons = [
        'document' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>',
        'id-card' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884.896 1.6 2 1.6 1.104 0 2-.716 2-1.6M12 12v3m0 0l-2-2m2 2l2-2"></path>',
        'image' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>',
    ];
@endphp

<div class="file-dropzone">
    {{-- Icon --}}
    <div class="w-12 h-12 bg-dat-primary/10 rounded-full flex items-center justify-center mb-3">
        <svg class="w-6 h-6 text-dat-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $icons[$icon] ?? $icons['document'] !!}
        </svg>
    </div>

    {{-- Label --}}
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2 text-center">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    {{-- File Input --}}
    <input 
        type="file"
        id="{{ $name }}"
        name="{{ $name }}"
        @if($wireModel) wire:model="{{ $wireModel }}" @endif
        accept="{{ $accept }}"
        class="block w-full text-sm text-gray-500 
               file:mr-2 file:py-2 file:px-3 file:rounded-full file:border-0 
               file:text-xs file:font-semibold file:bg-dat-primary file:text-white 
               hover:file:bg-dat-secondary cursor-pointer"
    >

    {{-- Loading Indicator --}}
    @if($wireModel)
        <div wire:loading wire:target="{{ $wireModel }}" class="mt-2 text-xs text-dat-primary">
            <svg class="animate-spin inline-block h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Mengupload...
        </div>
    @endif

    {{-- Success Message (current file) --}}
    @if($currentFile)
        <p class="text-dat-primary text-xs mt-2 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ is_object($currentFile) ? $currentFile->getClientOriginalName() : $currentFile }}
        </p>
    @endif

    {{-- Error Message --}}
    @error($wireModel ?? $name)
        <span class="form-error text-center">{{ $message }}</span>
    @enderror

    {{-- Help Text --}}
    <p class="text-xs text-gray-400 mt-2">Format: {{ str_replace('.', '', strtoupper($accept)) }} (Maks. {{ $maxSize }})</p>
</div>
