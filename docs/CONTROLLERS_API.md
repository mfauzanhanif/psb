# Controllers & API Routes

> Dokumentasi lengkap tentang HTTP Controllers dan Web Routes.

**Dokumentasi Terkait:**
- [← Kembali ke README](../README.md)
- [Filament Resources](./FILAMENT_RESOURCES.md)
- [Exports & Reports](./EXPORTS_REPORTS.md)

---

## Overview

Aplikasi menggunakan kombinasi:
- **Livewire Components** - Untuk halaman publik interaktif
- **HTTP Controllers** - Untuk file downloads, PDF generation, dan API endpoints
- **Filament Resources** - Untuk admin panel CRUD

---

## Web Routes

**File**: `routes/web.php`

### Public Routes (Tanpa Login)

| Route | Method | Controller/Component | Deskripsi |
|-------|--------|---------------------|-----------|
| `/` | GET | `Home::class` | Landing page |
| `/daftar` | GET | `RegistrationWizard::class` | Form pendaftaran |
| `/cek-status` | GET | `CheckStatus::class` | Cek status pendaftaran |
| `/transaksi/{token}` | GET | `ReceiptController@publicDownload` | Download kwitansi via token |
| `/transaksi/verify/{token}` | GET | `ReceiptController@verify` | Halaman verifikasi kwitansi |
| `/registrasi/{student}/pdf` | GET | `RegistrationPdfController@download` | Download PDF pendaftaran |

### Admin Routes (Require Auth)

| Route | Method | Controller | Deskripsi |
|-------|--------|------------|-----------|
| `/admin/transaksi/{transaction}/cetak` | GET | `ReceiptController@adminPrint` | Cetak kwitansi |
| `/transactions/export/pdf` | GET | `TransactionExportController@pdf` | Export PDF transaksi |
| `/dokumen/{document}` | GET | `DocumentController@show` | Lihat dokumen santri |

### Filament Routes (Auto-generated)

| Route | Deskripsi |
|-------|-----------|
| `/admin` | Dashboard admin |
| `/admin/students` | Kelola data santri |
| `/admin/transactions` | Kelola transaksi |
| `/admin/fund-transfers` | Kelola distribusi dana |
| `/admin/fee-components` | Kelola komponen biaya |
| `/admin/institutions` | Kelola lembaga |
| `/admin/academic-years` | Kelola tahun ajaran |
| `/admin/users` | Kelola user |

---

## Controllers

### 1. ReceiptController

**File**: `app/Http/Controllers/ReceiptController.php`

Menangani generate dan verifikasi kwitansi pembayaran.

#### Methods

##### `adminPrint(Transaction $transaction)`
Cetak kwitansi dari admin panel.

```php
// Route: /admin/transaksi/{transaction}/cetak
// Middleware: auth
// Return: PDF download
```

##### `publicDownload(string $token)`
Download kwitansi via token unik (untuk QR code).

```php
// Route: /transaksi/{token}
// Middleware: none (public)
// Return: PDF download
```

##### `verify(string $token)`
Halaman verifikasi keaslian kwitansi.

```php
// Route: /transaksi/verify/{token}
// Middleware: none (public)
// Return: View (verify-receipt)
```

##### `generateReceipt(Transaction $transaction)` (protected)
Generate PDF kwitansi dengan QR code.

**Features:**
- QR code untuk verifikasi
- Data santri lengkap
- Total pembayaran, tagihan, dan sisa
- Format A5 portrait

```php
// Output: PDF file
// Paper: A5
// Filename: nota-{registration_number}-{date}.pdf
```

#### Contoh Penggunaan

```php
// Di Filament Resource (sebagai action)
Tables\Actions\Action::make('print')
    ->url(fn (Transaction $record) => 
        route('transaksi.cetak', $record)
    )
    ->openUrlInNewTab();

// Public link (untuk QR code)
$transaction->getVerifyUrl(); // Returns: /transaksi/verify/{token}
```

---

### 2. DocumentController

**File**: `app/Http/Controllers/DocumentController.php`

Menangani akses dokumen santri yang di-upload.

#### Methods

##### `show(StudentDocument $document)`
Menampilkan file dokumen.

```php
// Route: /dokumen/{document}
// Middleware: auth
// Return: File response
```

**Authorization:**
- Hanya user yang login
- Bisa ditambahkan permission check

```php
public function show(StudentDocument $document)
{
    $user = auth()->user();
    
    if (!$user) {
        abort(403, 'Unauthorized');
    }
    
    // Optional: permission check
    // if (!$user->can('view_student_documents')) {
    //     abort(403);
    // }
    
    return response()->file(
        Storage::disk('local')->path($document->file_path)
    );
}
```

---

