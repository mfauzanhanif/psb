<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Lembaga Tujuan</p>
            <p class="font-medium text-gray-900 dark:text-white">{{ $record->institution->name }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Jumlah</p>
            <p class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($record->amount, 0, ',', '.') }}
            </p>
        </div>
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Transfer</p>
            <p class="font-medium text-gray-900 dark:text-white">{{ $record->transfer_date->format('d/m/Y') }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Metode</p>
            <p class="font-medium text-gray-900 dark:text-white">
                {{ $record->transfer_method === 'cash' ? 'Cash/Tunai' : 'Transfer Bank' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Petugas</p>
            <p class="font-medium text-gray-900 dark:text-white">{{ $record->user?->name ?? 'System' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Dibuat</p>
            <p class="font-medium text-gray-900 dark:text-white">{{ $record->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    @if($record->student)
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Santri</p>
            <p class="font-medium text-gray-900 dark:text-white">{{ $record->student->full_name }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $record->student->registration_number }}</p>
        </div>
    @endif

    @if($record->notes)
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Catatan</p>
            <p class="text-gray-900 dark:text-white">{{ $record->notes }}</p>
        </div>
    @endif
</div>