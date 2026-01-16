# Dokumentasi Sistem Keuangan PSB

## Arsitektur

Sistem keuangan PSB menggunakan **Hybrid Cash Collection & Manual Settlement** yang menggabungkan:

1. **Priority Algorithm** - Perhitungan hak dana per lembaga
2. **Hybrid Cash Collection** - Tracking lokasi uang fisik (Panitia vs Unit)
3. **Manual Settlement** - Workflow 3 langkah untuk distribusi dana

---

## Model Database

### 1. Bill (Tagihan)

Tagihan per santri per lembaga.

| Field | Type | Deskripsi |
|-------|------|-----------|
| student_id | FK | Santri pemilik tagihan |
| institution_id | FK | Lembaga tujuan (Pondok, SMP, MA, dll) |
| amount | decimal | Total tagihan |
| remaining_amount | decimal | Sisa tagihan |
| status | enum | unpaid, partial, paid |
| description | text | Rincian komponen biaya |

**Dibuat otomatis** saat santri mendaftar via `Student::generateBills()`.

---

### 2. Transaction (Pembayaran Masuk)

Record pembayaran dari santri.

| Field | Type | Deskripsi |
|-------|------|-----------|
| student_id | FK | Santri yang membayar |
| user_id | FK | Petugas yang menginput |
| amount | decimal | Jumlah bayar |
| payment_method | enum | cash, transfer |
| transaction_date | date | Tanggal bayar |
| **payment_location** | enum | PANITIA, UNIT |
| **is_settled** | boolean | Sudah didistribusikan? |
| verification_token | string | Token untuk verifikasi online |

**payment_location** ditentukan otomatis:
- Admin/Bendahara Pondok/Petugas → `PANITIA`
- Bendahara Unit → `UNIT`

---

### 3. FundTransfer (Distribusi Dana)

Record perpindahan dana dari Panitia ke Unit.

| Field | Type | Deskripsi |
|-------|------|-----------|
| institution_id | FK | Lembaga tujuan |
| student_id | FK | Santri terkait |
| bill_id | FK | Tagihan yang dibayar |
| transaction_id | FK | Transaksi asal |
| amount | decimal | Jumlah transfer |
| **status** | enum | PENDING, APPROVED, COMPLETED, REJECTED |
| approved_at/by | timestamp/FK | Info approval |
| received_at/by | timestamp/FK | Info penerimaan |

---

## Alur Kerja

### A. Input Pembayaran

```
┌─────────────────────────────────────────────────────────────────┐
│                     SANTRI BAYAR                                │
└─────────────────────┬───────────────────────────────────────────┘
                      │
           ┌──────────┴──────────┐
           ▼                     ▼
    ┌──────────────┐      ┌──────────────┐
    │   PANITIA    │      │    UNIT      │
    │ (Admin/BdP)  │      │   (BdU)      │
    └──────┬───────┘      └──────┬───────┘
           │                     │
           ▼                     ▼
    ┌──────────────┐      ┌──────────────┐
    │is_settled=0  │      │is_settled=1  │
    │No FundTransfer│     │Auto COMPLETED│
    │ "Floating"   │      │ FundTransfer │
    └──────────────┘      └──────────────┘
```

### B. Settlement Workflow (3 Langkah)

```
STEP 1            STEP 2           STEP 3
┌────────┐       ┌────────┐       ┌─────────┐
│PENDING │──────▶│APPROVED│──────▶│COMPLETED│
└────────┘       └────────┘       └─────────┘
 Bendahara        Kepala          Bendahara
  Pondok                            Unit
```

1. **PENDING**: Bendahara Pondok membuat request distribusi
2. **APPROVED**: Kepala menyetujui
3. **COMPLETED**: Bendahara Unit konfirmasi terima

---

## Priority Algorithm

Perhitungan hak dana per lembaga berdasarkan total pembayaran:

```
Total Bayar Santri
       │
       ▼
┌─────────────────────────────────┐
│ PRIORITAS 1: MADRASAH (100%)    │
└─────────────────────────────────┘
       │ Sisa
       ▼
┌─────────────────────────────────┐
│ 50:50 SEKOLAH & PONDOK          │
│ • Sekolah max = tagihan sekolah │
│ • Overflow ke Pondok            │
└─────────────────────────────────┘
```

**Contoh:**
- Bayar: Rp 10.000.000
- Tagihan Madrasah: Rp 3.000.000 → dapat Rp 3.000.000
- Sisa Rp 7.000.000 → 50:50
- Tagihan SMP: Rp 2.000.000 → dapat Rp 2.000.000 (max)
- Pondok: Rp 3.500.000 + Rp 1.500.000 overflow = Rp 5.000.000

