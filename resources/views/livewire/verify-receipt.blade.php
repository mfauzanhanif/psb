<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Dokumen - PSB Dar Al Tauhid</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 40px;
            max-width: 450px;
            width: 100%;
            text-align: center;
        }

        .valid .check-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: white;
        }

        .invalid .x-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: white;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .valid h1 {
            color: #059669;
        }

        .invalid h1 {
            color: #dc2626;
        }

        .subtitle {
            color: #6b7280;
            margin-bottom: 30px;
        }

        .info-table {
            width: 100%;
            text-align: left;
            border-collapse: collapse;
        }

        .info-table tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .info-table td {
            padding: 12px 0;
        }

        .info-table td:first-child {
            color: #6b7280;
            width: 140px;
        }

        .info-table td:last-child {
            font-weight: 600;
            color: #1f2937;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #9ca3af;
            font-size: 12px;
        }

        .logo {
            font-size: 14px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    @if($valid)
        <div class="card valid">
            <div class="check-icon">✓</div>
            <h1>DOKUMEN VALID</h1>
            <p class="subtitle">Nota pembayaran ini asli dan sah.</p>

            <table class="info-table">
                <tr>
                    <td>No. Nota</td>
                    <td>#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td>Nama Santri</td>
                    <td>{{ $student->full_name }}</td>
                </tr>
                <tr>
                    <td>No. Pendaftaran</td>
                    <td>{{ $student->registration_number }}</td>
                </tr>
                <tr>
                    <td>Jumlah</td>
                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Petugas</td>
                    <td>{{ $transaction->user?->name ?? 'System' }}</td>
                </tr>
            </table>

            <div class="footer">
                <p class="logo">Pondok Pesantren Dar Al Tauhid</p>
                <p>Sistem Penerimaan Santri Baru</p>
            </div>
        </div>
    @else
        <div class="card invalid">
            <div class="x-icon">✕</div>
            <h1>DOKUMEN TIDAK VALID</h1>
            <p class="subtitle">Token verifikasi tidak ditemukan atau tidak valid.</p>

            <div class="footer">
                <p class="logo">Pondok Pesantren Dar Al Tauhid</p>
                <p>Sistem Penerimaan Santri Baru</p>
            </div>
        </div>
    @endif
</body>

</html>