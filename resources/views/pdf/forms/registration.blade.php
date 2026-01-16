<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Bukti Pendaftaran - {{ $student->registration_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #166534;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #166534;
            margin: 0;
            font-size: 18pt;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .reg-number {
            background: #dcfce7;
            border: 2px solid #166534;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .reg-number .label {
            font-size: 10pt;
            color: #166534;
            margin-bottom: 5px;
        }

        .reg-number .number {
            font-size: 28pt;
            font-weight: bold;
            color: #166534;
            letter-spacing: 3px;
        }

        .section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .section-title {
            background: #166534;
            color: white;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 0;
        }

        .section-content {
            border: 1px solid #ddd;
            border-top: none;
            padding: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td {
            padding: 4px 0;
            vertical-align: top;
        }

        table td:first-child {
            width: 35%;
            color: #666;
        }

        table td:last-child {
            font-weight: 500;
        }

        .two-col {
            display: table;
            width: 100%;
        }

        .two-col>div {
            display: table-cell;
            width: 50%;
            padding-right: 10px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        .note {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 10px;
            margin-top: 20px;
            font-size: 10pt;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>PONDOK PESANTREN DAR AL TAUHID</h1>
        <p>Bukti Pendaftaran Santri Baru</p>
    </div>

    <div class="reg-number">
        <div class="label">Nomor Pendaftaran</div>
        <div class="number">{{ $student->registration_number }}</div>
    </div>

    {{-- Data Santri --}}
    <div class="section">
        <div class="section-title">DATA SANTRI</div>
        <div class="section-content">
            <table>
                <tr>
                    <td>Nama Lengkap</td>
                    <td>{{ $student->full_name }}</td>
                </tr>
                <tr>
                    <td>NIK</td>
                    <td>{{ $student->nik }}</td>
                </tr>
                <tr>
                    <td>NISN</td>
                    <td>{{ $student->nisn ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Tempat, Tanggal Lahir</td>
                    <td>{{ $student->place_of_birth }},
                        {{ \Carbon\Carbon::parse($student->date_of_birth)->format('d F Y') }}
                    </td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td>{{ $student->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</td>
                </tr>
                <tr>
                    <td>Anak ke / dari</td>
                    <td>{{ $student->child_number }} dari {{ $student->total_siblings }} bersaudara</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>{{ $student->address_street }}, {{ $student->village }}, {{ $student->district }},
                        {{ $student->regency }}, {{ $student->province }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Data Ayah --}}
    @if($father)
        <div class="section">
            <div class="section-title">DATA AYAH KANDUNG</div>
            <div class="section-content">
                <table>
                    <tr>
                        <td>Nama</td>
                        <td>{{ $father->name }}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>{{ $father->life_status == 'alive' ? 'Masih Hidup' : ($father->life_status == 'deceased' ? 'Sudah Meninggal' : 'Tidak Diketahui') }}
                        </td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>{{ $father->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Pendidikan Terakhir</td>
                        <td>{{ $father->education ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Pendidikan Pesantren</td>
                        <td>{{ $father->pesantren_education ?? 'Tidak' }}</td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td>
                        <td>{{ $father->job ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Penghasilan</td>
                        <td>{{ $father->income ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>No. WhatsApp</td>
                        <td>{{ $father->phone_number ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    @endif

    {{-- Data Ibu --}}
    @if($mother)
        <div class="section">
            <div class="section-title">DATA IBU KANDUNG</div>
            <div class="section-content">
                <table>
                    <tr>
                        <td>Nama</td>
                        <td>{{ $mother->name }}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>{{ $mother->life_status == 'alive' ? 'Masih Hidup' : ($mother->life_status == 'deceased' ? 'Sudah Meninggal' : 'Tidak Diketahui') }}
                        </td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>{{ $mother->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Pendidikan Terakhir</td>
                        <td>{{ $mother->education ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Pendidikan Pesantren</td>
                        <td>{{ $mother->pesantren_education ?? 'Tidak' }}</td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td>
                        <td>{{ $mother->job ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Penghasilan</td>
                        <td>{{ $mother->income ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>No. WhatsApp</td>
                        <td>{{ $mother->phone_number ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    @endif

    {{-- Data Wali --}}
    <div class="section">
        <div class="section-title">DATA WALI</div>
        <div class="section-content">
            @if(isset($waliType) && $waliType == 'father')
                <p style="margin-bottom: 10px;"><em>Wali: Ayah Kandung</em></p>
                <table>
                    <tr>
                        <td>Nama</td>
                        <td>{{ $father->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>No. WhatsApp</td>
                        <td>{{ $father->phone_number ?? '-' }}</td>
                    </tr>
                </table>
            @elseif(isset($waliType) && $waliType == 'mother')
                <p style="margin-bottom: 10px;"><em>Wali: Ibu Kandung</em></p>
                <table>
                    <tr>
                        <td>Nama</td>
                        <td>{{ $mother->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>No. WhatsApp</td>
                        <td>{{ $mother->phone_number ?? '-' }}</td>
                    </tr>
                </table>
            @elseif($guardian)
                <p style="margin-bottom: 10px;"><em>Wali: Lainnya</em></p>
                <table>
                    <tr>
                        <td>Nama</td>
                        <td>{{ $guardian->name }}</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>{{ $guardian->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Pendidikan Terakhir</td>
                        <td>{{ $guardian->education ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td>
                        <td>{{ $guardian->job ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>No. WhatsApp</td>
                        <td>{{ $guardian->phone_number ?? '-' }}</td>
                    </tr>
                </table>
            @else
                <p>Data wali tidak tersedia</p>
            @endif
        </div>
    </div>


    {{-- Data Sekolah --}}
    @if($registration)
        <div class="section">
            <div class="section-title">DATA SEKOLAH</div>
            <div class="section-content">
                <table>
                    <tr>
                        <td>Sekolah Asal</td>
                        <td>{{ $registration->previous_school_name }}</td>
                    </tr>
                    <tr>
                        <td>Jenjang</td>
                        <td>{{ $registration->previous_school_level }}</td>
                    </tr>
                    <tr>
                        <td>NPSN Sekolah Asal</td>
                        <td>{{ $registration->previous_school_npsn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Alamat Sekolah Asal</td>
                        <td>{{ $registration->previous_school_address }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding-top: 10px; border-top: 1px dashed #ccc;"></td>
                    </tr>
                    <tr>
                        <td>Sekolah Tujuan</td>
                        <td>{{ $registration->destinationInstitution->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Kelas Tujuan</td>
                        <td>{{ $registration->destination_class ? 'Kelas ' . $registration->destination_class : '-' }}</td>
                    </tr>
                    <tr>
                        <td>Sumber Pembiayaan</td>
                        <td>{{ $registration->funding_source }}</td>
                    </tr>
                </table>
            </div>
        </div>
    @endif

    <div class="note">
        <strong>Catatan:</strong> Simpan bukti pendaftaran ini dengan baik. Nomor pendaftaran digunakan untuk mengecek
        status pendaftaran dan pembayaran.
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
        <p>Pondok Pesantren Dar Al Tauhid - Sistem Penerimaan Santri Baru</p>
    </div>
</body>

</html>