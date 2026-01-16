<div class="space-y-4">
    @if($transactions->isEmpty())
        <div class="text-center py-4 text-gray-500">
            Belum ada transaksi untuk tagihan ini.
        </div>
    @else
        <div class="bg-gray-50 p-4 rounded-lg mb-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm text-gray-500">Total Tagihan:</span>
                    <p class="font-bold">Rp {{ number_format($record->amount, 0, ',', '.') }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Sisa Tagihan:</span>
                    <p class="font-bold {{ $record->remaining_amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                        Rp {{ number_format($record->remaining_amount, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="divide-y divide-gray-200">
            @foreach($transactions as $transaction)
                <div class="py-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                            <p class="text-sm text-gray-500">{{ $transaction->transaction_date->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
                                {{ $transaction->user?->name ?? 'System' }}
                            </span>
                        </div>
                    </div>
                    @if($transaction->notes)
                        <p class="text-sm text-gray-600 mt-1">{{ $transaction->notes }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>