---

## Roles & Permissions

| Role | Input Transaksi | Buat Distribusi | Approve | Terima Dana |
|------|-----------------|-----------------|---------|-------------|
| Administrator | ✅ | ✅ | ✅ | ✅ |
| Bendahara Pondok | ✅ (Panitia) | ✅ | ❌ | ✅ (Pondok) |
| Bendahara Unit | ✅ (Unit) | ❌ | ❌ | ✅ (Unit-nya) |
| Kepala Pondok | ❌ | ❌ | ✅ | ❌ |
| Kepala Unit | ❌ | ❌ | ❌ | ❌ |
| Petugas | ✅ (Panitia) | ❌ (View Only) | ❌ | ❌ |

**Catatan:**
- Approval distribusi **hanya** oleh Kepala Pondok (bukan kepala unit lain)
- Penerimaan dana **hanya** oleh Bendahara tujuan (match institution_id)

---

## API / Methods

### Transaction
```php
$transaction->isAtPanitia()   // Cek lokasi PANITIA
$transaction->isAtUnit()      // Cek lokasi UNIT
$transaction->markAsSettled() // Tandai sudah disetor
$transaction->getPaymentLocationLabel() // Label lokasi (Panitia/Bendahara X)
```

### FundTransfer
```php
$transfer->isPending()        // Status PENDING?
$transfer->approve($user)     // Approve (Kepala)
$transfer->confirmReceipt($user) // Terima (BdU)
$transfer->reject()           // Tolak
```

### Bill
```php
$bill->applyPayment($amount)  // Aplikasikan pembayaran
```

### PaymentDistributionService
```php
$service->calculateStudentEntitlement($student) // Hitung hak per lembaga
$service->createSettlementRequests($institution, $user) // Buat PENDING transfers
$service->getFloatingCashAtPanitia() // Total kas mengendap
$service->getCashAtUnit($institution) // Kas di unit
```

---

## Dashboard Widget

Menampilkan:
1. **Kas Mengendap di Panitia** - Dana belum didistribusikan
2. **Proses Distribusi Berjalan** - PENDING/APPROVED transfers
3. **Hak Dana Per Lembaga** - Hasil priority algorithm

---

## Referensi File

