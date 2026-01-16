<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReceiptController extends Controller
{
    /**
     * Admin print receipt (requires login).
     */
    public function adminPrint(Transaction $transaction)
    {
        return $this->generateReceipt($transaction);
    }

    /**
     * Public download receipt via token.
     */
    public function publicDownload(string $token)
    {
        $transaction = Transaction::where('verification_token', $token)->firstOrFail();

        return $this->generateReceipt($transaction);
    }

    /**
     * Public verification page (for QR scan).
     */
    public function verify(string $token)
    {
        $transaction = Transaction::where('verification_token', $token)
            ->with(['student', 'user'])
            ->first();

        if (! $transaction) {
            return view('livewire.verify-receipt', [
                'valid' => false,
                'transaction' => null,
                'student' => null,
            ]);
        }

        return view('livewire.verify-receipt', [
            'valid' => true,
            'transaction' => $transaction,
            'student' => $transaction->student,
        ]);
    }

    /**
     * Generate PDF receipt with QR code.
     */
    protected function generateReceipt(Transaction $transaction)
    {
        $transaction->load(['student.parents', 'student.bills', 'user']);

        $student = $transaction->student;

        // Get all transactions for this student on the same date for a combined receipt
        $transactions = Transaction::where('student_id', $student->id)
            ->where('transaction_date', $transaction->transaction_date)
            ->get();

        $totalAmount = $transactions->sum('amount');

        // Get student's total bill status
        $totalBill = $student->bills->sum('amount');
        $totalPaid = $student->getTotalPaid();
        $remaining = $student->getTotalRemaining();

        // Generate QR code as SVG and encode as base64 for dompdf
        $svgContent = QrCode::format('svg')
            ->size(80)
            ->generate($transaction->getVerifyUrl());
        $qrCode = 'data:image/svg+xml;base64,'.base64_encode($svgContent);

        $html = view('pdf.receipts.transaction', compact(
            'transaction',
            'transactions',
            'student',
            'totalAmount',
            'totalBill',
            'totalPaid',
            'remaining',
            'qrCode'
        ))->render();

        // Generate PDF using dompdf
        $options = new \Dompdf\Options;
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'sans-serif');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A5', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $filename = 'nota-'.$student->registration_number.'-'.\Carbon\Carbon::parse($transaction->transaction_date)->format('dmY').'.pdf';

        return response($output, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }
}
