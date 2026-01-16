# Sistem Penerimaan Santri Baru (PSB)
## Pondok Pesantren Dar Al Tauhid

Aplikasi berbasis web untuk mengelola pendaftaran santri baru, pembayaran, dan manajemen data santri secara terpusat. Dibangun menggunakan **Laravel 12**, **Filament 4**, dan **Livewire 3**.

---

## 📋 Fitur Utama

### Pendaftaran Online
- Wizard pendaftaran step-by-step untuk wali santri
- Validasi data real-time
- Nomor pendaftaran otomatis

### Manajemen Keuangan
- Pelacakan pembayaran per santri
- Komponen biaya yang dapat dikonfigurasi per lembaga
- Distribusi dana otomatis ke lembaga terkait
- Laporan transaksi komprehensif

### Notifikasi WhatsApp
- Integrasi dengan [Fonnte](https://fonnte.com) untuk notifikasi otomatis
- Notifikasi status pendaftaran ke wali santri
- Notifikasi pembayaran

### Manajemen Dokumen
- Upload berkas santri (foto, ijazah, akta, dll)
- Verifikasi dokumen oleh admin
- Penyimpanan file terorganisir

### Laporan & Ekspor
- Laporan transaksi keuangan
- Data santri dalam format Excel
- Generate kwitansi pembayaran PDF
- Laporan ringkasan pendaftaran

### Multi-Lembaga
Mendukung pengelolaan untuk:
- ✅ Pondok Pesantren
- ✅ Madrasah Diniyah
- ✅ SMP Plus Dar Al Tauhid
- ✅ MA Nusantara
- ✅ MTsN 3 Cirebon

---

## 🛠️ Prasyarat Sistem

| Komponen | Versi Minimum |
|----------|---------------|
| PHP | 8.2+ |
| MySQL/MariaDB | 5.7+ / 10.3+ |
| Composer | 2.x |
| Node.js | 18.x+ |
| NPM | 9.x+ |

### Ekstensi PHP yang Diperlukan
- BCMath
- Ctype
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO (MySQL)
- Tokenizer
- XML
- GD / Imagick (untuk QR Code)

---

## 🚀 Instalasi Cepat

### 1. Clone Repositori
```bash
git clone https://github.com/your-repo/psb.git
cd psb
```

### 2. Install Dependensi
```bash
composer install
npm install
```

### 3. Konfigurasi Lingkungan
```bash
cp .env.example .env
```

Edit file `.env` dan sesuaikan:
```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=psb
DB_USERNAME=root
DB_PASSWORD=your_password

# WhatsApp Gateway (Fonnte)
FONNTE_TOKEN=your_fonnte_token
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Migrasi & Seed Database
```bash
php artisan migrate:fresh --seed
```
> ⚠️ Perintah ini akan membuat ulang database dan mengisi data awal termasuk user admin, lembaga, dan komponen biaya.

### 6. Link Storage
```bash
php artisan storage:link
```

### 7. Build Aset Frontend
```bash
npm run build
```

### 8. Jalankan Server Development
```bash
# Menggunakan script composer (recommended)
composer dev

# Atau secara manual
php artisan serve
```

---

## 👤 Akun Default

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

## 🔐 Role & Permission

| Role | Deskripsi | Akses |
|------|-----------|-------|
| **Administrator** | Super Admin | Akses penuh ke semua fitur |
| **Petugas** | Panitia PSB | Kelola data santri (CRUD) |
| **Bendahara Pondok** | Keuangan Pusat | Transaksi semua lembaga |
| **Bendahara Unit** | Keuangan Lembaga | Transaksi lembaga sendiri |
| **Kepala** | Pimpinan Lembaga | Lihat data lembaga (Read Only) |

---

## 🔄 Alur Kerja Utama

### Alur Pendaftaran
```
Wali Santri          Sistem              Admin
     │                  │                  │
     ├── Isi Formulir ──►                  │
     │                  │                  │
     │    ◄── Notif WA ─┤                  │
     │                  │                  │
     ├── Bayar ─────────►                  │
     │                  │                  │
     │                  ├── Verifikasi ────►
     │                  │                  │
     │    ◄── Status ───┤◄─────────────────┤
     │                  │                  │
```

### Alur Keuangan
```
Transaksi Masuk ──► Validasi ──► Distribusi ke Komponen Biaya ──► Laporan
```

---

## 📁 Struktur Direktori

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
│   └── Models/            # Eloquent Models
├── database/
│   ├── migrations/        # Database Migrations
│   └── seeders/           # Database Seeders
├── resources/
│   └── views/             # Blade Templates
├── routes/
│   └── web.php            # Web Routes
└── public/                # Public Assets
```

---

## 📦 Dependensi Utama

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

## 🧪 Development

### Menjalankan Server Development
```bash
composer dev
```
Script ini akan menjalankan:
- Laravel server (`php artisan serve`)
- Queue worker (`php artisan queue:listen`)
- Log viewer (`php artisan pail`)
- Vite dev server (`npm run dev`)

### Menjalankan Tests
```bash
composer test
```

### Code Style (Laravel Pint)
```bash
./vendor/bin/pint
```

---

## 🌐 Environment Variables

| Variable | Deskripsi | Contoh |
|----------|-----------|--------|
| `APP_NAME` | Nama Aplikasi | `PSB` |
| `APP_URL` | URL Aplikasi | `https://psb.daraltauhid.com` |
| `DB_*` | Konfigurasi Database | - |
| `FONNTE_TOKEN` | Token API Fonnte | `your_token` |
| `PUSHER_*` | Konfigurasi Pusher | - |
| `MAIL_*` | Konfigurasi SMTP | - |

---

## 📞 Kontak & Dukungan

**Pondok Pesantren Dar Al Tauhid**
- Website: [daraltauhid.com](https://daraltauhid.com)
- Email: admin@daraltauhid.com

---

## 📄 Lisensi

**Private/Proprietary** - Pondok Pesantren Dar Al Tauhid

Hak cipta dilindungi. Dilarang menyalin, mendistribusikan, atau memodifikasi tanpa izin tertulis.

---

*Built with ❤️ using Laravel, Filament, and Livewire*
