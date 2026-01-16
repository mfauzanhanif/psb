# Exports & Reports

> Dokumentasi lengkap tentang fitur export data dan laporan.

**Dokumentasi Terkait:**
- [← Kembali ke README](../README.md)
- [Controllers & API](./CONTROLLERS_API.md)
- [Filament Resources](./FILAMENT_RESOURCES.md)

---

## Overview

Aplikasi menyediakan fitur export data dalam format:
- **Excel** (.xlsx) - Menggunakan Maatwebsite/Excel
- **PDF** - Menggunakan DomPDF

**Package yang digunakan:**
- `maatwebsite/excel` - Export Excel
- `barryvdh/laravel-dompdf` - Generate PDF

---

## Excel Exports

### Daftar Export Classes

| Class | File | Deskripsi |
|-------|------|-----------|
| `StudentsExport` | `app/Exports/StudentsExport.php` | Export data santri |
| `TransactionsExport` | `app/Exports/TransactionsExport.php` | Export transaksi |

---

### StudentsExport

**File**: `app/Exports/StudentsExport.php`

Export data santri ke Excel.

#### Interface yang diimplementasi

```php
class StudentsExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles
```

#### Constructor

```php
public function __construct(Collection $students)
{
    $this->students = $students;
}
```

#### Kolom Export

| No | Heading | Field |
|----|---------|-------|
| 1 | No. Pendaftaran | `registration_number` |
| 2 | Nama Lengkap | `full_name` |
| 3 | NIK | `nik` |
| 4 | NISN | `nisn` |
| 5 | Jenis Kelamin | `gender` (translated) |
| 6 | Tempat Lahir | `place_of_birth` |
| 7 | Tanggal Lahir | `date_of_birth` (formatted) |
| 8 | Alamat | `address_street` |
| 9 | Desa/Kelurahan | `village` |
| 10 | Kecamatan | `district` |
| 11 | Kabupaten/Kota | `regency` |
| 12 | Provinsi | `province` |
| 13 | Nama Ayah | `parents.father.name` |
| 14 | No. WA Ayah | `parents.father.phone_number` |
| 15 | Nama Ibu | `parents.mother.name` |
| 16 | No. WA Ibu | `parents.mother.phone_number` |
| 17 | Sekolah Tujuan | `registration.destinationInstitution.name` |
| 18 | Status | `status` (translated) |

#### Penggunaan

```php
use App\Exports\StudentsExport;
use Maatwebsite\Excel\Facades\Excel;

// Di Filament Resource (bulk action)
Tables\Actions\BulkAction::make('export')
    ->action(function (Collection $records) {
        return Excel::download(
            new StudentsExport($records),
            'santri-' . now()->format('Y-m-d') . '.xlsx'
        );
    });
```

#### Output

- **Format**: Excel (.xlsx)
- **Filename**: `santri-{date}.xlsx`
- **Header**: Bold (row 1)

---

### TransactionsExport

**File**: `app/Exports/TransactionsExport.php`

Export data transaksi ke Excel.

#### Interface yang diimplementasi

```php
class TransactionsExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles
```

#### Constructor

```php
public function __construct($startDate = null, $endDate = null)
{
    $this->startDate = $startDate;
    $this->endDate = $endDate;
}
```

#### Kolom Export

| No | Heading | Field |
|----|---------|-------|
| 1 | No | Auto-increment |
| 2 | Tanggal | `transaction_date` (d/m/Y) |
| 3 | No. Pendaftaran | `student.registration_number` |
| 4 | Nama Santri | `student.full_name` |
| 5 | Keterangan | "Biaya Pendaftaran" |
| 6 | Jumlah | `amount` |
| 7 | Metode | `payment_method` (translated) |
| 8 | Petugas | `user.name` |
| 9 | Catatan | `notes` |

#### Penggunaan

