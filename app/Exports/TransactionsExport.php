<?php

namespace App\Exports;

use App\Models\Transaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = Transaction::with(['student', 'user']);

        if ($this->startDate) {
            $query->whereDate('transaction_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('transaction_date', '<=', $this->endDate);
        }

        return $query->orderBy('transaction_date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'No. Pendaftaran',
            'Nama Santri',
            'Keterangan',
            'Jumlah',
            'Metode',
            'Petugas',
            'Catatan',
        ];
    }

    public function map($transaction): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            Carbon::parse($transaction->transaction_date)->format('d/m/Y'),
            $transaction->student?->registration_number ?? '-',
            $transaction->student?->full_name ?? '-',
            'Biaya Pendaftaran',
            $transaction->amount,
            $transaction->payment_method === 'cash' ? 'Cash' : 'Transfer',
            $transaction->user?->name ?? 'System',
            $transaction->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