### Models
| File | Deskripsi |
|------|-----------|
| [Bill.php](file:///c:/laragon/www/psb/app/Models/Bill.php) | Model tagihan per lembaga per santri |
| [Transaction.php](file:///c:/laragon/www/psb/app/Models/Transaction.php) | Model pembayaran masuk |
| [FundTransfer.php](file:///c:/laragon/www/psb/app/Models/FundTransfer.php) | Model distribusi dana |
| [Student.php](file:///c:/laragon/www/psb/app/Models/Student.php) | Model santri (punya bills, transactions) |
| [Institution.php](file:///c:/laragon/www/psb/app/Models/Institution.php) | Model lembaga (punya bills, fundTransfers) |
| [FeeComponent.php](file:///c:/laragon/www/psb/app/Models/FeeComponent.php) | Model komponen biaya per lembaga |

### Filament Resources (Admin Panel)
| File | Deskripsi |
|------|-----------|
| [TransactionResource.php](file:///c:/laragon/www/psb/app/Filament/Resources/TransactionResource.php) | CRUD Transaksi |
| [CreateTransaction.php](file:///c:/laragon/www/psb/app/Filament/Resources/TransactionResource/Pages/CreateTransaction.php) | Halaman create transaksi (hybrid logic) |
| [ListTransactions.php](file:///c:/laragon/www/psb/app/Filament/Resources/TransactionResource/Pages/ListTransactions.php) | List transaksi + header action |
| [FundTransferResource.php](file:///c:/laragon/www/psb/app/Filament/Resources/FundTransferResource.php) | CRUD Distribusi Dana |
| [ListFundTransfers.php](file:///c:/laragon/www/psb/app/Filament/Resources/FundTransferResource/Pages/ListFundTransfers.php) | List distribusi + buat distribusi baru |
| [StudentResource.php](file:///c:/laragon/www/psb/app/Filament/Resources/StudentResource.php) | Resource santri (termasuk info tagihan) |
| [FeeComponentResource.php](file:///c:/laragon/www/psb/app/Filament/Resources/FeeComponentResource.php) | CRUD komponen biaya |

### Services
| File | Deskripsi |
|------|-----------|
| [PaymentDistributionService.php](file:///c:/laragon/www/psb/app/Services/PaymentDistributionService.php) | Priority Algorithm & floating cash calculation |

### Widgets
| File | Deskripsi |
|------|-----------|
| [FundSummaryWidget.php](file:///c:/laragon/www/psb/app/Filament/Widgets/FundSummaryWidget.php) | Widget rekap kas mengendap |
| [BaseFinanceStatsWidget.php](file:///c:/laragon/www/psb/app/Filament/Widgets/BaseFinanceStatsWidget.php) | Base class statistik keuangan |
| [GlobalFinanceStatsWidget.php](file:///c:/laragon/www/psb/app/Filament/Widgets/GlobalFinanceStatsWidget.php) | Statistik keuangan global |
| [PondokFinanceStatsWidget.php](file:///c:/laragon/www/psb/app/Filament/Widgets/PondokFinanceStatsWidget.php) | Statistik keuangan Pondok |
| [SmpFinanceStatsWidget.php](file:///c:/laragon/www/psb/app/Filament/Widgets/SmpFinanceStatsWidget.php) | Statistik keuangan SMP |
| [MaFinanceStatsWidget.php](file:///c:/laragon/www/psb/app/Filament/Widgets/MaFinanceStatsWidget.php) | Statistik keuangan MA |
| [MadrasahFinanceStatsWidget.php](file:///c:/laragon/www/psb/app/Filament/Widgets/MadrasahFinanceStatsWidget.php) | Statistik keuangan Madrasah |
| [RegistrationStatsWidget.php](file:///c:/laragon/www/psb/app/Filament/Widgets/RegistrationStatsWidget.php) | Statistik pendaftaran santri |

### Controllers
| File | Deskripsi |
|------|-----------|
| [ReceiptController.php](file:///c:/laragon/www/psb/app/Http/Controllers/ReceiptController.php) | Cetak & verifikasi nota transaksi |
| [TransactionExportController.php](file:///c:/laragon/www/psb/app/Http/Controllers/TransactionExportController.php) | Export PDF transaksi |

### Livewire Components
| File | Deskripsi |
|------|-----------|
| [CheckStatus.php](file:///c:/laragon/www/psb/app/Livewire/CheckStatus.php) | Cek status pembayaran santri (public) |
| [RegistrationWizard.php](file:///c:/laragon/www/psb/app/Livewire/RegistrationWizard.php) | Wizard pendaftaran (generate bills) |

### Views (Blade)
| File | Deskripsi |
|------|-----------|
| [check-status.blade.php](file:///c:/laragon/www/psb/resources/views/livewire/check-status.blade.php) | UI cek status pembayaran |
| [transaction.blade.php](file:///c:/laragon/www/psb/resources/views/receipts/transaction.blade.php) | Template nota transaksi |
| [verify.blade.php](file:///c:/laragon/www/psb/resources/views/receipts/verify.blade.php) | Halaman verifikasi nota |
| [header.blade.php](file:///c:/laragon/www/psb/resources/views/filament/resources/fund-transfer/header.blade.php) | Widget rekap distribusi dana |
| [detail-modal.blade.php](file:///c:/laragon/www/psb/resources/views/filament/resources/fund-transfer/detail-modal.blade.php) | Modal detail distribusi |
| [transaction-history.blade.php](file:///c:/laragon/www/psb/resources/views/filament/modals/transaction-history.blade.php) | Modal riwayat transaksi |
| [transactions-pdf.blade.php](file:///c:/laragon/www/psb/resources/views/exports/transactions-pdf.blade.php) | Template export PDF transaksi |

### Migrations

| File | Deskripsi |
|------|-----------|
| [2025_03_01_000004_create_finance_tables.php](file:///c:/laragon/www/psb/database/migrations/2025_03_01_000004_create_finance_tables.php) | Tabel `bills` (student_id, institution_id, amount, remaining_amount, status, description) dan `transactions` (student_id, user_id, amount, payment_method, transaction_date, proof_image, notes, verification_token, payment_location, is_settled) |
| [2025_03_01_000007_create_fund_transfers_table.php](file:///c:/laragon/www/psb/database/migrations/2025_03_01_000007_create_fund_transfers_table.php) | Tabel `fund_transfers` (institution_id, student_id, bill_id, transaction_id, user_id, amount, transfer_date, transfer_method, notes, status, approved_at/by, received_at/by) |

### Observers & Exports
| File | Deskripsi |
|------|-----------|
| [TransactionObserver.php](file:///c:/laragon/www/psb/app/Observers/TransactionObserver.php) | Observer untuk transaksi |
| [FeeComponentObserver.php](file:///c:/laragon/www/psb/app/Observers/FeeComponentObserver.php) | Observer untuk update tagihan saat fee berubah |
| [TransactionsExport.php](file:///c:/laragon/www/psb/app/Exports/TransactionsExport.php) | Export Excel transaksi |
