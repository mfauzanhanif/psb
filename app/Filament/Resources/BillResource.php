<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillResource\Pages;
use App\Models\Bill;
use App\Models\FundTransfer;
use App\Models\Student;
use App\Models\Transaction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class BillResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Tagihan';

    protected static ?string $modelLabel = 'Tagihan';

    protected static ?string $pluralModelLabel = 'Tagihan';

    protected static ?string $slug = 'tagihan';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user->hasAnyRole(['Administrator', 'Kepala', 'Bendahara Pondok', 'Bendahara Unit']);
    }

    public static function canCreate(): bool
    {
        return false; // Bills are auto-generated
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction(null)
            ->columns([
                Tables\Columns\TextColumn::make('registration_number')
                    ->label('No Pendaftaran')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama Santri')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_bill')
                    ->label('Total Tagihan')
                    ->getStateUsing(fn(Student $record) => $record->bills->sum('amount'))
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('remaining')
                    ->label('Belum Bayar')
                    ->getStateUsing(fn(Student $record) => $record->bills->sum('remaining_amount'))
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->color(fn($state) => $state > 0 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('panitia_payment')
                    ->label('Pembayaran Panitia')
                    ->getStateUsing(fn(Student $record) => $record->transactions()
                        ->where('payment_location', 'PANITIA')
                        ->sum('amount'))
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('direct_payment')
                    ->label('Pembayaran Langsung')
                    ->getStateUsing(fn(Student $record) => $record->transactions()
                        ->whereIn('payment_location', ['MADRASAH', 'SEKOLAH', 'UNIT'])
                        ->sum('amount'))
                    ->formatStateUsing(fn($state) => $state > 0 ? 'Rp ' . number_format($state, 0, ',', '.') : '-')
                    ->color('success'),
                Tables\Columns\TextColumn::make('distributed')
                    ->label('Sudah Distribusi')
                    ->getStateUsing(function(Student $record) {
                        return FundTransfer::where('student_id', $record->id)
                            ->where('status', 'COMPLETED')
                            ->sum('amount');
                    })
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('pending_distribution')
                    ->label('Belum Distribusi')
                    ->getStateUsing(function(Student $record) {
                        // Total paid at panitia minus distributed
                        $panitia = $record->transactions()
                            ->where('payment_location', 'PANITIA')
                            ->sum('amount');
                        $distributed = FundTransfer::where('student_id', $record->id)
                            ->whereHas('transaction', fn($q) => $q->where('payment_location', 'PANITIA'))
                            ->sum('amount');
                        return max(0, $panitia - $distributed);
                    })
                    ->formatStateUsing(fn($state) => $state > 0 ? 'Rp ' . number_format($state, 0, ',', '.') : '-')
                    ->color(fn($state) => $state > 0 ? 'warning' : 'success'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Status Pembayaran')
                    ->options([
                        'paid' => 'Lunas',
                        'partial' => 'Sebagian',
                        'unpaid' => 'Belum Bayar',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'paid') {
                            return $query->whereDoesntHave('bills', fn($q) => $q->where('remaining_amount', '>', 0));
                        }
                        if ($data['value'] === 'unpaid') {
                            return $query->whereDoesntHave('transactions');
                        }
                        if ($data['value'] === 'partial') {
                            return $query->whereHas('transactions')
                                ->whereHas('bills', fn($q) => $q->where('remaining_amount', '>', 0));
                        }
                        return $query;
                    }),
            ])
            ->actions([])
            ->bulkActions([])
            ->defaultSort('registration_number', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('bills')
            ->with(['bills', 'transactions']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBills::route('/'),
        ];
    }
}
