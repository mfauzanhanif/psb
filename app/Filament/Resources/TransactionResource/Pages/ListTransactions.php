<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Exports\TransactionsExport;
use App\Filament\Resources\TransactionResource;
use App\Models\Bill;
use App\Models\FundTransfer;
use App\Models\Student;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        $canModify = $user->hasAnyRole(['Administrator', 'Bendahara Pondok', 'Bendahara Unit', 'Petugas']);

        $actions = [];

        // Export Excel dengan periode
        $actions[] = Actions\Action::make('export_excel')
            ->label('Export Excel')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->form([
                DatePicker::make('start_date')
                    ->label('Dari Tanggal')
                    ->default(now()->startOfMonth()),
                DatePicker::make('end_date')
                    ->label('Sampai Tanggal')
                    ->default(now()),
            ])
            ->action(function (array $data) {
                return Excel::download(
                    new TransactionsExport($data['start_date'], $data['end_date']),
                    'transaksi-'.now()->format('Y-m-d').'.xlsx'
                );
            });

        // Export PDF dengan periode
        $actions[] = Actions\Action::make('export_pdf')
            ->label('Export PDF')
            ->icon('heroicon-o-document-arrow-down')
            ->color('danger')
            ->form([
                DatePicker::make('start_date')
                    ->label('Dari Tanggal')
                    ->default(now()->startOfMonth()),
                DatePicker::make('end_date')
                    ->label('Sampai Tanggal')
                    ->default(now()),
            ])
            ->action(function (array $data) {
                return redirect()->route('transactions.export.pdf', [
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                ]);
            });

        if ($canModify) {
            // Single transaction input using the same hybrid logic as CreateTransaction
            $actions[] = Actions\Action::make('create')
                ->label('Tambah Transaksi')
                ->icon('heroicon-o-plus')
                ->modalWidth('xl')
                ->form([
                    Section::make('Cari Santri')
                        ->schema([
                            Select::make('student_id')
                                ->label('Santri')
                                ->options(function () {
                                    return Student::query()
                                        ->whereHas('bills', fn ($q) => $q->where('remaining_amount', '>', 0))
                                        ->get()
                                        ->mapWithKeys(fn ($s) => [$s->id => "{$s->registration_number} - {$s->full_name}"]);
                                })
                                ->searchable()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state) {
                                        $student = Student::with(['bills', 'transactions'])->find($state);
                                        if ($student) {
                                            $totalBill = $student->getTotalBillAmount();
                                            $totalPaid = $student->getTotalPaid();
                                            $totalRemaining = $student->getTotalRemaining();

                                            $set('info_total_bill', number_format($totalBill, 0, ',', '.'));
                                            $set('info_total_paid', number_format($totalPaid, 0, ',', '.'));
                                            $set('info_total_unpaid', number_format($totalRemaining, 0, ',', '.'));
                                        }
                                    } else {
                                        $set('info_total_bill', null);
                                        $set('info_total_paid', null);
                                        $set('info_total_unpaid', null);
                                    }
                                }),
                        ]),

                    Section::make('Informasi Tagihan')
                        ->schema([
                            TextInput::make('info_total_bill')
                                ->label('Total Tagihan')
                                ->prefix('Rp')
                                ->readOnly(),
                            TextInput::make('info_total_paid')
                                ->label('Sudah Bayar')
                                ->prefix('Rp')
                                ->readOnly(),
                            TextInput::make('info_total_unpaid')
                                ->label('Belum Bayar')
                                ->prefix('Rp')
                                ->readOnly(),
                        ])
                        ->columns(3),

                    Section::make('Pembayaran')
                        ->schema([
                            TextInput::make('amount')
                                ->label('Jumlah Bayar')
                                ->prefix('Rp')
                                ->required()
                                ->numeric()
                                ->minValue(1),

                            Select::make('payment_method')
                                ->label('Metode Pembayaran')
                                ->options([
                                    'cash' => 'Cash',
                                    'transfer' => 'Transfer',
                                ])
                                ->required()
                                ->default('cash')
                                ->native(false),

                            DatePicker::make('transaction_date')
                                ->label('Tanggal Transaksi')
                                ->required()
                                ->default(now()),

                            FileUpload::make('proof_image')
                                ->label('Bukti Pembayaran')
                                ->image()
                                ->directory('proofs')
                                ->openable()
                                ->downloadable(),

                            Textarea::make('notes')
                                ->label('Catatan'),
                        ]),
                ])
                ->action(function (array $data): void {
                    $user = auth()->user();
                    $studentId = $data['student_id'] ?? null;

                    if (! $studentId) {
                        Notification::make()
                            ->title('Santri harus dipilih')
                            ->danger()
                            ->send();

                        return;
                    }

                    // Determine payment location based on user role and institution
                    $isUnitUser = $user->hasRole('Bendahara Unit') && $user->institution_id;
                    $paymentLocation = $isUnitUser ? 'UNIT' : 'PANITIA';

                    // Get the student's unpaid bills
                    $student = Student::with('bills.institution')->find($studentId);
                    $unpaidBills = $student->bills->where('remaining_amount', '>', 0)->sortBy('id');

                    if ($unpaidBills->isEmpty()) {
                        Notification::make()
                            ->title('Tidak ada tagihan yang perlu dibayar')
                            ->danger()
                            ->send();

                        return;
                    }

                    $amountToPay = (float) $data['amount'];
                    $totalPaid = 0;

                    // Generate verification token
                    $verificationToken = Str::random(32);

                    // Create single transaction for the total amount
                    $transaction = Transaction::create([
                        'student_id' => $studentId,
                        'amount' => $amountToPay,
                        'payment_method' => $data['payment_method'] ?? 'cash',
                        'transaction_date' => $data['transaction_date'],
                        'proof_image' => $data['proof_image'] ?? null,
                        'notes' => $data['notes'] ?? null,
                        'user_id' => $user->id,
                        'verification_token' => $verificationToken,
                        'payment_location' => $paymentLocation,
                        'is_settled' => $isUnitUser,
                    ]);

                    // Distribute payment across bills (FIFO) and update remaining_amount
                    foreach ($unpaidBills as $bill) {
                        if ($amountToPay <= 0) {
                            break;
                        }

                        $payForThisBill = min($amountToPay, (float) $bill->remaining_amount);

                        // Apply payment to bill - THIS IS THE KEY FIX!
                        $bill->applyPayment($payForThisBill);
                        $totalPaid += $payForThisBill;

                        // For UNIT payments, auto-create COMPLETED FundTransfer
                        if ($isUnitUser) {
                            FundTransfer::create([
                                'institution_id' => $user->institution_id,
                                'student_id' => $studentId,
                                'bill_id' => $bill->id,
                                'transaction_id' => $transaction->id,
                                'user_id' => $user->id,
                                'amount' => $payForThisBill,
                                'transfer_date' => now(),
                                'transfer_method' => $data['payment_method'] ?? 'cash',
                                'notes' => 'Auto-transfer: Pembayaran langsung di Unit',
                                'status' => 'COMPLETED',
                                'received_at' => now(),
                                'received_by' => $user->id,
                            ]);
                        }

                        $amountToPay -= $payForThisBill;
                    }

                    // Show appropriate notification based on location
                    if ($paymentLocation === 'UNIT') {
                        Notification::make()
                            ->title('Pembayaran berhasil dicatat')
                            ->body('Dana tercatat langsung di kas Unit.')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Pembayaran berhasil dicatat')
                            ->body('Dana berada di Panitia. Silakan lakukan distribusi ke Unit terkait.')
                            ->success()
                            ->send();
                    }
                });
        }

        return $actions;
    }
}
