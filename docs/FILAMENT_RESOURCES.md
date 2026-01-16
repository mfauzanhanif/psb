# Filament Resources & Widgets

> Dokumentasi lengkap tentang panel admin yang dibangun dengan Filament.

**Dokumentasi Terkait:**
- [← Kembali ke README](../README.md)
- [Role & Permissions](./ROLES_PERMISSIONS.md)
- [Status & Workflows](./STATUS_WORKFLOWS.md)

---

## Admin Panel Resources

### Overview

Resources adalah CRUD interface untuk mengelola data di admin panel.

| Resource | File | Fungsi |
|----------|------|--------|
| **StudentResource** | `StudentResource.php` | Data santri, biodata, dokumen |
| **TransactionResource** | `TransactionResource.php` | Transaksi pembayaran |
| **FundTransferResource** | `FundTransferResource.php` | Distribusi dana |
| **FeeComponentResource** | `FeeComponentResource.php` | Komponen biaya |
| **InstitutionResource** | `InstitutionResource.php` | Data lembaga |
| **AcademicYearResource** | `AcademicYearResource.php` | Tahun ajaran |
| **UserResource** | `UserResource.php` | User admin |

**Lokasi**: `app/Filament/Resources/`

---

## StudentResource

### Deskripsi
Mengelola data santri termasuk biodata, orang tua, pendaftaran, dan dokumen.

### Fitur Utama
- **Form multi-tab**: Biodata, Orang Tua, Pendaftaran, Dokumen
- **Integrasi WilayahService**: Cascade dropdown alamat
- **Generate tagihan otomatis**: Saat santri dibuat
- **Bulk actions**: Verifikasi, Terima, Tolak
- **Export ZIP**: Download semua dokumen santri
- **Filter**: Status, gender, institution

### Form Tabs

```
┌─────────────────────────────────────────────────────────┐
│  Biodata │ Orang Tua │ Pendaftaran │ Dokumen           │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  - Nama Lengkap                                         │
│  - NIK / NISN                                           │
│  - Tempat & Tanggal Lahir                               │
│  - Jenis Kelamin                                        │
│  - Alamat (Provinsi → Kabupaten → Kecamatan → Desa)    │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### Access Control

```php
public static function canCreate(): bool
{
    return auth()->user()->can('create_students');
}

public static function canEdit(Model $record): bool
{
    return auth()->user()->can('edit_students');
}

