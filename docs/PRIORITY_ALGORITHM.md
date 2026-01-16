# Priority Algorithm

Algoritma prioritas menentukan pembagian dana dari pembayaran santri ke berbagai lembaga di Pondok Pesantren.

## Kapan Algoritma Digunakan?

> [!IMPORTANT]
> - Algoritma prioritas **HANYA berlaku** untuk pembayaran di **PANITIA**
> - Pembayaran langsung ke **Madrasah/Sekolah** mengurangi **sisa tagihan**
> - Priority Algorithm menghitung berdasarkan **sisa tagihan** (setelah pembayaran langsung)

## Diagram Alur Lengkap

```mermaid
flowchart TD
    subgraph INPUT["💰 PEMBAYARAN MASUK"]
        A{"Di mana pembayaran<br/>diterima?"}
    end

    subgraph DIRECT["⚡ PEMBAYARAN LANGSUNG"]
        subgraph MADRASAH_DIRECT["📿 MADRASAH"]
            E["Bendahara Madrasah"]
            E1["Lunas/mengurangi tagihan Madrasah"]
        end
        
        subgraph SEKOLAH_DIRECT["🏫 SEKOLAH"]
            H["Bendahara Sekolah SMP/MA"]
            H1["Lunas/mengurangi tagihan Sekolah"]
        end
    end

    subgraph PANITIA["🏛️ PANITIA - PRIORITY ALGORITHM"]
        B["Admin / Petugas / Bendahara Pondok"]
        B1["Hitung SISA TAGIHAN<br/>(setelah pembayaran langsung)"]
        
        subgraph PRIORITY1["🥇 PRIORITAS 1: MADRASAH"]
            C["Cek sisa tagihan Madrasah"]
            C1{"Sisa Tagihan<br/>Madrasah > 0?"}
            C2["Alokasi ke Madrasah<br/>(hingga lunas)"]
        end
        
        subgraph PRIORITY2["🥈 PRIORITAS 2: 50:50 SPLIT"]
            D["Bagi Sisa Dana 50% : 50%"]
            
            subgraph SEKOLAH_SPLIT["SEKOLAH"]
                D1["Alokasi 50%"]
                D2["Cek sisa tagihan Sekolah"]
                D3{"Melebihi sisa<br/>tagihan Sekolah?"}
                D4["Terima max sisa tagihan"]
                D5["Overflow → Pondok"]
            end
            
            subgraph PONDOK_SPLIT["PONDOK"]
                D6["Terima 50% + Overflow"]
            end
        end
    end

    subgraph OUTPUT["✅ OUTPUT"]
        N["Total Penerimaan Per Lembaga"]
    end

    %% Input routing
    A -->|Madrasah| E
    A -->|Sekolah| H
    A -->|Panitia| B

    %% Direct Madrasah flow
    E --> E1 --> C
    
    %% Direct Sekolah flow
    H --> H1 --> D2

    %% Panitia flow - Priority 1 (Madrasah)
    B --> B1 --> C
    C --> C1
    C1 -->|Ya| C2
    C2 --> C1
    C1 -->|Tidak/Lunas| D
    
    %% Priority 2 (50:50 Split)
    D --> D1
    D1 --> D2
    D2 --> D3
    D3 -->|Ya| D4 --> D5
    D3 -->|Tidak| N
    D5 --> D6
    D --> D6
    D6 --> N

    style INPUT fill:#f5f5f5,stroke:#9e9e9e
    style DIRECT fill:#e8f5e9,stroke:#4caf50
    style MADRASAH_DIRECT fill:#fff3e0,stroke:#ff9800
    style SEKOLAH_DIRECT fill:#e3f2fd,stroke:#2196f3
    style PANITIA fill:#fce4ec,stroke:#e91e63
    style PRIORITY1 fill:#fff8e1,stroke:#ffc107
    style PRIORITY2 fill:#f3e5f5,stroke:#9c27b0
    style SEKOLAH_SPLIT fill:#e1f5fe,stroke:#03a9f4
    style PONDOK_SPLIT fill:#ede7f6,stroke:#673ab7
    style OUTPUT fill:#c8e6c9,stroke:#4caf50
```

## Lokasi Input Pembayaran

| Lokasi | Role | Algoritma | Efek |
|--------|------|-----------|------|
| **Panitia** | Admin, Petugas, Bd. Pondok | ✅ Aktif | Distribusi via algoritma ke sisa tagihan |
| **Madrasah** | Bendahara Madrasah | ❌ Tidak | Langsung kurangi tagihan Madrasah |
| **Sekolah** | Bendahara SMP/MA | ❌ Tidak | Langsung kurangi tagihan Sekolah |

---

## Cara Kerja Priority Algorithm

### Langkah 1: Hitung Sisa Tagihan

Sebelum algoritma berjalan, sistem menghitung **sisa tagihan** yang belum dibayar:

