<!-- Payment/Finance Tab Content -->
<h3 class="text-base md:text-lg font-semibold text-dat-text mb-4">Tagihan & Pembayaran</h3>

@if ($totalBill > 0)
    <!-- Summary Card -->
    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200 shadow-sm">
        <!-- Status Badge -->
        <div class="flex justify-center mb-4">
            @if ($overallStatus == 'paid')
                <span
                    class="px-4 py-2 text-sm font-semibold rounded-full bg-green-100 text-green-800 border border-green-300">
                    ✓ Lunas
                </span>
            @elseif($overallStatus == 'partial')
                <span
                    class="px-4 py-2 text-sm font-semibold rounded-full bg-amber-100 text-amber-800 border border-amber-300">
                    ◐ Dicicil
                </span>
            @else
                <span class="px-4 py-2 text-sm font-semibold rounded-full bg-red-100 text-red-800 border border-red-300">
                    ○ Belum Lunas
                </span>
            @endif
        </div>

        <!-- Totals Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <span class="block text-xs text-gray-500 uppercase tracking-wide mb-1">Total Tagihan</span>
                <span class="block text-lg md:text-xl font-bold text-dat-text">
                    Rp {{ number_format($totalBill, 0, ',', '.') }}
                </span>
            </div>
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <span class="block text-xs text-gray-500 uppercase tracking-wide mb-1">Sudah Dibayar</span>
                <span class="block text-lg md:text-xl font-bold text-green-600">
                    Rp {{ number_format($totalPaid, 0, ',', '.') }}
                </span>
            </div>
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <span class="block text-xs text-gray-500 uppercase tracking-wide mb-1">Sisa Tagihan</span>
                <span
                    class="block text-lg md:text-xl font-bold {{ $remainingAmount > 0 ? 'text-red-600' : 'text-green-600' }}">
                    Rp {{ number_format($remainingAmount, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Info Pembayaran -->
        @if ($remainingAmount > 0)
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm font-medium text-blue-800 mb-2">Informasi Pembayaran:</p>
                <div class="text-sm text-blue-700 space-y-1">
                    <p><span class="font-medium">Bank:</span> BRI</p>
                    <p><span class="font-medium">No. Rekening:</span> 060101000494304</p>
                    <p><span class="font-medium">A.N.:</span> Yayasan Dar Al Tauhid</p>
                </div>
            </div>
        @endif
    </div>
@else
    <div class="text-center py-8 text-gray-500">
        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
            </path>
        </svg>
        Belum ada tagihan.
    </div>
@endif
