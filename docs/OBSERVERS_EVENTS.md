# Observers & Events

> Dokumentasi lengkap tentang Model Observers dan Event handling.

**Dokumentasi Terkait:**
- [← Kembali ke README](../README.md)
- [Database Schema](./DATABASE_SCHEMA.md)
- [Status & Workflows](./STATUS_WORKFLOWS.md)

---

## Overview

Observers digunakan untuk men-trigger aksi otomatis saat model events terjadi (created, updated, deleted, dll).

**Package**: Laravel Model Observers (built-in)

---

## Daftar Observers

| Observer | Model | Deskripsi |
|----------|-------|-----------|
| `TransactionObserver` | Transaction | Auto-generate verification token |
| `FeeComponentObserver` | FeeComponent | Recalculate bills saat fee berubah |

**Lokasi**: `app/Observers/`

---

## TransactionObserver

**File**: `app/Observers/TransactionObserver.php`

### Deskripsi
Observer untuk model Transaction. Auto-generate verification token untuk kwitansi.

### Events

#### `created(Transaction $transaction)`

Dipanggil saat transaksi baru dibuat.

**Action:**
- Generate `verification_token` (16 karakter random)
- Token digunakan untuk QR code verifikasi kwitansi

```php
public function created(Transaction $transaction): void
{
    if (empty($transaction->verification_token)) {
        $transaction->updateQuietly([
            'verification_token' => Str::random(16)
        ]);
    }
}
```

**Catatan:**
- Menggunakan `updateQuietly()` untuk menghindari infinite loop
- Token hanya di-generate jika belum ada

### Penggunaan Token

```php
// Di Transaction model
public function getVerifyUrl(): string
{
    return route('transaksi.verify', $this->verification_token);
}

// Output URL: /transaksi/verify/aB1cD2eF3gH4iJ5k
```

---

## FeeComponentObserver

**File**: `app/Observers/FeeComponentObserver.php`

### Deskripsi
Observer untuk model FeeComponent. Otomatis recalculate tagihan (bills) saat komponen biaya berubah.

### Events

#### `created(FeeComponent $feeComponent)`

Dipanggil saat fee component baru dibuat.

```php
public function created(FeeComponent $feeComponent): void
{
    $this->recalculateBillsForInstitution($feeComponent->institution_id);
}
```

#### `updated(FeeComponent $feeComponent)`

Dipanggil saat fee component diupdate.

```php
public function updated(FeeComponent $feeComponent): void
{
    $this->recalculateBillsForInstitution($feeComponent->institution_id);
}
```

#### `deleted(FeeComponent $feeComponent)`

Dipanggil saat fee component dihapus.

```php
public function deleted(FeeComponent $feeComponent): void
{
    $this->recalculateBillsForInstitution($feeComponent->institution_id);
}
```

### Helper Method

```php
protected function recalculateBillsForInstitution(int $institutionId): void
{
    $bills = Bill::where('institution_id', $institutionId)->get();

    foreach ($bills as $bill) {
        $bill->recalculateAmount();
    }
}
```

### Flow Diagram

```
FeeComponent Created/Updated/Deleted
            │
            ▼
┌───────────────────────────┐
│  FeeComponentObserver     │
│  recalculateBills()       │
└───────────────────────────┘
            │
            ▼
┌───────────────────────────┐
│  Get all Bills for        │
│  institution_id           │
└───────────────────────────┘
            │
            ▼
┌───────────────────────────┐
│  Bill::recalculateAmount()│
│  untuk setiap bill        │
└───────────────────────────┘
            │
            ▼
┌───────────────────────────┐
│  Bills updated dengan     │
│  amount terbaru           │
└───────────────────────────┘
```

### Contoh Skenario

**Skenario**: Admin mengubah biaya pendaftaran SMP dari Rp 2.000.000 menjadi Rp 2.500.000

1. Admin edit FeeComponent di admin panel
2. `updated()` event trigger
3. Observer ambil semua bills untuk SMP
4. Setiap bill di-recalculate
5. Santri yang sudah daftar SMP langsung melihat tagihan terupdate