### 3. RegistrationPdfController

**File**: `app/Http/Controllers/RegistrationPdfController.php`

Generate PDF formulir pendaftaran.

#### Methods

##### `download(Student $student)`
Download PDF pendaftaran santri.

```php
// Route: /registrasi/{student}/pdf
// Middleware: none
// Return: PDF download
```

**Data yang di-load:**
- Data santri
- Data orang tua (ayah, ibu, wali)
- Data pendaftaran
- Lembaga tujuan
- Tahun ajaran

```php
// Output: PDF file
// Paper: A4
// Filename: Pendaftaran-{registration_number}.pdf
```

#### View Template
```
resources/views/pdf/forms/registration.blade.php
```

---

### 4. TransactionExportController

**File**: `app/Http/Controllers/TransactionExportController.php`

Export laporan transaksi dalam format PDF.

#### Methods

##### `pdf(Request $request)`
Generate PDF laporan transaksi.

```php
// Route: /transactions/export/pdf
// Middleware: auth
// Query params: start_date, end_date
// Return: PDF stream (inline)
```

**Parameters:**
| Param | Type | Required | Deskripsi |
|-------|------|----------|-----------|
| `start_date` | date | No | Filter dari tanggal |
| `end_date` | date | No | Filter sampai tanggal |

**Example:**
```
/transactions/export/pdf?start_date=2026-01-01&end_date=2026-01-31
```

#### View Template
```
resources/views/pdf/reports/transactions.blade.php
```

---

## Livewire Components

### Overview

| Component | File | Route | Deskripsi |
|-----------|------|-------|-----------|
| Home | `app/Livewire/Home.php` | `/` | Landing page |
| RegistrationWizard | `app/Livewire/RegistrationWizard.php` | `/daftar` | Multi-step form |
| CheckStatus | `app/Livewire/CheckStatus.php` | `/cek-status` | Cek status |

### Home Component

Landing page dengan:
- Hero section
- Fitur-fitur
- Informasi biaya
- CTA pendaftaran

### RegistrationWizard Component

Form pendaftaran 5 langkah:

```
Step 1: Biodata Santri
    ├── Nama, NIK, NISN
    ├── Tempat & Tanggal Lahir
    ├── Alamat (cascade dropdown)
    └── Anak ke-, Jumlah saudara

Step 2: Data Orang Tua
    ├── Data Ayah
    ├── Data Ibu
    └── Data Wali (opsional)

Step 3: Data Pendaftaran
    ├── Sekolah asal
    ├── Lembaga tujuan
    └── Sumber pembiayaan

Step 4: Upload Dokumen
    ├── Foto
    ├── Kartu Keluarga
    ├── Akta Kelahiran
    └── Ijazah/SKL

Step 5: Konfirmasi & Submit
```

### CheckStatus Component

Cek status pendaftaran dengan:
- Input nomor pendaftaran
- Tampilkan status santri
- Info pembayaran
- Sisa tagihan

---

## View Templates

### PDF Templates

| Template | Path | Deskripsi |
|----------|------|-----------|
| Kwitansi | `pdf/receipts/transaction.blade.php` | Nota pembayaran A5 |
| Pendaftaran | `pdf/forms/registration.blade.php` | Formulir pendaftaran A4 |
| Laporan Transaksi | `pdf/reports/transactions.blade.php` | Laporan transaksi A4 |

### Verification Page

| Template | Path | Deskripsi |
|----------|------|-----------|
| Verify Receipt | `livewire/verify-receipt.blade.php` | Halaman verifikasi kwitansi |

---

## API Response Formats

### PDF Response

```php
return response($output, 200)
    ->header('Content-Type', 'application/pdf')
    ->header('Content-Disposition', 'attachment; filename="file.pdf"');
```

### File Response

```php
return response()->file($filePath);
```

### Stream Response (Inline PDF)

```php
return $dompdf->stream($filename, ['Attachment' => false]);
```

---

## Error Handling

### 403 Forbidden
```php
abort(403, 'Unauthorized');
```

### 404 Not Found
```php
abort(404);
// atau
$transaction = Transaction::where('verification_token', $token)->firstOrFail();
```

---

## File Reference

```
app/Http/Controllers/
├── Controller.php            # Base controller
├── DocumentController.php    # Akses dokumen
├── ReceiptController.php     # Kwitansi
├── RegistrationPdfController.php  # PDF pendaftaran
└── TransactionExportController.php # Export transaksi

app/Livewire/
├── Home.php                  # Landing page
├── RegistrationWizard.php    # Form pendaftaran
└── CheckStatus.php           # Cek status

routes/
└── web.php                   # Semua routes
```

---

*Dokumentasi ini terakhir diperbarui pada: Januari 2026*
