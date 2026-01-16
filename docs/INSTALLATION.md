# Instalasi & Konfigurasi

> Panduan lengkap untuk menginstal dan mengkonfigurasi aplikasi PSB.

**Dokumentasi Terkait:**
- [← Kembali ke README](../README.md)
- [Struktur Database](./DATABASE_SCHEMA.md)
- [Integrasi Pihak Ketiga](./INTEGRATIONS.md)

---

## Prasyarat Sistem

| Komponen | Versi | Keterangan |
|----------|-------|------------|
| **PHP** | `^8.2` | Sesuai `composer.json` |
| **Laravel** | `^12.0` | Framework utama |
| **Filament** | `^4.3` | Admin panel |
| **Livewire** | `^3.7` | Komponen reaktif |
| **MySQL/MariaDB** | 5.7+ / 10.3+ | Database |
| **Node.js** | 18.x+ | Build frontend |

### Ekstensi PHP Wajib
```
BCMath, Ctype, DOM, Fileinfo, JSON, Mbstring, 
OpenSSL, PDO (mysql), Tokenizer, XML, GD/Imagick
```

---

## Setup File `.env`

```bash
# 1. Copy template
cp .env.example .env

# 2. Edit konfigurasi
```

### Konfigurasi Database
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=psb
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Konfigurasi WhatsApp Gateway (Fonnte)
```env
FONNTE_TOKEN=your_fonnte_api_token
```
> Token didapat dari dashboard [fonnte.com](https://fonnte.com). Disimpan di `.env` dan diakses via `config('services.fonnte.token')`.

### Konfigurasi Pusher (Realtime Events)
```env
PUSHER_APP_ID="your_app_id"
PUSHER_APP_KEY="your_app_key"
PUSHER_APP_SECRET="your_app_secret"
PUSHER_APP_CLUSTER="ap1"
```

### Konfigurasi Email SMTP
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="admin@daraltauhid.com"
```

---

## Perintah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/your-repo/psb.git
cd psb

# 2. Install dependensi PHP
composer install

# 3. Install dependensi Node.js
npm install

# 4. Copy dan konfigurasi .env
cp .env.example .env
# Edit .env sesuai kebutuhan

# 5. Generate application key
php artisan key:generate

# 6. Link storage
php artisan storage:link

# 7. Migrasi database + seeder
php artisan migrate:fresh --seed

# 8. Optimisasi (opsional, untuk production)
php artisan filament:optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 9. Build assets
npm run build
```

---

## Menjalankan Development Server

```bash
# Cara 1: Script composer (recommended)
composer dev

# Cara 2: Manual
php artisan serve                    # Server
php artisan queue:listen --tries=1   # Queue worker
npm run dev                          # Vite dev server
```

Script `composer dev` menjalankan sekaligus:
- Laravel server (`php artisan serve`)
- Queue worker (`php artisan queue:listen`)
- Log viewer (`php artisan pail`)
- Vite dev server (`npm run dev`)

---

## Akun Default

Setelah menjalankan seeder, akun-akun berikut tersedia:

### Administrator
| Field | Value |
|-------|-------|
| Email | `fauzanhanif2112@gmail.com` |
| Password | `F@uzan2112` |
| Role | Administrator (Full Access) |

### Petugas PSB
| Field | Value |
|-------|-------|
| Email | `nabilmaulidi@psb.daraltauhid.com` |
| Password | `password` |
| Role | Petugas (Input/Edit Santri) |

### Bendahara
| Field | Value |
|-------|-------|
| Email | `bendahara.pondok@psb.com` |
| Password | `password` |
| Role | Bendahara Pondok (Keuangan Global) |

> 📝 Untuk daftar lengkap akun, lihat file `database/seeders/DatabaseSeeder.php`

---

## Environment Variables

| Variable | Deskripsi | Contoh |
|----------|-----------|--------|
| `APP_NAME` | Nama Aplikasi | `PSB` |
| `APP_URL` | URL Aplikasi | `https://psb.daraltauhid.com` |
| `DB_*` | Konfigurasi Database | - |
| `FONNTE_TOKEN` | Token API Fonnte | `your_token` |
| `PUSHER_*` | Konfigurasi Pusher | - |
| `MAIL_*` | Konfigurasi SMTP | - |

---

## Struktur Direktori

```
psb/
├── app/
│   ├── Filament/          # Panel Admin (Filament)
│   │   ├── Resources/     # CRUD Resources
│   │   └── Widgets/       # Dashboard Widgets
│   ├── Livewire/          # Komponen Livewire
│   │   ├── CheckStatus.php
│   │   ├── Home.php
│   │   └── RegistrationWizard.php
│   ├── Models/            # Eloquent Models
│   └── Services/          # Business Logic Services
├── database/
│   ├── migrations/        # Database Migrations
│   └── seeders/           # Database Seeders
├── docs/                  # Dokumentasi
├── resources/
│   └── views/             # Blade Templates
├── routes/
│   └── web.php            # Web Routes
└── public/                # Public Assets
```

---

## Dependensi Utama

| Package | Kegunaan |
|---------|----------|
| `filament/filament` | Admin Panel |
| `livewire/livewire` | Reactive Components |
| `barryvdh/laravel-dompdf` | Generate PDF |
| `maatwebsite/excel` | Export Excel |
| `spatie/laravel-permission` | Role & Permission |
| `simplesoftwareio/simple-qrcode` | QR Code Generator |
| `pusher/pusher-php-server` | Realtime Events |

---

## Development Commands

### Menjalankan Tests
```bash
composer test
```

### Code Style (Laravel Pint)
```bash
./vendor/bin/pint
```

---

## Troubleshooting

### Error: "Fonnte token not configured"
```bash
# Pastikan FONNTE_TOKEN ada di .env
FONNTE_TOKEN=your_token_here

# Clear config cache
php artisan config:clear
```

### Error: "Permission denied"
```bash
# Pastikan storage writable
chmod -R 775 storage bootstrap/cache

# Di Windows (Laragon), jalankan sebagai Administrator
```

### Bills tidak ter-generate
```php
// Pastikan ada academic_year aktif
AcademicYear::where('is_active', true)->exists();

// Pastikan ada fee_components untuk institution
FeeComponent::where('institution_id', $id)->exists();

// Regenerate bills manual
$student->generateBills();
```

---

*Dokumentasi ini terakhir diperbarui pada: Januari 2026*
