<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">
    <title>Invoice GameVault #{{ $trx->id }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">
    <style>
        @page { margin: 0px; }
        body { font-family: Helvetica, Arial, sans-serif; background-color: #0A0C10; color: #FFFFFF; margin: 0; padding: 40px; }
        .header { border-bottom: 1px solid #2A2D3A; padding-bottom: 25px; margin-bottom: 35px; }
        .header-table { width: 100%; }
        .header-table td { vertical-align: middle; }
        .header h1 { margin: 0; color: #7C3AED; font-size: 28px; text-transform: uppercase; letter-spacing: 3px; font-weight: bold; }
        .header p { margin: 5px 0 0 0; color: #9CA3AF; font-size: 12px; letter-spacing: 1px; }
        
        .info-container { width: 100%; margin-bottom: 40px; border-collapse: separate; border-spacing: 15px 0; margin-left: -15px; width: calc(100% + 30px); }
        .info-box { background-color: #12151C; border: 1px solid #2A2D3A; border-radius: 8px; padding: 20px; vertical-align: top; width: 50%; }
        .info-title { font-weight: bold; color: #9CA3AF; font-size: 11px; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1px; }
        .info-content { font-size: 16px; font-weight: bold; color: #FFFFFF; }
        .info-sub { font-size: 13px; color: #6B7280; margin-top: 4px; }

        .table-items { width: 100%; border-collapse: collapse; margin-bottom: 40px; background-color: #12151C; }
        .table-items th { background-color: #1A1D27; border-bottom: 2px solid #7C3AED; padding: 15px; text-align: left; font-size: 12px; text-transform: uppercase; color: #D1D5DB; letter-spacing: 1px; }
        .table-items td { border-bottom: 1px solid #2A2D3A; padding: 15px; font-size: 14px; color: #E5E7EB; }
        .table-items tr:nth-child(even) td { background-color: #0F1218; }
        .text-right { text-align: right !important; }

        .total-box { float: right; width: 300px; background-color: #12151C; border: 1px solid #2A2D3A; border-radius: 8px; padding: 20px; }
        .total-row { width: 100%; font-size: 22px; font-weight: bold; color: #7C3AED; }
        .total-row td { padding: 10px 0; }
        .total-label { color: #FFFFFF; font-size: 16px; font-weight: bold; text-transform: uppercase; }
        
        .footer { clear: both; margin-top: 60px; text-align: center; color: #6B7280; font-size: 12px; padding-top: 20px; border-top: 1px solid #2A2D3A; line-height: 1.6; }
        .badge-success { background-color: #1E1B4B; color: #A78BFA; padding: 6px 12px; border: 1px solid #4C1D95; border-radius: 4px; font-size: 12px; font-weight: bold; text-transform: uppercase; display: inline-block; }
    </style>

    
</head>
<body>

    <table class="header-table header">
        <tr>
            <td>
                @php
                    $logoPath = public_path('assets/Logo Game Vault 1.png');
                    $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
                @endphp
                <div style="margin-bottom: 8px;">
                    @if($logoData)
                        <img src="data:image/png;base64,{{ $logoData }}" style="height: 36px; vertical-align: middle; margin-right: 10px;">
                    @endif
                    <h1 style="display: inline-block; vertical-align: middle; margin: 0;">GAMEVAULT</h1>
                </div>
                <p>Digital Game License Provider &bull; Official Receipt</p>
            </td>
            <td class="text-right">
                <div style="font-size: 24px; color: #4B5563; font-weight: bold;">INVOICE</div>
            </td>
        </tr>
    </table>

    <table class="info-container">
        <tr>
            <td class="info-box">
                <div class="info-title">Ditagihkan Kepada:</div>
                <div class="info-content">{{ $user->name ?? $user->username }}</div>
                <div class="info-sub">{{ $user->email }}</div>
            </td>
            <td class="info-box text-right">
                <div class="info-title">Detail Transaksi:</div>
                <div class="info-content">Order ID: #{{ $trx->id }}</div>
                <div class="info-sub" style="margin-bottom: 10px;">Tanggal: {{ \Carbon\Carbon::parse($trx->created_at)->format('d M Y, H:i') }} WIB</div>
                <div><span class="badge-success">LUNAS</span></div>
            </td>
        </tr>
    </table>

    <table class="table-items">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="65%">Deskripsi Produk (Lisensi Game)</th>
                <th width="30%" class="text-right">Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td class="text-right">Rp {{ number_format($item->harga_saat_beli, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="total-box">
        <tr class="total-row">
            <td class="total-label">TOTAL BAYAR</td>
            <td class="text-right">Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer">
        Terima kasih telah berbelanja di GameVault!<br>
        Lisensi digital Anda telah diaktifkan di akun Anda. Dokumen ini sah dan diterbitkan secara otomatis oleh sistem.
    </div>

@include('components.toast-notification')
</body>
</html>