# Role & Permissions

> Dokumentasi lengkap tentang sistem role, permission, dan akses kontrol.

**Dokumentasi Terkait:**
- [← Kembali ke README](../README.md)
- [Instalasi & Konfigurasi](./INSTALLATION.md)
- [Filament Resources](./FILAMENT_RESOURCES.md)

---

## Overview

Sistem menggunakan **Spatie Laravel Permission** untuk manajemen role dan permission.

**Package**: `spatie/laravel-permission`

---

## Daftar Role

| Role | Deskripsi | Scope |
|------|-----------|-------|
| **Administrator** | Super Admin | Global - Akses semua fitur |
| **Petugas** | Panitia PSB | Global - Kelola data santri |
| **Bendahara Pondok** | Keuangan Pusat | Global - Transaksi semua lembaga |
| **Bendahara Unit** | Keuangan Lembaga | Unit - Transaksi lembaga sendiri |
| **Kepala** | Pimpinan Lembaga | Unit - Read only |

---

## Daftar Permission

### Santri Permissions
| Permission | Deskripsi |
|------------|-----------|
| `view_all_students` | Lihat semua santri |
| `view_institution_students` | Lihat santri lembaga sendiri |
| `create_students` | Buat santri baru |
| `edit_students` | Edit data santri |
| `delete_students` | Hapus santri |
| `verify_students` | Verifikasi data santri |
| `accept_students` | Terima santri |

### Transaksi Permissions
| Permission | Deskripsi |
|------------|-----------|
| `view_all_transactions` | Lihat semua transaksi |
| `view_institution_transactions` | Lihat transaksi lembaga sendiri |
| `create_transactions` | Buat transaksi baru |
| `edit_transactions` | Edit transaksi |

### Management Permissions
| Permission | Deskripsi |
|------------|-----------|
| `manage_users` | Kelola user admin |
| `manage_settings` | Kelola pengaturan |
| `view_dashboard_stats` | Lihat statistik dashboard |

---

## Role-Permission Matrix

### Administrator
```php
// Semua permission
$roleAdmin->givePermissionTo(Permission::all());
```

| Permission | Status |
|------------|--------|
| view_all_students | ✅ |
| view_institution_students | ✅ |
| create_students | ✅ |
| edit_students | ✅ |
| delete_students | ✅ |
| verify_students | ✅ |
| accept_students | ✅ |
| view_all_transactions | ✅ |
| view_institution_transactions | ✅ |
| create_transactions | ✅ |
| edit_transactions | ✅ |
| manage_users | ✅ |
| manage_settings | ✅ |
| view_dashboard_stats | ✅ |

---

### Petugas

```php
$rolePetugas->givePermissionTo([
    'view_all_students',
    'create_students',
    'edit_students',
    'view_dashboard_stats',
]);
```

| Permission | Status |
|------------|--------|
| view_all_students | ✅ |
| create_students | ✅ |
| edit_students | ✅ |
| view_dashboard_stats | ✅ |
| delete_students | ❌ |
| verify_students | ❌ |
| Transaksi | ❌ |

---

### Bendahara Pondok

```php
$roleBendaharaPondok->givePermissionTo([
    'view_all_transactions',
    'create_transactions',
    'edit_transactions',
    'view_all_students',
]);
```

| Permission | Status |
|------------|--------|
| view_all_transactions | ✅ |
| create_transactions | ✅ |
| edit_transactions | ✅ |
| view_all_students | ✅ |
| create_students | ❌ |
| manage_users | ❌ |

**Payment Location**: `PANITIA`

---

### Bendahara Unit

```php
$roleBendaharaUnit->givePermissionTo([
    'view_institution_transactions',
    'create_transactions',
    'view_institution_students',
]);
```

| Permission | Status |
|------------|--------|
| view_institution_transactions | ✅ |
| create_transactions | ✅ |
| view_institution_students | ✅ |
| view_all_transactions | ❌ |
| edit_transactions | ❌ |

**Payment Location**: `UNIT`

---

### Kepala

```php
$roleKepala->givePermissionTo([
    'view_institution_students',
    'view_institution_transactions',
    'view_dashboard_stats',
]);
```

| Permission | Status |
|------------|--------|
| view_institution_students | ✅ |
| view_institution_transactions | ✅ |
| view_dashboard_stats | ✅ |
| create_* | ❌ |
| edit_* | ❌ |
| delete_* | ❌ |

