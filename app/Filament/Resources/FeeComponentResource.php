<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeComponentResource\Pages;
use App\Models\FeeComponent;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class FeeComponentResource extends Resource
{
    protected static ?string $model = FeeComponent::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Rincian Biaya';

    protected static ?string $modelLabel = 'Rincian Biaya';

    protected static ?string $pluralModelLabel = 'Rincian Biaya';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user->hasAnyRole(['Administrator', 'Kepala', 'Bendahara Pondok', 'Bendahara Unit', 'Petugas']);
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user->hasAnyRole(['Administrator', 'Bendahara Pondok', 'Bendahara Unit']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        return $user->hasAnyRole(['Administrator', 'Bendahara Pondok', 'Bendahara Unit']);
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();
        return $user->hasAnyRole(['Administrator', 'Bendahara Pondok', 'Bendahara Unit']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('institution_id')
                    ->label('Lembaga')
                    ->relationship('institution', 'name')
                    ->required()
                    ->preload()
                    ->native(false),
                Select::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->relationship('academicYear', 'name')
                    ->required()
                    ->preload()
                    ->native(false),
                TextInput::make('name')
                    ->label('Nama Komponen Biaya')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label('Jenis')
                    ->options([
                        'yearly' => 'Tahunan',
                        'monthly' => 'Bulanan',
                    ])
                    ->required()
                    ->default('yearly')
                    ->native(false),
                TextInput::make('amount')
                    ->label('Nominal')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        $canModify = $user->hasAnyRole(['Administrator', 'Bendahara Pondok', 'Bendahara Unit']);

        return $table
            ->recordUrl(null)
            ->recordAction(null)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Biaya')
                    ->searchable(),
                Tables\Columns\TextColumn::make('institution.name')
                    ->label('Lembaga')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'yearly' => 'Tahunan',
                        'monthly' => 'Bulanan',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'yearly' => 'success',
                        'monthly' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->formatStateUsing(fn($state) => 'Rp. ' . number_format($state, 0, ',', ','))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('institution')
                    ->label('Lembaga')
                    ->relationship('institution', 'name'),
                Tables\Filters\SelectFilter::make('academicYear')
                    ->label('Tahun Ajaran')
                    ->relationship('academicYear', 'name'),
            ])
            ->actions($canModify ? [
                EditAction::make()
                    ->modalWidth('lg'),
                DeleteAction::make(),
            ] : [])
            ->bulkActions($canModify ? [
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ] : []);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeeComponents::route('/'),
        ];
    }
}