```php
use App\Exports\TransactionsExport;
use Maatwebsite\Excel\Facades\Excel;

// Export semua transaksi
return Excel::download(
    new TransactionsExport(),
    'transaksi.xlsx'
);

// Export dengan filter tanggal
return Excel::download(
    new TransactionsExport('2026-01-01', '2026-01-31'),
    'transaksi-januari-2026.xlsx'
);
```

#### Di Filament Resource

```php
// Header action di ListTransactions
Tables\Actions\Action::make('exportExcel')
    ->form([
        Forms\Components\DatePicker::make('start_date')
            ->label('Dari Tanggal'),
        Forms\Components\DatePicker::make('end_date')
            ->label('Sampai Tanggal'),
    ])
    ->action(function (array $data) {
        return Excel::download(
            new TransactionsExport($data['start_date'], $data['end_date']),
            'transaksi.xlsx'
        );
    });
```

---

## PDF Reports

### Daftar PDF

| Laporan | Controller | View Template |
|---------|------------|---------------|
| Kwitansi Transaksi | `ReceiptController` | `pdf/receipts/transaction.blade.php` |
| Formulir Pendaftaran | `RegistrationPdfController` | `pdf/forms/registration.blade.php` |
| Laporan Transaksi | `TransactionExportController` | `pdf/reports/transactions.blade.php` |

---

### Kwitansi Transaksi (Receipt)

**Controller**: `ReceiptController::generateReceipt()`

#### Konten

- Header lembaga
- Data santri
- Detail pembayaran (bisa multiple transaksi per hari)
- Total tagihan, terbayar, sisa
- QR code verifikasi
- Tanda tangan digital (tanggal, petugas)

#### Spesifikasi

| Property | Value |
|----------|-------|
| Paper Size | A5 |
| Orientation | Portrait |
| Font | Sans-serif |
| QR Code | 80px SVG |

#### Contoh Filename
```
nota-260001-16012026.pdf
```

#### Template Structure

```blade
{{-- pdf/receipts/transaction.blade.php --}}
<div class="receipt">
    <header>
        <h1>KWITANSI PEMBAYARAN</h1>
        <h2>Pondok Pesantren Dar Al Tauhid</h2>
    </header>
    
    <section class="student-info">
        <p>No. Pendaftaran: {{ $student->registration_number }}</p>
        <p>Nama: {{ $student->full_name }}</p>
    </section>
    
    <section class="payment-details">
        <table>
            @foreach($transactions as $tx)
            <tr>
                <td>{{ $tx->transaction_date }}</td>
                <td>Rp {{ number_format($tx->amount) }}</td>
            </tr>
            @endforeach
        </table>
    </section>
    
    <section class="summary">
        <p>Total Tagihan: Rp {{ number_format($totalBill) }}</p>
        <p>Total Terbayar: Rp {{ number_format($totalPaid) }}</p>
        <p>Sisa: Rp {{ number_format($remaining) }}</p>
    </section>
    
    <footer>
        <img src="{{ $qrCode }}" alt="QR Verify">
        <p>Scan untuk verifikasi</p>
    </footer>
</div>
```

---

### Formulir Pendaftaran

**Controller**: `RegistrationPdfController::download()`

#### Konten

- Data biodata santri lengkap
- Data orang tua (ayah, ibu, wali)
- Data pendaftaran (sekolah asal, tujuan)
- Tanda tangan wali

#### Spesifikasi

| Property | Value |
|----------|-------|
| Paper Size | A4 |
| Orientation | Portrait |
| Package | `barryvdh/laravel-dompdf` |

#### Contoh Filename
```
Pendaftaran-260001.pdf
```

---

### Laporan Transaksi

**Controller**: `TransactionExportController::pdf()`

#### Konten

- Header laporan dengan periode
- Tabel transaksi (no, tanggal, santri, jumlah, metode, petugas)
- Total keseluruhan
- Footer dengan tanggal cetak

#### Spesifikasi

| Property | Value |
|----------|-------|
| Paper Size | A4 |
| Orientation | Portrait |
| Display | Stream (inline, tidak download) |

