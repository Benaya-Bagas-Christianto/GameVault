<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;

class GameSeeder extends Seeder
{
    /**
     * Cara pakai:
     * 1. Isi array $games di bawah dengan data game dari database lama kamu
     * 2. Salin nama file gambar PERSIS seperti yang ada di folder assets/
     * 3. Jalankan: php artisan db:seed --class=GameSeeder
     *
     * TIPS CEPAT:
     * Daripada isi manual, export tabel tb_games dari phpMyAdmin
     * lalu import langsung ke database Laravel. Jauh lebih cepat!
     */
    public function run(): void
{
    // MATIKAN cek foreign key dulu sebelum truncate
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    Game::truncate();

    // NYALAKAN lagi setelah truncate
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $games = [
            // =============================================
            // CONTOH FORMAT DATA (Sesuaikan dengan milik kamu)
            // Kolom: name, image, price, genre, platform, stok, synopsis, description
            // =============================================

            // --- PC Games ---
            [
                'name'        => 'Cyberpunk 2077',
                'image'       => 'Cyberpunk-2077.jpg',    // Nama file gambar di folder public/assets/
                'price'       => 350000,
                'genre'       => 'RPG',
                'platform'    => 'PC',
                'stok'        => 99,
                'synopsis'    => 'Jelajahi Night City, kota masa depan yang penuh bahaya.',
                'description' => 'Cyberpunk 2077 adalah RPG aksi open-world yang berlatar tahun 2077 di Night City.',
            ],
            [
                'name'        => 'GTA V',
                'image'       => 'GTA-V.jpeg',
                'price'       => 150000,
                'genre'       => 'Action',
                'platform'    => 'PC',
                'stok'        => 99,
                'synopsis'    => 'Tiga penjahat bersatu dalam misi kejahatan terbesar di Los Santos.',
                'description' => 'Grand Theft Auto V adalah game aksi dunia terbuka yang dikembangkan oleh Rockstar Games.',
            ],

            // --- PlayStation Games ---
            [
                'name'        => 'The Witcher 3',
                'image'       => 'The-witcher3.jpg',
                'price'       => 200000,
                'genre'       => 'RPG',
                'platform'    => 'PlayStation',
                'stok'        => 99,
                'synopsis'    => 'Geralt of Rivia mencari Ciri di dunia yang penuh monster.',
                'description' => 'The Witcher 3: Wild Hunt adalah RPG aksi yang merupakan seri ketiga dari franchise The Witcher.',
            ],

            // --- Xbox Games ---
            [
                'name'        => 'Halo Infinite',
                'image'       => 'Halo.jpg',
                'price'       => 250000,
                'genre'       => 'FPS',
                'platform'    => 'Xbox',
                'stok'        => 99,
                'synopsis'    => 'Master Chief kembali untuk menyelamatkan galaksi.',
                'description' => 'Halo Infinite adalah game first-person shooter yang dikembangkan oleh 343 Industries.',
            ],

            // --- Mobile Games ---
            [
                'name'        => 'Mobile Legends',
                'image'       => 'Mobile-Legends.jpg',
                'price'       => 0,             // Gratis
                'genre'       => 'Action',
                'platform'    => 'Mobile',
                'stok'        => 999,
                'synopsis'    => 'Game MOBA 5v5 paling populer di Asia Tenggara.',
                'description' => 'Mobile Legends: Bang Bang adalah game MOBA mobile yang dikembangkan oleh Moonton.',
            ],

            // Tambahkan data game lainnya di sini...
            // Salin dari database lama kamu
        ];

        // Masukkan semua data ke database
        foreach ($games as $game) {
        Game::create($game);
    }

    $this->command->info('✅ GameSeeder berhasil! ' . count($games) . ' game ditambahkan.');
}
}