---

## Registrasi Observer

Observers didaftarkan di `AppServiceProvider` atau menggunakan attribute:

### Via AppServiceProvider

```php
// app/Providers/AppServiceProvider.php

use App\Models\Transaction;
use App\Models\FeeComponent;
use App\Observers\TransactionObserver;
use App\Observers\FeeComponentObserver;

public function boot(): void
{
    Transaction::observe(TransactionObserver::class);
    FeeComponent::observe(FeeComponentObserver::class);
}
```

### Via Attribute (Laravel 10+)

```php
// app/Models/Transaction.php

use App\Observers\TransactionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([TransactionObserver::class])]
class Transaction extends Model
{
    // ...
}
```

---

## Model Events Reference

### Available Events

| Event | Timing |
|-------|--------|
| `retrieved` | Setelah model di-retrieve dari database |
| `creating` | Sebelum model dibuat (bisa cancel) |
| `created` | Setelah model dibuat |
| `updating` | Sebelum model diupdate (bisa cancel) |
| `updated` | Setelah model diupdate |
| `saving` | Sebelum create/update (bisa cancel) |
| `saved` | Setelah create/update |
| `deleting` | Sebelum model dihapus (bisa cancel) |
| `deleted` | Setelah model dihapus |
| `restoring` | Sebelum soft-deleted model di-restore |
| `restored` | Setelah soft-deleted model di-restore |
| `forceDeleting` | Sebelum force delete |
| `forceDeleted` | Setelah force delete |

### Prevent Infinite Loops

Gunakan `updateQuietly()` atau `saveQuietly()` untuk update tanpa men-trigger observer lagi:

```php
// ❌ Bisa menyebabkan infinite loop
$transaction->update(['verification_token' => 'xxx']);

// ✅ Tidak trigger observer
$transaction->updateQuietly(['verification_token' => 'xxx']);
```

---

## Potential Future Observers

### StudentObserver (belum diimplementasi)

```php
class StudentObserver
{
    public function created(Student $student): void
    {
        // Kirim notifikasi WhatsApp ke wali
        // Generate nomor pendaftaran (jika belum)
    }
    
    public function updated(Student $student): void
    {
        // Jika status berubah, kirim notifikasi
        if ($student->wasChanged('status')) {
            // Notify wali santri
        }
    }
}
```

### BillObserver (belum diimplementasi)

```php
class BillObserver
{
    public function updated(Bill $bill): void
    {
        // Jika status jadi 'paid', update student status
        if ($bill->status === 'paid') {
            // Check if all bills paid
        }
    }
}
```

---

## Queue Events (Future)

Untuk operasi berat, gunakan queued listeners:

```php
// app/Listeners/SendPaymentNotification.php

class SendPaymentNotification implements ShouldQueue
{
    public function handle(TransactionCreated $event): void
    {
        $fonnte = new FonnteService();
        $fonnte->send(
            $event->transaction->student->parents->first()->phone_number,
            "Pembayaran sebesar {$event->transaction->amount} telah diterima."
        );
    }
}
```

---

## File Reference

```
app/Observers/
├── FeeComponentObserver.php   # Recalculate bills
└── TransactionObserver.php    # Generate verification token

app/Providers/
└── AppServiceProvider.php     # Register observers
```

---

## Best Practices

### 1. Keep Observers Focused
Satu observer untuk satu model, satu concern.

### 2. Use Queues for Heavy Operations
Jangan blocking request dengan operasi berat (email, WhatsApp, dll).

### 3. Avoid Business Logic Complexity
Jika logika terlalu kompleks, pindahkan ke Service class.

### 4. Test Observers
Pastikan test case mencakup observer behavior.

```php
public function test_transaction_gets_verification_token(): void
{
    $transaction = Transaction::factory()->create();
    
    $this->assertNotNull($transaction->verification_token);
    $this->assertEquals(16, strlen($transaction->verification_token));
}
```

---

*Dokumentasi ini terakhir diperbarui pada: Januari 2026*
