<?php

namespace App\Filament\Resources\FundTransferResource\Pages;

use App\Filament\Resources\FundTransferResource;
use App\Models\FundTransfer;
use App\Models\Institution;
use App\Models\Transaction;
use App\Services\PaymentDistributionService;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListFundTransfers extends ListRecords
{
    protected static string $resource = FundTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Create Settlement Request Action (Step 1)
            Actions\Action::make('create_settlement_request')
                ->label('Buat Distribusi Baru')
                ->icon('heroicon-o-banknotes')
                ->color('primary')
                ->modalWidth('xl')
                ->form([
                    Select::make('institution_id')
                        ->label('Lembaga Tujuan')
                        ->options(function () {
                            $service = app(PaymentDistributionService::class);
                            $distribution = $service->calculateBulkDistribution();

                            $options = [];
                            foreach ($distribution as $institutionId => $data) {
                                if ($data['total_pending'] > 0) {
                                    $options[$institutionId] = $data['institution']->name .
                                        ' (Tersedia: Rp ' . number_format($data['total_pending'], 0, ',', '.') . ')';
                                }
                            }
                            return $options;
                        })
                        ->required()
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $service = app(PaymentDistributionService::class);
                                $distribution = $service->calculateBulkDistribution();
                                $data = $distribution->get((int) $state);

                                if ($data) {
                                    $set('available_amount', number_format($data['total_pending'], 0, ',', '.'));
                                    $set('max_amount', $data['total_pending']);
                                    $set('distribution_details', $this->formatDistributionDetails($data['details']));
                                }
                            } else {
                                $set('available_amount', null);
                                $set('max_amount', null);
                                $set('distribution_details', null);
                            }
                        }),

                    TextInput::make('available_amount')
                        ->label('Dana Tersedia (dari Priority Algorithm)')
                        ->prefix('Rp')
                        ->readOnly()
                        ->helperText('Jumlah yang dihitung berdasarkan algoritma prioritas (Madrasah → 50:50 Sekolah/Pondok)'),

                    TextInput::make('max_amount')
                        ->hidden(),

                    Textarea::make('distribution_details')
                        ->label('Rincian Per Santri')
                        ->readOnly()
                        ->rows(5),

                    Select::make('transfer_method')
                        ->label('Metode Transfer')
                        ->options([
                            'cash' => 'Cash/Tunai',
                            'transfer' => 'Transfer Bank',
                        ])
                        ->required()
                        ->default('cash')
                        ->native(false),

                    DatePicker::make('transfer_date')
                        ->label('Tanggal Request')
                        ->required()
                        ->default(now()),

                    Textarea::make('notes')
                        ->label('Catatan')
                        ->placeholder('Contoh: Pencairan Termin 1')
                        ->rows(2),
                ])
                ->action(function (array $data): void {
                    $institution = Institution::find($data['institution_id']);
                    if (!$institution) {
                        Notification::make()
                            ->title('Gagal')
                            ->body('Lembaga tidak ditemukan')
                            ->danger()
                            ->send();
                        return;
                    }

                    $service = app(PaymentDistributionService::class);
                    
                    // Use the service to create PENDING transfers (not auto-complete)
                    $transfers = $service->createSettlementRequests(
                        $institution,
                        Auth::user(),
                        $data['transfer_method'],
                        $data['notes'] ?? null
                    );

                    if ($transfers->isEmpty()) {
                        Notification::make()
                            ->title('Tidak Ada Distribusi')
                            ->body('Tidak ada dana yang perlu didistribusikan ke lembaga ini.')
                            ->warning()
                            ->send();
                        return;
                    }

                    $totalAmount = $transfers->sum('amount');
                    Notification::make()
                        ->title('Permintaan Distribusi Dibuat')
                        ->body("Berhasil membuat {$transfers->count()} permintaan distribusi. Total: Rp " . number_format($totalAmount, 0, ',', '.') . ". Menunggu approval dari Kepala.")
                        ->success()
                        ->send();
                })
                ->visible(fn() => auth()->user()->hasAnyRole(['Administrator', 'Bendahara Pondok'])),
        ];
    }

    protected function formatDistributionDetails(array $details): string
    {
        $lines = [];
        foreach ($details as $detail) {
            if ($detail['pending'] <= 0) {
                continue;
            }
            $student = \App\Models\Student::find($detail['student_id']);
            $studentName = $student ? $student->full_name : 'Unknown';
            $amount = number_format($detail['pending'], 0, ',', '.');
            $lines[] = "- {$studentName}: Rp {$amount}";
        }
        return implode("\n", $lines);
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\FundSummaryWidget::class,
        ];
    }
}
