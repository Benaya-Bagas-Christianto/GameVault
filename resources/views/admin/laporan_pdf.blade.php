<!DOCTYPE html>
<html lang="id">
<head>
    <script>
        (function() {
            let currentUserId = "{{ Auth::check() ? Auth::id() : 'null' }}";
            if (localStorage.getItem('lastUserId') !== currentUserId) {
                localStorage.removeItem('cartCount');
                localStorage.removeItem('wishlist');
                localStorage.setItem('lastUserId', currentUserId);
            }
        })();
    </script>

    <meta charset="UTF-8">
    <title>Laporan Penjualan - GameVault</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Logo Game Vault 1.png') }}">
    <style>
        /* CSS Khusus untuk DomPDF agar rapi saat dicetak - Tema Gelap */
        @page { margin: 0px; }
        body {
            font-family: Helvetica, Arial, sans-serif;
            background-color: #0A0C10;
            color: #FFFFFF;
            font-size: 12px;
            margin: 0;
            padding: 40px;
        }
        .header {
            width: 100%;
            border-bottom: 1px solid #2A2D3A;
            padding-bottom: 25px;
            margin-bottom: 30px;
        }
        .header table {
            width: 100%;
            border: none;
        }
        .header td {
            border: none;
            padding: 0;
            color: #9CA3AF;
        }
        .title {
            font-size: 28px;
            font-weight: bold;
            color: #7C3AED;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 14px;
            color: #9CA3AF;
            margin-top: 5px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header strong {
            color: #E5E7EB;
        }
        .summary-box {
            background-color: #12151C;
            border-left: 5px solid #7C3AED;
            border: 1px solid #2A2D3A;
            border-left: 5px solid #7C3AED;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .summary-box table {
            width: 100%;
            border: none;
        }
        .summary-box td {
            border: none;
            padding: 5px 0;
            font-size: 14px;
            color: #E5E7EB;
        }
        .summary-box strong {
            color: #9CA3AF;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
            margin-right: 5px;
        }
        .total-highlight {
            font-size: 20px;
            font-weight: bold;
            color: #A78BFA;
            display: block;
            margin-top: 5px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: #12151C;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #2A2D3A;
            padding: 12px 10px;
            text-align: left;
        }
        table.data-table th {
            background-color: #1A1D27;
            font-weight: bold;
            color: #D1D5DB;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1px;
            border-bottom: 2px solid #7C3AED;
        }
        table.data-table td {
            color: #E5E7EB;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #0F1218;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .status-success {
            background-color: #1E1B4B;
            color: #A78BFA;
            padding: 4px 10px;
            border: 1px solid #4C1D95;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }
        .status-pending {
            background-color: #422006;
            color: #FBBF24;
            padding: 4px 10px;
            border: 1px solid #78350F;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #2A2D3A;
            text-align: right;
            font-size: 11px;
            color: #6B7280;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td style="width: 60%;">
                    @php
                        $logoPath = public_path('assets/Logo Game Vault 1.png');
                        $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
                    @endphp
                    <div style="margin-bottom: 5px;">
                        @if($logoData)
                            <img src="data:image/png;base64,{{ $logoData }}" style="height: 36px; vertical-align: middle; margin-right: 10px;">
                        @endif
                        <div class="title" style="display: inline-block; vertical-align: middle; margin: 0;">GAMEVAULT</div>
                    </div>
                    <div class="subtitle">Laporan Rekapitulasi Transaksi Penjualan</div>
                </td>
                <td style="width: 40%; text-align: right;">
                    <strong>Tanggal Cetak:</strong> {{ date('d F Y') }}<br>
                    <strong>Waktu:</strong> {{ date('H:i:s') }} WIB<br>
                    <strong>Dicetak Oleh:</strong> Administrator
                </td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <table>
            <tr>
                <td style="width: 50%;">
                    <strong>Total Seluruh Transaksi:</strong> {{ $transaksi->count() }} Pesanan
                </td>
                <td style="width: 50%; text-align: right;">
                    Total Pendapatan (Sukses): <br>
                    <span class="total-highlight">Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</span>
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">ID Order</th>
                <th style="width: 20%;">Tanggal & Waktu</th>
                <th style="width: 20%;">Nama Pembeli</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 20%; text-align: right;">Total Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $index => $t)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>#ORD-{{ $t->id }}</strong></td>
                <td>{{ $t->created_at ? $t->created_at->format('d/m/Y H:i') : '-' }}</td>
                <td>
                    {{ $t->nama_pembeli ?? 'Tidak ada nama' }}<br>
                    <span style="font-size: 10px; color: #9CA3AF;">({{ $t->username }})</span>
                </td>
                <td>
                    @if($t->status == 'Success')
                        <span class="status-success">Sukses</span>
                    @else
                        <span class="status-pending">Pending</span>
                    @endif
                </td>
                <td class="text-right"><strong>Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 30px;">Belum ada data transaksi di sistem.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>&copy; {{ date('Y') }} GameVault System. Dokumen ini di-generate secara otomatis dan sah.</p>
    </div>

@include('components.success-modal')
    @include('components.toast-notification')
</body>
</html>