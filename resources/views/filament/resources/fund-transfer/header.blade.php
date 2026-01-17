<div class="mb-6 space-y-4">
    {{-- Table 1: Rekap Panitia --}}
    <div
        class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
        <div class="fi-section-header flex flex-col gap-3 px-6 py-4">
            <div class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                💰 Rekap Pembayaran di Panitia
            </div>
        </div>
        <div class="fi-section-content overflow-x-auto">
            <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th
                            class="fi-ta-header-cell px-4 py-3 text-start text-sm font-semibold text-gray-950 dark:text-white">
                            Total Pembayaran di Panitia</th>
                        <th
                            class="fi-ta-header-cell px-4 py-3 text-start text-sm font-semibold text-gray-950 dark:text-white">
                            Belum Didistribusikan</th>
                        <th
                            class="fi-ta-header-cell px-4 py-3 text-start text-sm font-semibold text-gray-950 dark:text-white">
                            Sudah Didistribusikan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                    <tr>
                        <td class="fi-ta-cell px-4 py-3 text-lg font-bold text-gray-950 dark:text-white">
                            Rp {{ number_format($panitiaSummary['total_panitia'] ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="fi-ta-cell px-4 py-3 text-lg font-bold text-amber-600 dark:text-amber-400">
                            Rp {{ number_format($panitiaSummary['total_pending'] ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="fi-ta-cell px-4 py-3 text-lg font-bold text-green-600 dark:text-green-400">
                            Rp {{ number_format($panitiaSummary['total_distributed'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pending Settlements Workflow --}}
    @if (isset($pendingSettlements) && count($pendingSettlements) > 0)
        <div
            class="fi-section rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 overflow-hidden">
            <div class="fi-section-header px-6 py-3 border-b border-blue-200 dark:border-blue-700">
                <div class="text-base font-semibold text-blue-800 dark:text-blue-200">
                    ⏳ Proses Distribusi Berjalan
                </div>
            </div>
            <div class="px-6 py-3 flex flex-wrap gap-4">
                @foreach ($pendingSettlements as $item)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg px-4 py-3 shadow-sm border border-blue-100 dark:border-blue-800">
                        <div class="font-medium text-gray-900 dark:text-white">
                            {{ $item['institution']->name ?? 'Unknown' }}</div>
                        <div class="text-lg font-bold text-blue-600 dark:text-blue-400">Rp
                            {{ number_format($item['total_amount'], 0, ',', '.') }}</div>
                        <div class="flex gap-2 mt-1 text-xs">
                            @if ($item['pending_count'] > 0)
                                <span
                                    class="px-2 py-0.5 rounded bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                    {{ $item['pending_count'] }} Menunggu</span>
                            @endif
                            @if ($item['approved_count'] > 0)
                                <span
                                    class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ $item['approved_count'] }} Disetujui</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Table 2: Hak Per Lembaga --}}
    <div
        class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
        <div class="fi-section-header flex flex-col gap-3 px-6 py-4">
            <div class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                📊 Hak Dana Per Lembaga
            </div>
            <p class="fi-section-header-description text-sm text-gray-500 dark:text-gray-400">
                Priority Algorithm: Madrasah (100%) → 50:50 Sekolah/Pondok
            </p>
        </div>

        @if (count($summary) > 0)
            <div class="fi-section-content overflow-x-auto">
                <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th
                                class="fi-ta-header-cell px-4 py-3 text-start text-sm font-semibold text-gray-950 dark:text-white">
                                Lembaga</th>
                            <th
                                class="fi-ta-header-cell px-4 py-3 text-end text-sm font-semibold text-gray-950 dark:text-white">
                                Hak Lembaga</th>
                            <th
                                class="fi-ta-header-cell px-4 py-3 text-end text-sm font-semibold text-gray-950 dark:text-white">
                                Pembayaran Langsung</th>
                            <th
                                class="fi-ta-header-cell px-4 py-3 text-end text-sm font-semibold text-gray-950 dark:text-white">
                                Total Diterima</th>
                            <th
                                class="fi-ta-header-cell px-4 py-3 text-end text-sm font-semibold text-gray-950 dark:text-white">
                                Sisa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                        @foreach ($summary as $item)
                            <tr class="fi-ta-row transition duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="fi-ta-cell px-4 py-3 text-sm text-gray-950 dark:text-white font-medium">
                                    {{ $item['institution']->name }}
                                </td>
                                <td class="fi-ta-cell px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-end">
                                    Rp {{ number_format($item['priority_entitlement'] ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="fi-ta-cell px-4 py-3 text-sm text-end">
                                    @if (($item['direct_payments'] ?? 0) > 0)
                                        <span class="text-green-600 dark:text-green-400">
                                            Rp {{ number_format($item['direct_payments'], 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td
                                    class="fi-ta-cell px-4 py-3 text-sm text-gray-950 dark:text-white text-end font-medium">
                                    Rp {{ number_format($item['total_transferred'] ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="fi-ta-cell px-4 py-3 text-sm text-end">
                                    @if (($item['pending_amount'] ?? 0) > 0)
                                        <span
                                            class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1 bg-warning-50 text-warning-600 ring-warning-600/10 dark:bg-warning-400/10 dark:text-warning-400 dark:ring-warning-400/30">
                                            Rp {{ number_format($item['pending_amount'], 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span
                                            class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1 bg-success-50 text-success-600 ring-success-600/10 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30">
                                            ✓ Lunas
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="fi-section-content px-6 py-12">
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data transaksi</p>
                </div>
            </div>
        @endif
    </div>
</div>
