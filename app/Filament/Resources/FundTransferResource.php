<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FundTransferResource\Pages;
use App\Models\FundTransfer;
use App\Models\Institution;
use App\Models\Transaction;
use App\Services\PaymentDistributionService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
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

class FundTransferResource extends Resource
{
    protected static ?string $model = FundTransfer::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Distribusi Dana';

    protected static ?string $modelLabel = 'Distribusi Dana';

    protected static ?string $pluralModelLabel = 'Distribusi Dana';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        // Petugas can view but not create/approve
        return $user->hasAnyRole(['Administrator', 'Bendahara Pondok', 'Bendahara Unit', 'Kepala', 'Petugas']);
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        // Only Admin and Bendahara Pondok can create fund transfers
        return $user->hasAnyRole(['Administrator', 'Bendahara Pondok']);
    }

    public static function canEdit(Model $record): bool
    {
        return false; // Editing is done via workflow actions
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();
        // Only Admin can delete, and only if not completed
        return $user->hasRole('Administrator') && !$record->isCompleted();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Distribusi')
                    ->schema([
                        Select::make('institution_id')
                            ->label('Lembaga Tujuan')
                            ->relationship('institution', 'name')
                            ->disabled(),

                        TextInput::make('amount')
                            ->label('Jumlah')
                            ->prefix('Rp')
                            ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.'))
                            ->disabled(),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'PENDING' => 'Menunggu Approval',
                                'APPROVED' => 'Disetujui',
                                'COMPLETED' => 'Selesai',
                                'REJECTED' => 'Ditolak',
                            ])
                            ->disabled(),

                        DatePicker::make('transfer_date')
                            ->label('Tanggal Transfer')
                            ->disabled(),

                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(2)
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('institution.name')
                    ->label('Lembaga Tujuan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Santri')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(?string $state) => match ($state) {
                        'PENDING' => 'Menunggu',
                        'APPROVED' => 'Disetujui',
                        'COMPLETED' => 'Selesai',
                        'REJECTED' => 'Ditolak',
                        default => $state ?? 'Legacy',
                    })
                    ->color(fn(?string $state) => match ($state) {
                        'PENDING' => 'warning',
                        'APPROVED' => 'info',
                        'COMPLETED' => 'success',
                        'REJECTED' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('transfer_method')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn(?string $state) => $state === 'cash' ? 'Cash' : 'Transfer')
                    ->color(fn(?string $state) => $state === 'cash' ? 'success' : 'info')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('transfer_date')
                    ->label('Tgl Request')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('approver.name')
                    ->label('Disetujui Oleh')
                    ->toggleable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Tgl Approval')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('receiver.name')
                    ->label('Diterima Oleh')
                    ->toggleable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('received_at')
                    ->label('Tgl Terima')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('institution_id')
                    ->label('Lembaga')
                    ->options(fn() => Institution::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'PENDING' => 'Menunggu',
                        'APPROVED' => 'Disetujui',
                        'COMPLETED' => 'Selesai',
                        'REJECTED' => 'Ditolak',
                    ]),
                Tables\Filters\SelectFilter::make('transfer_method')
                    ->label('Metode')
                    ->options([
                        'cash' => 'Cash',
                        'transfer' => 'Transfer',
                    ]),
            ])
            ->actions([
                // Step 2: Approve Action (Kepala Pondok only)
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Distribusi?')
                    ->modalDescription('Dana akan disiapkan untuk diserahkan ke Unit.')
                    ->modalSubmitActionLabel('Ya, Setujui')
                    ->action(function (FundTransfer $record) {
                        $record->approve(auth()->user());
                        Notification::make()
                            ->title('Distribusi disetujui')
                            ->body('Menunggu konfirmasi penerimaan dari Bendahara Unit.')
                            ->success()
                            ->send();
                    })
                    ->visible(function (FundTransfer $record) {
                        $user = auth()->user();
                        // Admin always allowed
                        if ($user->hasRole('Administrator')) return $record->isPending();
                        // Only Kepala Pondok can approve (institution type = 'pondok')
                        if ($user->hasRole('Kepala') && $user->institution) {
                            return $record->isPending() && $user->institution->type === 'pondok';
                        }
                        return false;
                    }),

                // Step 3: Confirm Receipt Action (Target Bendahara only)
                Action::make('confirm_receipt')
                    ->label('Terima')
                    ->icon('heroicon-o-hand-raised')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Penerimaan Dana?')
                    ->modalDescription('Dana akan dicatat masuk ke kas Unit Anda.')
                    ->modalSubmitActionLabel('Ya, Terima Dana')
                    ->action(function (FundTransfer $record) {
                        $record->confirmReceipt(auth()->user());
                        Notification::make()
                            ->title('Dana diterima')
                            ->body('Dana telah dicatat masuk ke kas Unit.')
                            ->success()
                            ->send();
                    })
                    ->visible(function (FundTransfer $record) {
                        $user = auth()->user();
                        // Admin always allowed
                        if ($user->hasRole('Administrator')) return $record->isApproved();
                        // Only the target institution's Bendahara can confirm receipt
                        if ($user->hasRole('Bendahara Unit') && $user->institution_id) {
                            return $record->isApproved() && $record->institution_id === $user->institution_id;
                        }
                        // Bendahara Pondok can also receive for Pondok institution
                        if ($user->hasRole('Bendahara Pondok') && $user->institution_id) {
                            return $record->isApproved() && $record->institution_id === $user->institution_id;
                        }
                        return false;
                    }),

                // Reject Action (Kepala Pondok only)
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Distribusi?')
                    ->modalDescription('Permintaan distribusi akan ditolak.')
                    ->modalSubmitActionLabel('Ya, Tolak')
                    ->action(function (FundTransfer $record) {
                        $record->reject();
                        Notification::make()
                            ->title('Distribusi ditolak')
                            ->warning()
                            ->send();
                    })
                    ->visible(function (FundTransfer $record) {
                        $user = auth()->user();
                        if ($user->hasRole('Administrator')) return $record->isPending();
                        // Only Kepala Pondok can reject
                        if ($user->hasRole('Kepala') && $user->institution) {
                            return $record->isPending() && $user->institution->type === 'pondok';
                        }
                        return false;
                    }),

                // View detail action
                Action::make('view_detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading('Detail Distribusi')
                    ->modalContent(function (FundTransfer $record) {
                        return view('filament.resources.fund-transfer.detail-modal', [
                            'record' => $record,
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),

                // Delete/Cancel action (Admin only, for pending/rejected)
                DeleteAction::make()
                    ->label('Hapus')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Distribusi?')
                    ->modalDescription('Data distribusi akan dihapus permanen.')
                    ->visible(fn(FundTransfer $record) =>
                        auth()->user()->hasRole('Administrator') &&
                        !$record->isCompleted()
                    ),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->requiresConfirmation()
                        ->visible(fn() => auth()->user()->hasRole('Administrator')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFundTransfers::route('/'),
        ];
    }
}
