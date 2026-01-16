<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionExportController extends Controller
{
    public function pdf(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = Transaction::with(['bill.student', 'user']);

        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->get();
        $totalAmount = $transactions->sum('amount');

        $html = view('pdf.reports.transactions', compact(
            'transactions',
            'totalAmount',
            'startDate',
            'endDate'
        ))->render();

        $options = new \Dompdf\Options;
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'sans-serif');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'transaksi-'.Carbon::parse($startDate)->format('dmY').'-'.Carbon::parse($endDate)->format('dmY').'.pdf';

        return $dompdf->stream($filename, ['Attachment' => false]);
    }
}
