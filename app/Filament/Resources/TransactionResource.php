<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Student;
use App\Models\Transaction;
use App\Services\FonnteService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Transaksi';

    protected static ?string $modelLabel = 'Transaksi';

    protected static ?string $pluralModelLabel = 'Transaksi';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user->hasAnyRole(['Administrator', 'Kepala', 'Bendahara Pondok', 'Bendahara Unit', 'Petugas']);
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user->hasAnyRole(['Administrator', 'Bendahara Pondok', 'Bendahara Unit', 'Petugas']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        return $user->hasAnyRole(['Administrator', 'Bendahara Pondok', 'Bendahara Unit', 'Petugas']);
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();
        return $user->hasAnyRole(['Administrator', 'Bendahara Pondok', 'Bendahara Unit', 'Petugas']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Cari Santri')
                    ->schema([
                        Select::make('student_id')
                            ->label('Santri')
                            ->options(function () {
                                return Student::query()
                                    ->whereHas('bills', fn($q) => $q->where('remaining_amount', '>', 0))
                                    ->get()
                                    ->mapWithKeys(fn($s) => [$s->id => "{$s->registration_number} - {$s->full_name}"]);
                            })
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $student = Student::with(['bills', 'transactions'])->find($state);
                                    if ($student) {
                                        $totalBill = $student->getTotalBillAmount();
                                        // Use getTotalPaid() from transactions - doesn't change after distribution
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
                    ])
                    ->columnSpanFull(),

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
                    ->columns(3)
                    ->columnSpanFull(),

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
                    ])
                    ->columnSpanFull(),

                Hidden::make('user_id')
                    ->default(fn() => Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        $canModify = $user->hasAnyRole(['Administrator', 'Bendahara Pondok', 'Bendahara Unit', 'Petugas']);

        return $table
            ->recordUrl(null)
            ->recordAction(null)
            ->columns([
                Tables\Columns\TextColumn::make('student.registration_number')
                    ->label('No. Daftar')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Santri')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('distribution_status')
                    ->label('Status Distribusi')
                    ->getStateUsing(fn(Transaction $record) => $record->isFullyDistributed() ? 'Sudah Disetor' : 'Menunggu Distribusi')
                    ->badge()
                    ->color(fn(Transaction $record) => $record->isFullyDistributed() ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('payment_location')
                    ->label('Lokasi Dana')
                    ->badge()
                    ->getStateUsing(fn(Transaction $record) => $record->getPaymentLocationLabel())
                    ->color(fn(Transaction $record) => $record->isAtPanitia() ? 'warning' : 'info'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->formatStateUsing(fn($state) => 'Rp. ' . number_format($state, 0, ',', ','))
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn(?string $state) => $state === 'cash' ? 'Cash' : 'Transfer')
                    ->color(fn(?string $state) => $state === 'cash' ? 'success' : 'info'),
                Tables\Columns\TextColumn::make('undistributed_amount')
                    ->label('Belum Disetor')
                    ->getStateUsing(fn(Transaction $record) => $record->getUndistributedAmount())
                    ->formatStateUsing(fn($state) => $state > 0 ? 'Rp. ' . number_format($state, 0, ',', ',') : '-')
                    ->color(fn($state) => $state > 0 ? 'warning' : 'success')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('proof_image')
                    ->label('Bukti')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Petugas')
                    ->default('System/Guest')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Metode')
                    ->options([
                        'cash' => 'Cash',
                        'transfer' => 'Transfer',
                    ]),
                Tables\Filters\SelectFilter::make('payment_location')
                    ->label('Lokasi Dana')
                    ->options([
                        'PANITIA' => 'Panitia',
                        'UNIT' => 'Unit',
                    ]),
                Tables\Filters\TernaryFilter::make('is_settled')
                    ->label('Status Settle')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah Settle')
                    ->falseLabel('Belum Settle'),
            ])
            ->actions([
                // Cetak Nota (admin)
                Action::make('cetak_nota')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn(Transaction $record) => route('transaksi.cetak', $record))
                    ->openUrlInNewTab(),

                // Kirim Nota via WhatsApp
                Action::make('kirim_nota')
                    ->label('Kirim')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->form([
                        Select::make('recipient')
                            ->label('Kirim ke')
                            ->options(function (Transaction $record) {
                                $student = $record->student;
                                $student->load('parents');
                                $options = [];

                                foreach ($student->parents as $parent) {
                                    if (!empty($parent->phone_number)) {
                                        $label = match ($parent->type) {
                                            'father' => 'Ayah',
                                            'mother' => 'Ibu',
                                            'guardian' => 'Wali',
                                            default => $parent->type,
                                        };
                                        $options[$parent->phone_number] = "{$label} - {$parent->name} ({$parent->phone_number})";
                                    }
                                }

                                return $options;
                            })
                            ->required()
                            ->native(false)
                    ])
                    ->action(function (Transaction $record, array $data) {
                        $fonnteService = app(FonnteService::class);
                        $studentName = $record->student->full_name;
                        $downloadUrl = $record->getDownloadUrl();

                        $message = "Assalamualaikum,\n\n" .
                            "Berikut nota pembayaran atas nama *{$studentName}*.\n\n" .
                            "Unduh: {$downloadUrl}\n\n" .
                            "Terima kasih.\n" .
                            "_Pondok Pesantren Dar Al Tauhid_";

                        $result = $fonnteService->send($data['recipient'], $message);

                        if ($result['success']) {
                            Notification::make()
                                ->title('Berhasil')
                                ->body('Nota berhasil dikirim via WhatsApp!')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Gagal')
                                ->body('Gagal mengirim: ' . ($result['message'] ?? 'Unknown error'))
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(function (Transaction $record) {
                        $student = $record->student;
                        return $student->parents()->whereNotNull('phone_number')->where('phone_number', '!=', '')->exists();
                    }),

                DeleteAction::make()
                    ->visible($canModify),
            ])
            ->bulkActions($canModify ? [
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ] : [])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery()->with(['student', 'user']);
        $user = auth()->user();

        if ($user->hasRole(['Administrator', 'Bendahara Pondok'])) {
            return $query;
        }

        // For other roles, filter by student's registration destination institution
        if ($user->institution_id) {
            $institution = \App\Models\Institution::find($user->institution_id);

            if ($user->hasRole('Bendahara Unit')) {
                // For Pondok/Madrasah: show all transactions (via bills)
                if ($institution && in_array($institution->type, ['pondok', 'madrasah'])) {
                    return $query->whereHas('student.bills', function ($q) use ($user) {
                        $q->where('institution_id', $user->institution_id);
                    });
                }
                return $query->whereHas('student.registration', function ($q) use ($user) {
                    $q->where('destination_institution_id', $user->institution_id);
                });
            }

            if ($user->hasRole('Kepala')) {
                if ($institution && in_array($institution->type, ['pondok', 'madrasah'])) {
                    return $query->whereHas('student.bills', function ($q) use ($user) {
                        $q->where('institution_id', $user->institution_id);
                    });
                }
                return $query->whereHas('student.registration', function ($q) use ($user) {
                    $q->where('destination_institution_id', $user->institution_id);
                });
            }
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
        ];
    }
}