#### Query Parameters

```
GET /transactions/export/pdf?start_date=2026-01-01&end_date=2026-01-31
```

---

## Generate PDF dengan DomPDF

### Manual (tanpa Facade)

```php
use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'sans-serif');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Download
return response($dompdf->output(), 200)
    ->header('Content-Type', 'application/pdf')
    ->header('Content-Disposition', 'attachment; filename="file.pdf"');

// Stream (inline)
return $dompdf->stream('file.pdf', ['Attachment' => false]);
```

### Via Facade

```php
use Barryvdh\DomPDF\Facade\Pdf;

$pdf = Pdf::loadView('pdf.template', ['data' => $data]);
$pdf->setPaper('A4', 'portrait');

return $pdf->download('file.pdf');
// atau
return $pdf->stream('file.pdf');
```

---

## Export di Filament

### Bulk Export (Selected Records)

```php
// Di table() method
->bulkActions([
    Tables\Actions\BulkAction::make('export')
        ->label('Export Excel')
        ->icon('heroicon-o-arrow-down-tray')
        ->action(function (Collection $records) {
            return Excel::download(
                new StudentsExport($records),
                'santri-selected.xlsx'
            );
        }),
])
```

### Header Action (With Form)

```php
// Di ListRecords page
protected function getHeaderActions(): array
{
    return [
        Tables\Actions\Action::make('exportPdf')
            ->label('Export PDF')
            ->form([
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->required(),
            ])
            ->action(function (array $data) {
                return redirect()->route('transactions.export.pdf', $data);
            }),
    ];
}
```

---

## View Templates Struktur

```
resources/views/pdf/
├── receipts/
│   └── transaction.blade.php   # Kwitansi pembayaran
├── forms/
│   └── registration.blade.php  # Formulir pendaftaran
└── reports/
    └── transactions.blade.php  # Laporan transaksi
```

---

## Styling PDF

### CSS untuk DomPDF

```css
/* Inline styles atau <style> tag */
body {
    font-family: sans-serif;
    font-size: 12px;
    line-height: 1.4;
}

table {
    width: 100%;
    border-collapse: collapse;
}

td, th {
    border: 1px solid #333;
    padding: 5px;
}

.text-right {
    text-align: right;
}

.text-center {
    text-align: center;
}
```

### QR Code di PDF

```php
$svgContent = QrCode::format('svg')
    ->size(80)
    ->generate($url);

$qrCode = 'data:image/svg+xml;base64,' . base64_encode($svgContent);

// Di blade
<img src="{{ $qrCode }}" alt="QR">
```

---

## File Reference

```
app/Exports/
├── StudentsExport.php       # Export santri
└── TransactionsExport.php   # Export transaksi

app/Http/Controllers/
├── ReceiptController.php           # Kwitansi PDF
├── RegistrationPdfController.php   # Formulir PDF
└── TransactionExportController.php # Laporan PDF

resources/views/pdf/
├── receipts/
│   └── transaction.blade.php
├── forms/
│   └── registration.blade.php
└── reports/
    └── transactions.blade.php
```

---

## Best Practices

### 1. Memory Management
Untuk export data besar, gunakan chunking:

```php
use Maatwebsite\Excel\Concerns\FromQuery;

class LargeStudentsExport implements FromQuery
{
    public function query()
    {
        return Student::query();
    }
}
```

### 2. Queue Export
Untuk file besar, gunakan queue:

```php
Excel::queue(new StudentsExport(), 'students.xlsx')
    ->chain([
        new NotifyUserOfCompletedExport($user),
    ]);
```

### 3. Stream untuk File Besar
Gunakan streaming untuk menghindari memory limit:

```php
return Excel::download(new StudentsExport(), 'students.xlsx', 
    \Maatwebsite\Excel\Excel::XLSX
);
```

---

*Dokumentasi ini terakhir diperbarui pada: Januari 2026*
