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
| PHP | 8.4+ |
| MySQL/MariaDB | 8.0+ |
| Composer | 2.7+ |
| Node.js | 18.x+ |

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

## 📚 Dokumentasi

Dokumentasi teknis lengkap tersedia di folder `docs/`:

| Dokumen | Deskripsi |
|---------|-----------|
| [Instalasi & Konfigurasi](docs/INSTALLATION.md) | Panduan instalasi, setup environment, dan menjalankan aplikasi |
| [Struktur Database](docs/DATABASE_SCHEMA.md) | ERD, tabel-tabel, dan relasi antar entitas |
| [Integrasi Pihak Ketiga](docs/INTEGRATIONS.md) | Fonnte WhatsApp, Wilayah API |
| [Filament Resources](docs/FILAMENT_RESOURCES.md) | Admin panel resources dan widgets |
| [Role & Permissions](docs/ROLES_PERMISSIONS.md) | Sistem role, permission, dan akses kontrol |
| [Status & Workflows](docs/STATUS_WORKFLOWS.md) | Alur kerja pendaftaran, pembayaran, dan distribusi dana |
| [Sistem Keuangan](docs/FINANCIAL_SYSTEM.md) | Arsitektur keuangan dan distribusi dana |
| [Controllers & API](docs/CONTROLLERS_API.md) | HTTP Controllers dan Web Routes |
| [Observers & Events](docs/OBSERVERS_EVENTS.md) | Model observers dan event handling |
| [Exports & Reports](docs/EXPORTS_REPORTS.md) | Export Excel dan PDF reports |

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
