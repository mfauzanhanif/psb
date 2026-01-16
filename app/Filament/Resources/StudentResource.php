<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Institution;
use App\Models\Student;
use App\Services\WilayahService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Pendaftaran';

    protected static ?string $navigationLabel = 'Daftar Santri Baru';

    protected static ?string $modelLabel = 'Santri';

    protected static ?string $pluralModelLabel = 'Data Santri';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user->hasAnyRole(['Administrator', 'Kepala', 'Bendahara Pondok', 'Bendahara Unit', 'Petugas']);
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user->hasAnyRole(['Administrator', 'Petugas']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        return $user->hasAnyRole(['Administrator', 'Petugas']);
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();
        return $user->hasRole('Administrator');
    }

    public static function form(Schema $schema): Schema
    {
        $educationOptions = [
            'Tidak Sekolah' => 'Tidak Sekolah',
            'SD/Sederajat' => 'SD/Sederajat',
            'SMP/Sederajat' => 'SMP/Sederajat',
            'SMA/Sederajat' => 'SMA/Sederajat',
            'D1' => 'D1',
            'D2' => 'D2',
            'D3' => 'D3',
            'S1' => 'S1',
            'S2' => 'S2',
            'S3' => 'S3',
        ];

        $jobOptions = [
            'PNS' => 'PNS',
            'TNI/Polri' => 'TNI/Polri',
            'Karyawan Swasta' => 'Karyawan Swasta',
            'Wiraswasta' => 'Wiraswasta',
            'Petani' => 'Petani',
            'Buruh' => 'Buruh',
            'Pedagang' => 'Pedagang',
            'Guru/Dosen' => 'Guru/Dosen',
            'Dokter' => 'Dokter',
            'Tidak Bekerja' => 'Tidak Bekerja',
            'Ibu Rumah Tangga' => 'Ibu Rumah Tangga',
            'Lainnya' => 'Lainnya',
        ];

        $incomeOptions = [
            '< 1 Juta' => '< 1 Juta',
            '1 - 3 Juta' => '1 - 3 Juta',
            '3 - 5 Juta' => '3 - 5 Juta',
            '5 - 10 Juta' => '5 - 10 Juta',
            '> 10 Juta' => '> 10 Juta',
        ];

        $lifeStatusOptions = [
            'alive' => 'Masih Hidup',
            'deceased' => 'Sudah Meninggal',
            'unknown' => 'Tidak Diketahui',
        ];

        return $schema
            ->components([
                Tabs::make('Data Santri')
                    ->tabs([
                        // =========================================
                        // TAB 1: DATA SANTRI
                        // =========================================
                        Tabs\Tab::make('Data Santri')
                            ->schema([
                                TextInput::make('full_name')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->columnSpanFull(),
                                Grid::make(2)->schema([
                                    TextInput::make('nik')
                                        ->label('NIK')
                                        ->required()
                                        ->minLength(16)
                                        ->maxLength(16)
                                        ->regex('/^[0-9]{16}$/')
                                        ->validationMessages(['regex' => 'NIK harus terdiri dari 16 digit angka']),
                                    TextInput::make('nisn')
                                        ->label('NISN')
                                        ->minLength(10)
                                        ->maxLength(10)
                                        ->regex('/^[0-9]{10}$/')
                                        ->validationMessages(['regex' => 'NISN harus terdiri dari 10 digit angka']),
                                ]),
                                Grid::make(2)->schema([
                                    TextInput::make('place_of_birth')
                                        ->label('Tempat Lahir')
                                        ->required(),
                                    DatePicker::make('date_of_birth')
                                        ->label('Tanggal Lahir')
                                        ->required(),
                                ]),
                                Grid::make(3)->schema([
                                    Select::make('gender')
                                        ->label('Jenis Kelamin')
                                        ->options([
                                            'male' => 'Laki-laki',
                                            'female' => 'Perempuan',
                                        ])
                                        ->required(),
                                    TextInput::make('child_number')
                                        ->label('Anak Ke-')
                                        ->numeric()
                                        ->required(),
                                    TextInput::make('total_siblings')
                                        ->label('Dari ... Bersaudara')
                                        ->numeric()
                                        ->required(),
                                ]),
                                TextInput::make('registration_number')
                                    ->label('Nomor Pendaftaran')
                                    ->readOnly()
                                    ->visibleOn('edit'),

                                Section::make('Alamat')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Select::make('province_code')
                                                ->label('Provinsi')
                                                ->options(fn() => WilayahService::getProvinces())
                                                ->searchable()
                                                ->required()
                                                ->live()
                                                ->afterStateUpdated(function ($set, ?string $state) {
                                                    $set('regency_code', null);
                                                    $set('district_code', null);
                                                    $set('village_code', null);
                                                    if ($state) {
                                                        $provinces = WilayahService::getProvinces();
                                                        $set('province', $provinces[$state] ?? null);
                                                    }
                                                }),
                                            Select::make('regency_code')
                                                ->label('Kabupaten/Kota')
                                                ->options(fn($get) => $get('province_code') ? WilayahService::getRegencies($get('province_code')) : [])
                                                ->searchable()
                                                ->required()
                                                ->live()
                                                ->afterStateUpdated(function ($set, ?string $state, $get) {
                                                    $set('district_code', null);
                                                    $set('village_code', null);
                                                    if ($state && $get('province_code')) {
                                                        $regencies = WilayahService::getRegencies($get('province_code'));
                                                        $set('regency', $regencies[$state] ?? null);
                                                    }
                                                }),
                                            Select::make('district_code')
                                                ->label('Kecamatan')
                                                ->options(fn($get) => $get('regency_code') ? WilayahService::getDistricts($get('regency_code')) : [])
                                                ->searchable()
                                                ->required()
                                                ->live()
                                                ->afterStateUpdated(function ($set, ?string $state, $get) {
                                                    $set('village_code', null);
                                                    if ($state && $get('regency_code')) {
                                                        $districts = WilayahService::getDistricts($get('regency_code'));
                                                        $set('district', $districts[$state] ?? null);
                                                    }
                                                }),
                                            Select::make('village_code')
                                                ->label('Desa/Kelurahan')
                                                ->options(fn($get) => $get('district_code') ? WilayahService::getVillages($get('district_code')) : [])
                                                ->searchable()
                                                ->required()
                                                ->afterStateUpdated(function ($set, ?string $state, $get) {
                                                    if ($state && $get('district_code')) {
                                                        $villages = WilayahService::getVillages($get('district_code'));
                                                        $set('village', $villages[$state] ?? null);
                                                    }
                                                }),
                                        ]),
                                        // Hidden fields to store the actual names
                                        Hidden::make('province'),
                                        Hidden::make('regency'),
                                        Hidden::make('district'),
                                        Hidden::make('village'),
                                        Textarea::make('address_street')
                                            ->label('Jalan / Blok / RT / RW')
                                            ->required(),
                                    ]),
                            ]),

                        // =========================================
                        // TAB 2: DATA ORANG TUA
                        // =========================================
                        Tabs\Tab::make('Data Orang Tua')
                            ->schema([
                                // AYAH KANDUNG
                                Section::make('Data Ayah Kandung')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('father_name')
                                                ->label('Nama Ayah')
                                                ->required(),
                                            Select::make('father_life_status')
                                                ->label('Status')
                                                ->options($lifeStatusOptions)
                                                ->default('alive'),
                                        ]),
                                        Grid::make(2)->schema([
                                            TextInput::make('father_nik')
                                                ->label('NIK')
                                                ->minLength(16)
                                                ->maxLength(16)
                                                ->regex('/^[0-9]{16}$/')
                                                ->validationMessages(['regex' => 'NIK harus terdiri dari 16 digit angka']),
                                            TextInput::make('father_place_of_birth')
                                                ->label('Tempat Lahir'),
                                        ]),
                                        Grid::make(2)->schema([
                                            DatePicker::make('father_date_of_birth')
                                                ->label('Tanggal Lahir'),
                                            Select::make('father_education')
                                                ->label('Pendidikan Terakhir')
                                                ->options($educationOptions),
                                        ]),
                                        Grid::make(2)->schema([
                                            Checkbox::make('father_has_pesantren')
                                                ->label('Ada Pendidikan Pesantren?')
                                                ->live(),
                                            TextInput::make('father_pesantren_name')
                                                ->label('Nama Pesantren')
                                                ->visible(fn($get) => $get('father_has_pesantren')),
                                        ]),
                                        Grid::make(2)->schema([
                                            Select::make('father_job')
                                                ->label('Pekerjaan')
                                                ->options($jobOptions)
                                                ->live(),
                                            TextInput::make('father_job_other')
                                                ->label('Pekerjaan Lainnya')
                                                ->visible(fn($get) => $get('father_job') === 'Lainnya'),
                                        ]),
                                        Grid::make(2)->schema([
                                            Select::make('father_income')
                                                ->label('Rata-rata Penghasilan')
                                                ->options($incomeOptions),
                                            TextInput::make('father_phone')
                                                ->label('No. WhatsApp'),
                                        ]),
                                    ]),

                                // IBU KANDUNG
                                Section::make('Data Ibu Kandung')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('mother_name')
                                                ->label('Nama Ibu')
                                                ->required(),
                                            Select::make('mother_life_status')
                                                ->label('Status')
                                                ->options($lifeStatusOptions)
                                                ->default('alive'),
                                        ]),
                                        Grid::make(2)->schema([
                                            TextInput::make('mother_nik')
                                                ->label('NIK')
                                                ->minLength(16)
                                                ->maxLength(16)
                                                ->regex('/^[0-9]{16}$/')
                                                ->validationMessages(['regex' => 'NIK harus terdiri dari 16 digit angka']),
                                            TextInput::make('mother_place_of_birth')
                                                ->label('Tempat Lahir'),
                                        ]),
                                        Grid::make(2)->schema([
                                            DatePicker::make('mother_date_of_birth')
                                                ->label('Tanggal Lahir'),
                                            Select::make('mother_education')
                                                ->label('Pendidikan Terakhir')
                                                ->options($educationOptions),
                                        ]),
                                        Grid::make(2)->schema([
                                            Checkbox::make('mother_has_pesantren')
                                                ->label('Ada Pendidikan Pesantren?')
                                                ->live(),
                                            TextInput::make('mother_pesantren_name')
                                                ->label('Nama Pesantren')
                                                ->visible(fn($get) => $get('mother_has_pesantren')),
                                        ]),
                                        Grid::make(2)->schema([
                                            Select::make('mother_job')
                                                ->label('Pekerjaan')
                                                ->options($jobOptions)
                                                ->live(),
                                            TextInput::make('mother_job_other')
                                                ->label('Pekerjaan Lainnya')
                                                ->visible(fn($get) => $get('mother_job') === 'Lainnya'),
                                        ]),
                                        Grid::make(2)->schema([
                                            Select::make('mother_income')
                                                ->label('Rata-rata Penghasilan')
                                                ->options($incomeOptions),
                                            TextInput::make('mother_phone')
                                                ->label('No. WhatsApp'),
                                        ]),
                                    ]),

                                // WALI
                                Section::make('Data Wali')
                                    ->schema([
                                        Radio::make('wali_type')
                                            ->label('Siapa yang menjadi wali santri?')
                                            ->options([
                                                'father' => 'Ayah Kandung',
                                                'mother' => 'Ibu Kandung',
                                                'other' => 'Lainnya',
                                            ])
                                            ->default('father')
                                            ->live()
                                            ->required(),

                                        // Form Wali Lainnya
                                        Section::make('Data Wali Lainnya')
                                            ->visible(fn($get) => $get('wali_type') === 'other')
                                            ->schema([
                                                Grid::make(2)->schema([
                                                    TextInput::make('guardian_name')
                                                        ->label('Nama Wali')
                                                        ->requiredIf('wali_type', 'other'),
                                                    Select::make('guardian_life_status')
                                                        ->label('Status')
                                                        ->options($lifeStatusOptions)
                                                        ->default('alive'),
                                                ]),
                                                Grid::make(2)->schema([
                                                    TextInput::make('guardian_nik')
                                                        ->label('NIK')
                                                        ->minLength(16)
                                                        ->maxLength(16)
                                                        ->regex('/^[0-9]{16}$/')
                                                        ->validationMessages(['regex' => 'NIK harus terdiri dari 16 digit angka']),
                                                    TextInput::make('guardian_place_of_birth')
                                                        ->label('Tempat Lahir'),
                                                ]),
                                                Grid::make(2)->schema([
                                                    DatePicker::make('guardian_date_of_birth')
                                                        ->label('Tanggal Lahir'),
                                                    Select::make('guardian_education')
                                                        ->label('Pendidikan Terakhir')
                                                        ->options($educationOptions),
                                                ]),
                                                Grid::make(2)->schema([
                                                    Checkbox::make('guardian_has_pesantren')
                                                        ->label('Ada Pendidikan Pesantren?')
                                                        ->live(),
                                                    TextInput::make('guardian_pesantren_name')
                                                        ->label('Nama Pesantren')
                                                        ->visible(fn($get) => $get('guardian_has_pesantren')),
                                                ]),
                                                Grid::make(2)->schema([
                                                    Select::make('guardian_job')
                                                        ->label('Pekerjaan')
                                                        ->options($jobOptions)
                                                        ->live(),
                                                    TextInput::make('guardian_job_other')
                                                        ->label('Pekerjaan Lainnya')
                                                        ->visible(fn($get) => $get('guardian_job') === 'Lainnya'),
                                                ]),
                                                Grid::make(2)->schema([
                                                    Select::make('guardian_income')
                                                        ->label('Rata-rata Penghasilan')
                                                        ->options($incomeOptions),
                                                    TextInput::make('guardian_phone')
                                                        ->label('No. WhatsApp'),
                                                ]),
                                            ]),
                                    ]),
                            ]),

                        // =========================================
                        // TAB 3: DATA SEKOLAH
                        // =========================================
                        Tabs\Tab::make('Data Sekolah')
                            ->schema([
                                Section::make('Sekolah Asal')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Select::make('previous_school_level')
                                                ->label('Jenjang Sekolah Asal')
                                                ->options([
                                                    'SD/Sederajat' => 'SD/Sederajat',
                                                    'SMP/Sederajat' => 'SMP/Sederajat',
                                                    'SMA/Sederajat' => 'SMA/Sederajat',
                                                ])
                                                ->required(),
                                            TextInput::make('previous_school_name')
                                                ->label('Nama Sekolah Asal')
                                                ->required(),
                                        ]),
                                        Grid::make(2)->schema([
                                            TextInput::make('previous_school_npsn')
                                                ->label('NPSN'),
                                            Textarea::make('previous_school_address')
                                                ->label('Alamat Sekolah Asal')
                                                ->required(),
                                        ]),
                                    ]),

                                Section::make('Sekolah Tujuan')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Select::make('destination_institution_id')
                                                ->label('Pilihan Sekolah Formal')
                                                ->options(
                                                    Institution::whereIn('type', ['smp', 'ma', 'mts'])
                                                        ->pluck('name', 'id')
                                                )
                                                ->required()
                                                ->live(),
                                            Select::make('destination_class')
                                                ->label('Kelas')
                                                ->options([
                                                    '7' => 'Kelas 7 (SMP/MTs)',
                                                    '8' => 'Kelas 8 (SMP/MTs)',
                                                    '9' => 'Kelas 9 (SMP/MTs)',
                                                    '10' => 'Kelas 10 (MA)',
                                                    '11' => 'Kelas 11 (MA)',
                                                    '12' => 'Kelas 12 (MA)',
                                                ])
                                                ->required(),
                                        ]),
                                        Select::make('funding_source')
                                            ->label('Sumber Pembiayaan')
                                            ->options([
                                                'Orang Tua' => 'Orang Tua',
                                                'Wali' => 'Wali',
                                                'Sendiri' => 'Ditanggung Sendiri',
                                                'Lainnya' => 'Lainnya',
                                            ])
                                            ->default('Orang Tua')
                                            ->required(),
                                    ]),
                            ]),

                        // =========================================
                        // TAB 4: DOKUMEN
                        // =========================================
                        Tabs\Tab::make('Dokumen')
                            ->schema([
                                Grid::make(2)->schema([
                                    FileUpload::make('doc_kk')
                                        ->label('Kartu Keluarga (KK)')
                                        ->disk('local')
                                        ->directory('documents')
                                        ->visibility('private')
                                        ->acceptedFileTypes(['image/*', 'application/pdf'])
                                        ->downloadable()
                                        ->openable(),
                                    FileUpload::make('doc_akta')
                                        ->label('Akta Kelahiran')
                                        ->disk('local')
                                        ->directory('documents')
                                        ->visibility('private')
                                        ->acceptedFileTypes(['image/*', 'application/pdf'])
                                        ->downloadable()
                                        ->openable(),
                                    FileUpload::make('doc_ijazah')
                                        ->label('Ijazah Terakhir')
                                        ->disk('local')
                                        ->directory('documents')
                                        ->visibility('private')
                                        ->acceptedFileTypes(['image/*', 'application/pdf'])
                                        ->downloadable()
                                        ->openable(),
                                    FileUpload::make('doc_photo')
                                        ->label('Pas Foto')
                                        ->disk('local')
                                        ->directory('documents')
                                        ->visibility('private')
                                        ->acceptedFileTypes(['image/*'])
                                        ->downloadable()
                                        ->openable(),
                                    FileUpload::make('doc_ktp_ayah')
                                        ->label('KTP Ayah')
                                        ->disk('local')
                                        ->directory('documents')
                                        ->visibility('private')
                                        ->acceptedFileTypes(['image/*', 'application/pdf'])
                                        ->downloadable()
                                        ->openable(),
                                    FileUpload::make('doc_ktp_ibu')
                                        ->label('KTP Ibu')
                                        ->disk('local')
                                        ->directory('documents')
                                        ->visibility('private')
                                        ->acceptedFileTypes(['image/*', 'application/pdf'])
                                        ->downloadable()
                                        ->openable(),
                                ]),
                                FileUpload::make('doc_ktp_wali')
                                    ->label('KTP Wali')
                                    ->disk('local')
                                    ->directory('documents')
                                    ->visibility('private')
                                    ->acceptedFileTypes(['image/*', 'application/pdf'])
                                    ->downloadable()
                                    ->openable()
                                    ->visible(fn($get) => $get('wali_type') === 'other'),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        $canModify = $user->hasAnyRole(['Administrator', 'Petugas']);

        return $table
            ->recordUrl(null)
            ->poll('5s')
            ->columns([
                Tables\Columns\TextColumn::make('registration_number')
                    ->label('No. Pendaftaran')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama Santri')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('destination_types')
                    ->label('Tujuan')
                    ->getStateUsing(function (Student $record) {
                        $types = ['Pondok', 'Madrasah'];
                        $dest = $record->registration?->destinationInstitution;
                        if ($dest) {
                            $typeLabel = match ($dest->type) {
                                'smp' => 'SMP',
                                'ma' => 'MA',
                                'mts_external' => 'MTs',
                                default => null,
                            };
                            if ($typeLabel) {
                                $types[] = $typeLabel;
                            }
                        }
                        return implode(', ', $types);
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'verified' => 'warning',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft' => 'Draft / Menunggu',
                        'verified' => 'Terverifikasi',
                        'accepted' => 'Diterima',
                        'rejected' => 'Ditolak',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('total_bill')
                    ->label('Total Tagihan')
                    ->getStateUsing(fn(Student $record) => $record->bills->sum('amount'))
                    ->formatStateUsing(fn($state) => 'Rp. ' . number_format($state, 0, ',', ',')),
                Tables\Columns\TextColumn::make('total_paid')
                    ->label('Sudah Bayar')
                    ->getStateUsing(fn(Student $record) => $record->bills->sum('amount') - $record->bills->sum('remaining_amount'))
                    ->formatStateUsing(fn($state) => 'Rp. ' . number_format($state, 0, ',', ','))
                    ->color('success'),
                Tables\Columns\TextColumn::make('total_remaining')
                    ->label('Belum Bayar')
                    ->getStateUsing(fn(Student $record) => $record->bills->sum('remaining_amount'))
                    ->formatStateUsing(fn($state) => 'Rp. ' . number_format($state, 0, ',', ','))
                    ->color(fn($state) => $state > 0 ? 'danger' : 'success'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Menunggu',
                        'verified' => 'Terverifikasi',
                        'accepted' => 'Diterima',
                        'rejected' => 'Ditolak',
                    ]),
                Tables\Filters\SelectFilter::make('destination_institution_id')
                    ->label('Sekolah')
                    ->relationship('registration.destinationInstitution', 'name'),
            ])
            ->actions($canModify ? [
                EditAction::make(),
                ActionGroup::make([
                    Action::make('verify')
                        ->label('Verifikasi')
                        ->icon('heroicon-o-check')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn(Student $record) => $record->update(['status' => 'verified'])),

                    Action::make('accept')
                        ->label('Terima')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn(Student $record) => $record->update(['status' => 'accepted'])),

                    Action::make('reject')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn(Student $record) => $record->update(['status' => 'rejected'])),
                ]),
            ] : [])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('export_excel')
                        ->label('Export Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) {
                            return \Maatwebsite\Excel\Facades\Excel::download(
                                new \App\Exports\StudentsExport($records),
                                'data-santri-' . now()->format('Y-m-d') . '.xlsx'
                            );
                        }),
                    BulkAction::make('download_documents')
                        ->label('Download Berkas')
                        ->icon('heroicon-o-folder-arrow-down')
                        ->action(function (Collection $records) {
                            $zipFileName = 'berkas-santri-' . now()->format('Y-m-d-His') . '.zip';
                            $zipPath = storage_path('app/temp/' . $zipFileName);

                            if (!file_exists(storage_path('app/temp'))) {
                                mkdir(storage_path('app/temp'), 0755, true);
                            }

                            $zip = new ZipArchive();
                            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                                throw new \Exception('Cannot create zip file');
                            }

                            foreach ($records as $student) {
                                $folderName = preg_replace('/[^a-zA-Z0-9\s]/', '', $student->full_name);
                                $folderName = trim($folderName);

                                foreach ($student->documents as $doc) {
                                    $filePath = Storage::disk('local')->path($doc->file_path);
                                    if (file_exists($filePath)) {
                                        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                                        $docType = ucfirst(str_replace('_', ' ', $doc->type));
                                        $fileName = "{$docType} - {$student->full_name}.{$extension}";
                                        $zip->addFile($filePath, "{$folderName}/{$fileName}");
                                    }
                                }
                            }

                            $zip->close();

                            return response()->download($zipPath)->deleteFileAfterSend(true);
                        }),
                    DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()->hasRole('Administrator')),
                ]),
            ]);
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery()->with(['registration.destinationInstitution', 'documents', 'bills', 'parents']);
        $user = auth()->user();

        if ($user->hasRole(['Administrator', 'Petugas', 'Bendahara Pondok'])) {
            return $query;
        }

        if ($user->institution_id) {
            $institution = Institution::find($user->institution_id);

            if ($user->hasRole('Bendahara Unit')) {
                // For Pondok/Madrasah: show all students (they all have bills for these)
                if ($institution && in_array($institution->type, ['pondok', 'madrasah'])) {
                    return $query->whereHas('bills', function ($q) use ($user) {
                        $q->where('institution_id', $user->institution_id);
                    });
                }
                // For SMP/MA: filter by destination_institution_id
                return $query->whereHas('registration', function ($q) use ($user) {
                    $q->where('destination_institution_id', $user->institution_id);
                });
            }

            if ($user->hasRole('Kepala')) {
                // For Pondok/Madrasah: show all students
                if ($institution && in_array($institution->type, ['pondok', 'madrasah'])) {
                    return $query->whereHas('bills', function ($q) use ($user) {
                        $q->where('institution_id', $user->institution_id);
                    });
                }
                // For SMP/MA: filter by destination_institution_id
                return $query->whereHas('registration', function ($q) use ($user) {
                    $q->where('destination_institution_id', $user->institution_id);
                });
            }
        }

        return $query;
    }
}
