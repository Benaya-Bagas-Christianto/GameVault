<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Review;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // LOGIKA PENAHANAN SEED RANDOM (AGAR BACK DARI DETAIL TIDAK BERUBAH)
        $hasKeepSeed = isset($_COOKIE['keep_seed']);
        $seed = session('game_seed');
        if (!$hasKeepSeed || !$seed) {
            $seed = rand(1, 999999);
            session(['game_seed' => $seed]);
        }
        
        if ($hasKeepSeed) {
            // Hapus cookie secara raw karena diset via JS
            setcookie('keep_seed', '', time() - 3600, '/');
            unset($_COOKIE['keep_seed']);
        }

        // 1. Carousel hero - game terbaru
        $carousel_games = Game::with('reviews')->inRandomOrder($seed)->limit(5)->get();



        // 3. Game populer (rating tertinggi)
        $populer_games = Game::with('reviews')->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'desc')->limit(10)->get();

        // 4. Katalog utama (tanpa paginasi di home, dibatasi 15)
        $games = Game::with('reviews')->latest()->limit(15)->get();

        // 5. Game terbaru
        $new_releases = Game::with('reviews')->latest()->limit(10)->get();

        // 6. Game trending (berdasarkan jumlah review terbanyak)
        $trending_games = Game::with('reviews')
            ->withCount('reviews')
            ->orderBy('reviews_count', 'desc')
            ->limit(10)->get();

        // 7. Game gratis
        $free_games = Game::with('reviews')->where('price', 0)->limit(20)->get();

        // 8. Statistik untuk banner
        $stats = [
            'total_games'    => Game::count(),
            'total_reviews'  => \App\Models\Review::count(),
        ];

        // 9. Genre unik untuk section genre
        $genres = Game::select('genre')->distinct()->pluck('genre')
            ->flatMap(fn($g) => explode(',', $g))
            ->map(fn($g) => trim($g))
            ->filter()
            ->unique()
            ->values()
            ->take(8);

        return view('index', compact(
            'carousel_games',
            'populer_games',
            'games',
            'new_releases',
            'trending_games',
            'free_games',
            'stats',
            'genres'
        ));
    }
}
