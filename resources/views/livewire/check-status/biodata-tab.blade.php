<!-- Biodata Tab Content -->
<h3 class="text-base md:text-lg font-semibold text-dat-text mb-4">Data Santri</h3>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4 text-sm">
    <div class="bg-gray-50 p-3 rounded-lg">
        <span class="block text-gray-500 text-xs uppercase">Tempat, Tanggal Lahir</span>
        <span class="font-medium text-dat-text">{{ $student->place_of_birth }},
            {{ $student->date_of_birth }}</span>
    </div>
    <div class="bg-gray-50 p-3 rounded-lg">
        <span class="block text-gray-500 text-xs uppercase">Jenis Kelamin</span>
        <span class="font-medium text-dat-text">{{ $student->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</span>
    </div>
    <div class="bg-gray-50 p-3 rounded-lg sm:col-span-2">
        <span class="block text-gray-500 text-xs uppercase">Alamat</span>
        <span class="font-medium text-dat-text">{{ $student->address_street }}, {{ $student->village }},
            {{ $student->district }}</span>
    </div>
    <div class="bg-gray-50 p-3 rounded-lg">
        <span class="block text-gray-500 text-xs uppercase">Anak Ke</span>
        <span class="font-medium text-dat-text">{{ $student->child_number }} dari
            {{ $student->total_siblings }} bersaudara</span>
    </div>
</div>

@php $parents = $student->parents; @endphp
@if ($parents->isNotEmpty())
    <h3 class="text-base md:text-lg font-semibold text-dat-text mt-6 mb-4">Data Orang Tua</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4 text-sm">
        @foreach ($parents as $parent)
            <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                <span
                    class="block text-xs uppercase tracking-wide text-dat-primary font-semibold mb-1">{{ $parent->type == 'father' ? 'Ayah' : ($parent->type == 'mother' ? 'Ibu' : 'Wali') }}</span>
                <p class="font-medium text-dat-text">{{ $parent->name }}</p>
                <p class="text-gray-500 text-sm">{{ $parent->phone_number }}</p>
            </div>
        @endforeach
    </div>
@endif