**Note**: Kepala Pondok dapat **approve** distribusi dana.

---

## Akun Default

Setelah menjalankan seeder:

### Administrator
| Field | Value |
|-------|-------|
| Name | Fauzan Hanif |
| Email | `fauzanhanif2112@gmail.com` |
| Password | `F@uzan2112` |
| Institution | - (Global) |

### Petugas
| Field | Value |
|-------|-------|
| Name | Nabil Maulidi |
| Email | `nabilmaulidi@psb.daraltauhid.com` |
| Password | `password` |
| Institution | - (Global) |

### Bendahara Pondok
| Field | Value |
|-------|-------|
| Name | Rohmah Saadah |
| Email | `bendahara.pondok@psb.com` |
| Password | `password` |
| Institution | Pondok |

### Bendahara Unit
| Name | Email | Institution |
|------|-------|-------------|
| Sofiyah | `bendahara.smp@psb.com` | SMP |
| Fatimah Zahra | `bendahara.ma@psb.com` | MA |
| Khadijah | `bendahara.madrasah@psb.com` | Madrasah |

### Kepala
| Name | Email | Institution |
|------|-------|-------------|
| KH. Abdul Rahman | `kepala.pondok@psb.com` | Pondok |
| Drs. Muhammad Saleh | `kepala.smp@psb.com` | SMP |
| Drs. Ahmad Dahlan | `kepala.ma@psb.com` | MA |
| Musthofa, S.H. | `kepala.madrasah@psb.com` | Madrasah |

---

## Implementasi di Code

### Check Permission

```php
// Di Controller/Resource
if (auth()->user()->can('create_students')) {
    // Allow action
}

// Di Blade
@can('edit_students')
    <button>Edit</button>
@endcan
```

### Check Role

```php
// Single role
if (auth()->user()->hasRole('Administrator')) {
    // Admin only
}

// Multiple roles
if (auth()->user()->hasRole(['Administrator', 'Petugas'])) {
    // Admin or Petugas
}
```

### Filament Resource Authorization

```php
// app/Filament/Resources/StudentResource.php

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

public static function canViewAny(): bool
{
    $user = auth()->user();
    return $user->can('view_all_students') 
        || $user->can('view_institution_students');
}
```

### Scoped Queries

```php
// Filter data berdasarkan institution user
public static function getEloquentQuery(): Builder
{
    $user = auth()->user();
    $query = parent::getEloquentQuery();
    
    // Admin lihat semua
    if ($user->hasRole('Administrator')) {
        return $query;
    }
    
    // User lain lihat berdasarkan institution
    if ($user->institution_id) {
        return $query->whereHas('registration', fn($q) => 
            $q->where('destination_institution_id', $user->institution_id)
        );
    }
    
    return $query;
}
```

---

## Distribusi Dana - Role Matrix

### Settlement Workflow

| Step | Action | Role yang Bisa |
|------|--------|----------------|
| 1 | Buat request (PENDING) | Bendahara Pondok |
| 2 | Approve (APPROVED) | Kepala Pondok |
| 3 | Konfirmasi terima (COMPLETED) | Bendahara Unit (tujuan) |

### Permission Check

```php
// Approve - hanya Kepala Pondok
public function canApprove(): bool
{
    $user = auth()->user();
    return $user->hasRole('Kepala') 
        && $user->institution?->type === 'pondok';
}

// Confirm Receipt - hanya Bendahara tujuan
public function canConfirmReceipt(): bool
{
    $user = auth()->user();
    return $user->hasRole('Bendahara Unit') 
        && $user->institution_id === $this->institution_id;
}
```

---

## Seeder Reference

File: `database/seeders/DatabaseSeeder.php`

```php
// Create permissions
$permissions = [
    'view_all_students',
    'view_institution_students',
    'create_students',
    'edit_students',
    'delete_students',
    'verify_students',
    'accept_students',
    'view_all_transactions',
    'view_institution_transactions',
    'create_transactions',
    'edit_transactions',
    'manage_users',
    'manage_settings',
    'view_dashboard_stats',
];

foreach ($permissions as $permission) {
    Permission::create(['name' => $permission]);
}

// Create roles with permissions
$roleAdmin = Role::create(['name' => 'Administrator']);
$roleAdmin->givePermissionTo(Permission::all());

// ... (other roles)
```

---

*Dokumentasi ini terakhir diperbarui pada: Januari 2026*