public static function canDelete(Model $record): bool
{
    return auth()->user()->can('delete_students');
}
```

### Query Scope

Data difilter berdasarkan institution user:
```php
public static function getEloquentQuery(): Builder
{
    $user = auth()->user();
    
    if ($user->hasRole('Administrator')) {
        return parent::getEloquentQuery();
    }
    
    // Filter by user's institution
    return parent::getEloquentQuery()
        ->whereHas('registration', fn($q) => 
            $q->where('destination_institution_id', $user->institution_id)
        );
}
```

---

## TransactionResource

### Deskripsi
Mengelola transaksi pembayaran santri.

### Fitur Utama
- **Input pembayaran**: Dengan pilihan santri
- **Auto payment_location**: Berdasarkan role user
- **Print kwitansi**: Dengan QR code verifikasi
- **Kirim notifikasi**: WhatsApp ke wali
- **Export**: Excel dan PDF

### Payment Location Logic

```php
// Otomatis ditentukan berdasarkan role user
if (auth()->user()->hasRole(['Administrator', 'Bendahara Pondok', 'Petugas'])) {
    $transaction->payment_location = 'PANITIA';
} else {
    $transaction->payment_location = 'UNIT';
}
```

### Table Actions

| Action | Deskripsi | Icon |
|--------|-----------|------|
| Print | Cetak kwitansi | 🖨️ |
| WhatsApp | Kirim notifikasi | 📱 |
| Edit | Edit transaksi | ✏️ |
| Delete | Hapus transaksi | 🗑️ |

---

## FundTransferResource

### Deskripsi
Mengelola distribusi dana dari Panitia ke Unit.

### Fitur Utama
- **3-step workflow**: PENDING → APPROVED → COMPLETED
- **Bulk distribution**: Buat distribusi untuk semua santri
- **Approval tracking**: Siapa & kapan approve
- **Receipt confirmation**: Konfirmasi terima

### Status Actions

| Status | Action | Actor |
|--------|--------|-------|
| PENDING | Approve / Reject | Kepala Pondok |
| APPROVED | Confirm Receipt | Bendahara Unit |
| COMPLETED | - | Final state |

---

## FeeComponentResource

### Deskripsi
Mengelola komponen biaya per lembaga per tahun ajaran.

### Fields
- Institution (dropdown)
- Academic Year (dropdown)
- Nama komponen
- Type (yearly/monthly)
- Amount

### Observer
```php
// FeeComponentObserver.php
// Otomatis update bills saat fee component berubah
public function updated(FeeComponent $feeComponent)
{
    // Regenerate bills for affected students
}
```

---

## Dashboard Widgets

### Overview

Widgets menampilkan statistik di dashboard admin.

| Widget | File | Fungsi |
|--------|------|--------|
| **RegistrationStatsWidget** | `RegistrationStatsWidget.php` | Total pendaftar |
| **FundSummaryWidget** | `FundSummaryWidget.php` | Rekap kas mengendap |
| **GlobalFinanceStatsWidget** | `GlobalFinanceStatsWidget.php` | Statistik global |
| **PondokFinanceStatsWidget** | `PondokFinanceStatsWidget.php` | Keuangan Pondok |
| **SmpFinanceStatsWidget** | `SmpFinanceStatsWidget.php` | Keuangan SMP |
| **MaFinanceStatsWidget** | `MaFinanceStatsWidget.php` | Keuangan MA |
| **MadrasahFinanceStatsWidget** | `MadrasahFinanceStatsWidget.php` | Keuangan Madrasah |

**Lokasi**: `app/Filament/Widgets/`

---

### RegistrationStatsWidget

Menampilkan statistik pendaftar:

```php
protected function getStats(): array
{
    return [
        Stat::make('Total Pendaftar', $total)
            ->description('Semua santri')
            ->color('success'),
            
        Stat::make('Laki-laki', $male)
            ->description('Santri putra')
            ->color('info'),
            
        Stat::make('Perempuan', $female)
            ->description('Santri putri')
            ->color('danger'),
    ];
}
```

**Auto-refresh**: Setiap 5 detik
```php
protected function getPollingInterval(): ?string 
{ 
    return '5s'; 
}
```

---

### FundSummaryWidget

Menampilkan rekap kas per lembaga:

| Lembaga | Hak Dana | Sudah Terima | Pending |
|---------|----------|--------------|---------|
| Pondok | Rp X | Rp Y | Rp Z |
| SMP | Rp X | Rp Y | Rp Z |
| MA | Rp X | Rp Y | Rp Z |
| Madrasah | Rp X | Rp Y | Rp Z |

---

### BaseFinanceStatsWidget

Base class untuk widget keuangan per lembaga.

```php
abstract class BaseFinanceStatsWidget extends StatsOverviewWidget
{
    abstract protected function getInstitutionType(): string;
    
    protected function getStats(): array
    {
        // Total tagihan
        // Total terbayar
        // Sisa tagihan
        // Persentase lunas
    }
}
```

Widget turunan:
- `GlobalFinanceStatsWidget` - Semua lembaga
- `PondokFinanceStatsWidget` - type = 'pondok'
- `SmpFinanceStatsWidget` - type = 'smp'
- `MaFinanceStatsWidget` - type = 'ma'
- `MadrasahFinanceStatsWidget` - type = 'madrasah'

---

## Livewire Components

### Public-Facing Components

| Component | File | Route | Fungsi |
|-----------|------|-------|--------|
| Home | `Home.php` | `/` | Landing page |
| RegistrationWizard | `RegistrationWizard.php` | `/daftar` | Form pendaftaran |
| CheckStatus | `CheckStatus.php` | `/cek-status` | Cek status pendaftaran |

### RegistrationWizard

Form pendaftaran multi-step:

```
Step 1: Biodata Santri
    ↓
Step 2: Data Orang Tua
    ↓
Step 3: Data Pendaftaran
    ↓
Step 4: Upload Dokumen
    ↓
Step 5: Konfirmasi & Submit
```

**Fitur:**
- Validasi per step
- Auto-save progress
- Generate nomor pendaftaran
- Generate bills otomatis
- Kirim notifikasi WhatsApp

---

## File Reference

### Resources
```
app/Filament/Resources/
├── StudentResource.php
├── StudentResource/
│   └── Pages/
│       ├── CreateStudent.php
│       ├── EditStudent.php
│       └── ListStudents.php
├── TransactionResource.php
├── FundTransferResource.php
├── FeeComponentResource.php
├── InstitutionResource.php
├── AcademicYearResource.php
└── UserResource.php
```

### Widgets
```
app/Filament/Widgets/
├── BaseFinanceStatsWidget.php
├── FundSummaryWidget.php
├── GlobalFinanceStatsWidget.php
├── MaFinanceStatsWidget.php
├── MadrasahFinanceStatsWidget.php
├── PondokFinanceStatsWidget.php
├── RegistrationStatsWidget.php
└── SmpFinanceStatsWidget.php
```

### Livewire
```
app/Livewire/
├── Home.php
├── RegistrationWizard.php
└── CheckStatus.php
```

---

*Dokumentasi ini terakhir diperbarui pada: Januari 2026*
