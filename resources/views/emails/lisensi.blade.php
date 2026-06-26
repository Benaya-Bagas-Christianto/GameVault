<!DOCTYPE html>
<html>

<head>

    <style>
        body {
            background-color: #050505;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #0A0C10;
            border: 1px solid #1f1f1f;
            border-radius: 15px;
            padding: 30px;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .title {
            color: #7C3AED;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: 3px;
            margin: 0;
            text-transform: uppercase;
            display: inline-block;
            vertical-align: middle;
        }

        .game-item {
            background-color: #12151C;
            border: 1px solid #1f1f1f;
            border-left: 4px solid #7C3AED;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        .game-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #ffffff;
        }

        .key-box {
            background-color: #050505;
            color: #a78bfa;
            padding: 15px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 20px;
            text-align: center;
            letter-spacing: 4px;
            border: 1px dashed #7C3AED;
            border-radius: 8px;
            font-weight: bold;
            margin-top: 10px;
        }

        .footer {
            text-align: center;
            color: #666666;
            font-size: 12px;
            margin-top: 30px;
            border-top: 1px solid #1f1f1f;
            padding-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div style="margin-bottom: 10px;">
                <img src="{{ $message->embed(public_path('assets/Logo Game Vault 1.png')) }}" alt="Logo" style="width: 32px; height: 32px; vertical-align: middle; margin-right: 10px;">
                <p class="title">GAMEVAULT</p>
            </div>
            <p style="color: #888; margin-top: 5px; font-size: 14px; letter-spacing: 1px;">Lisensi Game Digital & Bukti Pembelian</p>
        </div>

        <p>Halo <strong>{{ $user->name ?? $user->username }}</strong>,</p>
        <p style="color: #ccc;">Pembayaranmu telah berhasil diverifikasi! Terima kasih telah mempercayakan GameVault sebagai tempat belanja lisensi game digitalmu.</p>
        <p style="color: #ccc;">Berikut adalah daftar <strong>Activation Key (Lisensi)</strong> untuk game yang baru saja kamu beli. Lisensi ini bisa kamu <em>redeem</em> di platform yang sesuai:</p>

        @foreach($details as $item)
        <div class="game-item">
            <div class="game-name">{{ $item->name }}</div>
            <div style="font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: 1px;">Activation Key / Product Code:</div>
            <div class="key-box">
                GV-{{ strtoupper(\Illuminate\Support\Str::random(4)) }}-{{ strtoupper(\Illuminate\Support\Str::random(4)) }}-{{ strtoupper(\Illuminate\Support\Str::random(4)) }}
            </div>
        </div>
        @endforeach

        <p style="margin-top: 30px; color: #aaa; font-size: 13px; background-color: #12151C; padding: 15px; border-radius: 8px; border: 1px solid #1f1f1f;">
            📄 <strong>Catatan:</strong> Kami juga telah melampirkan <strong>Struk Bukti Pembayaran Resmi</strong> dalam format PDF pada email ini untuk keperluan pencatatanmu.
        </p>

        <p style="text-align: center; margin-top: 30px; font-weight: bold; color: #7C3AED; font-size: 18px;">Selamat bermain dan jadilah legenda!</p>

        <div class="footer">
            &copy; {{ date('Y') }} GameVault System.<br>Email ini dibuat otomatis oleh sistem, mohon untuk tidak membalas email ini.
        </div>
    </div>
</body>

</html>