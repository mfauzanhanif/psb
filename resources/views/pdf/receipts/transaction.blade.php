<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Nota Pembayaran</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            padding: 20px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            color: #666;
        }

        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 5px 0;
        }

        .info-table td:first-child {
            width: 120px;
            font-weight: bold;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .detail-table th,
        .detail-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        .detail-table th {
            background-color: #f0f0f0;
        }

        .detail-table .amount {
            text-align: right;
        }

        .summary-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .summary-table td {
            padding: 5px;
        }

        .summary-table .label {
            text-align: right;
            width: 70%;
        }

        .summary-table .value {
            text-align: right;
            font-weight: bold;
        }

        .total-row {
            font-size: 14px;
            border-top: 2px solid #333;
        }

        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            text-align: center;
            width: 45%;
        }

        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }

        .note {
            margin-top: 20px;
            padding: 10px;
            background: #f9f9f9;
            border: 1px dashed #ccc;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>PONDOK PESANTREN DAR AL TAUHID</h1>
        <p>Jl. KH. A. Syathori, RT/RW 01/01, Desa Arjawinangun</p>
        <p>Kec. Arjawinangun, Kab. Cirebon, Jawa Barat - 45162</p>
        <p>Telp: 085624568440 | Email: psb@daraltauhid.com</p>
    </div>

    <div class="title">NOTA PEMBAYARAN</div>

    <table class="info-table">
        <tr>
            <td>No. Nota</td>
            <td>: {{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>: {{ \Carbon\Carbon::parse($transaction->transaction_date)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td>No. Pendaftaran</td>
            <td>: {{ $student->registration_number }}</td>
        </tr>
        <tr>
            <td>Nama Santri</td>
            <td>: {{ $student->full_name }}</td>
        </tr>
    </table>

    <table class="detail-table">
        <thead>
            <tr>
                <th style="width: 40px">No</th>
                <th>Keterangan</th>
                <th style="width: 120px" class="amount">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $index => $trx)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>Pembayaran Biaya Pendaftaran{{ $trx->notes ? ' - ' . $trx->notes : '' }}</td>
                    <td class="amount">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td class="label">Total Bayar:</td>
            <td class="value">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Total Tagihan Keseluruhan:</td>
            <td class="value">Rp {{ number_format($totalBill, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Total Sudah Dibayar:</td>
            <td class="value">Rp {{ number_format($totalPaid, 0, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td class="label">Sisa Tagihan:</td>
            <td class="value">Rp {{ number_format($remaining, 0, ',', '.') }}</td>
        </tr>
    </table>

    <table style="width: 100%;">
        <tr>
            <td style="width: 50%"></td>
            <td style="text-align: center;">
                <p>Arjawinangun, {{ \Carbon\Carbon::parse($transaction->transaction_date)->translatedFormat('d F Y') }}
                </p>
                <p>{{ $transaction->user?->position ?? 'Bendahara' }}</p>
                <br>
                <!-- QR Code in signature area -->
                <div style="margin: 10px 0;">
                    <img src="{{ $qrCode }}" alt="QR Code" style="width: 80px; height: 80px;">
                </div>
                <p style="border-top: 1px solid #333; display: inline-block; padding-top: 5px; min-width: 120px;">
                    {{ $transaction->user?->name ?? 'Petugas' }}
                </p>
            </td>
        </tr>
    </table>

    <div class="note">
        <strong>Catatan:</strong>
        <ul style="margin: 5px 0 0 15px; padding: 0;">
            <li>Simpan nota ini sebagai bukti pembayaran yang sah.</li>
            <li>Scan QR Code di atas untuk verifikasi keaslian dokumen.</li>
        </ul>
    </div>
</body>

</html>