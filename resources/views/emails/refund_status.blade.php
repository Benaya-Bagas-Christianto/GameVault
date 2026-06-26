<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Refund GameVault</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #050505; color: #ffffff; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #0A0C10; border: 1px solid #1f1f1f; border-radius: 12px; overflow: hidden; }
        .header { background-color: #12151C; padding: 30px; text-align: center; border-bottom: 1px solid #1f1f1f; }
        .header img { max-width: 150px; }
        .content { padding: 40px 30px; }
        .title { font-size: 24px; font-weight: 800; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 2px; }
        .text { color: #a0a0a0; line-height: 1.6; margin-bottom: 30px; }
        .box { background-color: #12151C; border: 1px solid #1f1f1f; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
        .box-title { color: #ffffff; font-weight: bold; margin-bottom: 10px; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        .box-value { color: #7C3AED; font-size: 18px; font-weight: bold; }
        .status-badge { display: inline-block; padding: 8px 16px; border-radius: 4px; font-weight: bold; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        .status-approved { background-color: rgba(34, 197, 94, 0.1); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.2); }
        .status-rejected { background-color: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); }
        .btn { display: inline-block; padding: 12px 24px; background-color: #7C3AED; color: #ffffff; text-decoration: none; font-weight: bold; border-radius: 8px; text-transform: uppercase; letter-spacing: 1px; font-size: 14px; }
        .footer { background-color: #12151C; padding: 20px; text-align: center; border-top: 1px solid #1f1f1f; color: #666; font-size: 12px; }
        .logo-text { color: #7C3AED; font-size: 28px; font-weight: 900; letter-spacing: 3px; margin: 0; text-transform: uppercase; display: inline-block; vertical-align: middle; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div style="margin-bottom: 10px;">
                <img src="{{ $message->embed(public_path('assets/Logo Game Vault 1.png')) }}" alt="Logo" style="width: 32px; height: 32px; vertical-align: middle; margin-right: 10px;">
                <p class="logo-text">GAMEVAULT</p>
            </div>
            <p style="color: #888; margin-top: 5px; font-size: 14px; letter-spacing: 1px;">Pemberitahuan Status Refund</p>
        </div>
        <div class="content">
            <h1 class="title">
                @if($status === 'approved')
                    Refund Disetujui
                @else
                    Refund Ditolak
                @endif
            </h1>
            
            <p class="text">
                Halo <strong>{{ $refund->user->name }}</strong>,<br><br>
                @if($status === 'approved')
                    Kabar gembira! Pengajuan refund Anda untuk game <strong>{{ $gameName }}</strong> telah disetujui oleh admin kami. Dana Anda akan segera dikembalikan sesuai dengan metode pembayaran yang Anda gunakan, dan lisensi game tersebut telah ditarik dari akun Anda.
                @else
                    Mohon maaf, pengajuan refund Anda untuk game <strong>{{ $gameName }}</strong> tidak dapat disetujui oleh admin kami. Anda tetap bisa memainkan game tersebut di Library Anda.
                @endif
            </p>

            <div class="box">
                <div class="box-title">Status Pengajuan</div>
                <div class="status-badge {{ $status === 'approved' ? 'status-approved' : 'status-rejected' }}">
                    {{ $status === 'approved' ? 'DISETUJUI' : 'DITOLAK' }}
                </div>
                
                <div style="margin-top: 20px;">
                    <div class="box-title">Game yang Diajukan</div>
                    <div class="box-value">{{ $gameName }}</div>
                </div>

                <div style="margin-top: 20px;">
                    <div class="box-title">Alasan Refund</div>
                    <div style="color: #a0a0a0; font-style: italic;">"{{ $refund->alasan }}"</div>
                </div>
            </div>

            <p class="text" style="font-size: 13px;">
                Jika Anda memiliki pertanyaan lebih lanjut, jangan ragu untuk menghubungi tim dukungan pelanggan kami.
            </p>

            <div style="text-align: center; margin-top: 40px;">
                <a href="{{ url('/orders') }}" class="btn">Lihat Riwayat Transaksi</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} GameVault. All rights reserved.<br>
            Email ini dibuat otomatis, mohon tidak membalas.
        </div>
    </div>
</body>
</html>
