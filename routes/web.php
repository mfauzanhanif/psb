<?php

use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\RegistrationPdfController;
use App\Http\Controllers\TransactionExportController;
use App\Livewire\CheckStatus;
use App\Livewire\Home;
use App\Livewire\RegistrationWizard;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/daftar', RegistrationWizard::class)->name('register');
Route::get('/cek-status', CheckStatus::class)->name('check-status');
Route::get('/dokumen/{document}', [\App\Http\Controllers\DocumentController::class, 'show'])->name('documents.show')->middleware('auth');
Route::get('/registrasi/{student}/pdf', [RegistrationPdfController::class, 'download'])->name('registration.pdf');

// Admin routes (require auth)
Route::middleware('auth')->group(function () {
    Route::get('/admin/transaksi/{transaction}/cetak', [ReceiptController::class, 'adminPrint'])->name('transaksi.cetak');
    Route::get('/transactions/export/pdf', [TransactionExportController::class, 'pdf'])->name('transactions.export.pdf');
});

// Public routes for e-signed receipts
Route::get('/transaksi/{token}', [ReceiptController::class, 'publicDownload'])->name('transaksi.download');
Route::get('/transaksi/verify/{token}', [ReceiptController::class, 'verify'])->name('transaksi.verify');

