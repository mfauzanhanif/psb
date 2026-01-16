<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            padding: 20px;
            font-size: 11px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10px;
            color: #666;
        }

        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .period {
            text-align: center;
            font-size: 11px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .amount {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>PONDOK PESANTREN DAR AL TAUHID</h1>
        <p>Jl. KH. A. Syathori, RT/RW 01/01, Desa Arjawinangun</p>
        <p>Kec. Arjawinangun, Kab. Cirebon, Jawa Barat - 45162</p>
    </div>

    <div class="title">LAPORAN TRANSAKSI</div>
    <div class="period">
        Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} -
        {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th style="width: 70px">Tanggal</th>
                <th style="width: 80px">No. Daftar</th>
                <th>Nama Santri</th>
                <th>Keterangan</th>
                <th style="width: 90px" class="amount">Jumlah</th>
                <th style="width: 50px">Metode</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $index => $trx)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d/m/Y') }}</td>
                    <td>{{ $trx->student?->registration_number ?? '-' }}</td>
                    <td>{{ $trx->student?->full_name ?? '-' }}</td>
                    <td>Biaya Pendaftaran</td>
                    <td class="amount">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                    <td>{{ $trx->payment_method === 'cash' ? 'Cash' : 'Transfer' }}</td>
                    <td>{{ $trx->user?->name ?? 'System' }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" style="text-align: right">TOTAL</td>
                <td class="amount">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>
</body>

</html>