```
Sisa Tagihan Madrasah = Tagihan Madrasah - Pembayaran Langsung ke Madrasah
Sisa Tagihan Sekolah  = Tagihan Sekolah  - Pembayaran Langsung ke Sekolah
Sisa Tagihan Pondok   = Tagihan Pondok   - Pembayaran Langsung ke Pondok
```

### Langkah 2: Distribusi Dana Panitia

Dana dari Panitia didistribusikan berdasarkan **sisa tagihan**:

1. **Prioritas 1: MADRASAH** - Alokasi 100% ke sisa tagihan Madrasah
2. **Prioritas 2: 50:50 SPLIT** - Sisa dana dibagi ke Sekolah dan Pondok
   - Sekolah maksimal menerima sebesar sisa tagihannya
   - Overflow dari Sekolah masuk ke Pondok

## Pembayaran Langsung ke Unit

### Di Madrasah

Ketika **Bendahara Unit Madrasah** menerima pembayaran:

```
Pembayaran: Rp 500.000
→ Langsung masuk ke tagihan Madrasah
→ Tidak mempengaruhi tagihan Sekolah/Pondok
```

**Hasil:**
- Madrasah: Rp 500.000 ✓
- Sekolah: Rp 0
- Pondok: Rp 0

### Di Sekolah (SMP/MA)

Ketika **Bendahara Unit SMP** atau **Bendahara Unit MA** menerima pembayaran:

```
Pembayaran: Rp 1.000.000
→ Langsung masuk ke tagihan Sekolah (SMP atau MA)
→ Tidak mempengaruhi tagihan Madrasah/Pondok
```

**Hasil:**
- Madrasah: Rp 0
- Sekolah: Rp 1.000.000 ✓
- Pondok: Rp 0

---

## Contoh Perhitungan (Pembayaran di Panitia)

### Tagihan Santri

| Komponen | Tagihan |
|----------|---------|
| Madrasah | Rp 290.000 |
| SMP | Rp 2.295.000 |
| Pondok | Rp 4.633.000 |
| **Total** | **Rp 7.218.000** |

### Skenario 1: Pembayaran Lengkap

**Pembayaran: Rp 7.218.000** (di Panitia)

```
1. Madrasah     → Rp 290.000    (100% tagihan)
2. Sisa         = Rp 6.928.000
3. Split 50:50  = Rp 3.464.000 masing-masing
4. SMP          → Rp 2.295.000  (max tagihan)
5. Overflow     = Rp 1.169.000  (ke Pondok)
6. Pondok       → Rp 3.464.000 + Rp 1.169.000 = Rp 4.633.000
```

**Hasil:**
- Madrasah: Rp 290.000 ✓ (Lunas)
- SMP: Rp 2.295.000 ✓ (Lunas)
- Pondok: Rp 4.633.000 ✓ (Lunas)

---

### Skenario 2: Pembayaran Sebagian

**Pembayaran: Rp 4.000.000** (di Panitia)

```
1. Madrasah     → Rp 290.000    (100% tagihan)
2. Sisa         = Rp 3.710.000
3. Split 50:50  = Rp 1.855.000 masing-masing
4. SMP          → Rp 1.855.000  (< tagihan, tidak ada overflow)
5. Overflow     = Rp 0
6. Pondok       → Rp 1.855.000
```

**Hasil:**
- Madrasah: Rp 290.000 ✓ (Lunas)
- SMP: Rp 1.855.000 (Sisa Rp 440.000)
- Pondok: Rp 1.855.000 (Sisa Rp 2.778.000)

---

### Skenario 3: Pembayaran Sangat Kecil

**Pembayaran: Rp 200.000** (di Panitia)

```
1. Madrasah     → Rp 200.000  (seluruh uang < tagihan)
2. Sisa         = Rp 0
```

**Hasil:**
- Madrasah: Rp 200.000 (Sisa Rp 90.000)
- SMP: Rp 0
- Pondok: Rp 0

---

## Kombinasi Pembayaran

Santri bisa membayar di **berbagai lokasi** secara bersamaan:

| Pembayaran | Lokasi | Hasil |
|------------|--------|-------|
| Rp 290.000 | Madrasah | → Langsung ke Madrasah |
| Rp 500.000 | SMP | → Langsung ke SMP |
| Rp 2.000.000 | Panitia | → Via algoritma (Madrasah → 50:50) |

Sistem akan menghitung total penerimaan dari semua sumber.

---

## Referensi File

| File | Deskripsi |
|------|-----------|
| [PaymentDistributionService.php](file:///c:/laragon/www/psb/app/Services/PaymentDistributionService.php) | Implementasi algoritma |
| [Bill.php](file:///c:/laragon/www/psb/app/Models/Bill.php) | Model tagihan |
| [Student.php](file:///c:/laragon/www/psb/app/Models/Student.php) | Method `getTotalPaid()` |

## Lihat Juga

- [Hybrid Cash Collection](file:///c:/laragon/www/psb/docs/HYBRID_CASH_COLLECTION.md)
- [Manual Settlement](file:///c:/laragon/www/psb/docs/MANUAL_SETTLEMENT.md)
