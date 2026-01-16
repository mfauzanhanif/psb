# Integrasi Pihak Ketiga

> Dokumentasi lengkap tentang integrasi dengan layanan eksternal.

**Dokumentasi Terkait:**
- [← Kembali ke README](../README.md)
- [Instalasi & Konfigurasi](./INSTALLATION.md)
- [Filament Resources](./FILAMENT_RESOURCES.md)

---

## 1. Fonnte (WhatsApp Gateway)

### Overview

Fonnte digunakan untuk mengirim notifikasi WhatsApp otomatis ke wali santri.

**File**: `app/Services/FonnteService.php`

### Cara Kerja

```
User Action → FonnteService::send() → HTTP POST ke api.fonnte.com → WhatsApp Message
```

### Konfigurasi

#### 1. Dapatkan Token API
1. Daftar di [fonnte.com](https://fonnte.com)
2. Hubungkan device WhatsApp
3. Copy token dari dashboard

#### 2. Set Environment Variable
```env
# .env
FONNTE_TOKEN=your_fonnte_api_token
```

#### 3. Konfigurasi Laravel
```php
// config/services.php
'fonnte' => [
    'token' => env('FONNTE_TOKEN'),
],
```

### Penggunaan

```php
use App\Services\FonnteService;

$fonnte = new FonnteService();

// Kirim pesan tunggal
$result = $fonnte->send('08123456789', 'Pesan Anda');

// Kirim pesan massal
$results = $fonnte->sendBulk(['08123456789', '08987654321'], 'Pesan broadcast');
```

### Format Nomor Telepon

Service otomatis mengkonversi format nomor Indonesia:

| Input | Output |
|-------|--------|
| `08123456789` | `628123456789` |
| `628123456789` | `628123456789` (tetap) |
| `+628123456789` | `628123456789` |

**Validasi:**
- Panjang: 10-15 digit
- Harus format Indonesia (62...)

### Response Format

```php
// Sukses
[
    'success' => true, 
    'message' => 'Message sent successfully'
]

// Gagal
[
    'success' => false, 
    'message' => 'Error reason'
]
```

### Penggunaan di Aplikasi

| Event | Pesan | File |
|-------|-------|------|
| Pendaftaran baru | Notifikasi ke wali santri | `RegistrationWizard.php` |
| Pembayaran | Konfirmasi pembayaran | `TransactionResource.php` |
| Status update | Perubahan status santri | `StudentResource.php` |

### Contoh Pesan

```
Assalamu'alaikum Bapak/Ibu,

Pembayaran sebesar Rp 5.000.000 untuk santri AHMAD FAUZAN 
telah kami terima.

Sisa tagihan: Rp 2.500.000

Terima kasih.
- Panitia PSB Dar Al Tauhid
```

### Troubleshooting

#### Token tidak terkonfigurasi
```bash
# Error: "Fonnte token not configured"
# Solusi:
php artisan config:clear
# Pastikan FONNTE_TOKEN ada di .env
```

#### Pesan tidak terkirim
- Pastikan device WhatsApp terhubung di dashboard Fonnte
- Cek saldo/kuota di dashboard Fonnte
- Cek log di `storage/logs/laravel.log`

---

## 2. Wilayah API (Data Provinsi/Kota)

### Overview

API publik untuk data wilayah Indonesia (Provinsi, Kabupaten, Kecamatan, Desa).

**File**: `app/Services/WilayahService.php`

### Sumber Data

API publik: `https://wilayah.id/api/`

### Endpoints yang Digunakan

| Endpoint | Deskripsi |
|----------|-----------|
| `/api/provinces.json` | Daftar provinsi |
| `/api/regencies/{code}.json` | Kabupaten/Kota |
| `/api/districts/{code}.json` | Kecamatan |
| `/api/villages/{code}.json` | Desa/Kelurahan |

### Penggunaan

```php
use App\Services\WilayahService;

// Get daftar provinsi
$provinces = WilayahService::getProvinces();
// Return: ['11' => 'ACEH', '12' => 'SUMATERA UTARA', ...]

// Get kabupaten/kota berdasarkan provinsi
$regencies = WilayahService::getRegencies('32'); // Jawa Barat
// Return: ['3201' => 'KAB. BOGOR', '3202' => 'KAB. SUKABUMI', ...]

// Get kecamatan
$districts = WilayahService::getDistricts('3209'); // Cirebon
// Return: ['320901' => 'Waled', '320902' => 'Pasaleman', ...]

// Get desa/kelurahan
$villages = WilayahService::getVillages('320901'); // Waled

// Cari kode berdasarkan nama (untuk form edit)
$provinceCode = WilayahService::findProvinceCode('JAWA BARAT');
$regencyCode = WilayahService::findRegencyCode('32', 'CIREBON');
```

### Caching

Data di-cache selama **1 jam (3600 detik)** untuk performa:

```php
Cache::remember('api_provinces', 3600, function () { ... });
Cache::remember("api_regencies_{$provinceCode}", 3600, function () { ... });
```

### Penggunaan di Form

Digunakan di form pendaftaran (`RegistrationWizard`) untuk cascade dropdown:

```
Provinsi → Kabupaten → Kecamatan → Desa
```

Setiap perubahan dropdown parent akan me-refresh dropdown child.

### Error Handling

```php
// HTTP request dengan retry
Http::withOptions(['verify' => false])
    ->timeout(10)
    ->retry(2, 1000)
    ->get($url);
```

- Retry 2x jika gagal
- Timeout 10 detik
- SSL verification disabled untuk kompatibilitas hosting

### Troubleshooting

#### Data wilayah tidak muncul
```bash
# Clear cache
php artisan cache:clear

# Cek koneksi ke API
curl https://wilayah.id/api/provinces.json
```

#### API timeout
- API wilayah.id mungkin overload
- Data tetap tersedia dari cache jika pernah di-load sebelumnya

---

## 3. Pusher (Realtime Events)

### Overview

Pusher digunakan untuk realtime broadcasting events.

### Konfigurasi

```env
PUSHER_APP_ID="your_app_id"
PUSHER_APP_KEY="your_app_key"
PUSHER_APP_SECRET="your_app_secret"
PUSHER_APP_CLUSTER="ap1"

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### Penggunaan

Saat ini digunakan untuk:
- Refresh dashboard widget secara realtime
- Notifikasi admin saat ada pendaftaran baru

---

## File Reference

| Service | File | Deskripsi |
|---------|------|-----------|
| FonnteService | `app/Services/FonnteService.php` | WhatsApp Gateway |
| WilayahService | `app/Services/WilayahService.php` | Data Wilayah Indonesia |
| PaymentDistributionService | `app/Services/PaymentDistributionService.php` | Distribusi Dana |

---

*Dokumentasi ini terakhir diperbarui pada: Januari 2026*
