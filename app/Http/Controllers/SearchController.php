<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request) {
        $keyword  = trim($request->get('q', ''));
        $platform = trim($request->get('platform', ''));
        $genre    = trim($request->get('genre', ''));
        $price    = trim($request->get('price', ''));
        $sort     = trim($request->get('sort', ''));

        $query = Game::with('reviews');

        if ($keyword) {
            $flexKeyword = str_replace(' ', '%', $keyword);
            $query->where('name', 'LIKE', "%$flexKeyword%");
        }
        
        if ($platform) {
            $query->where('platform', $platform);
        }
        
        if ($genre) {
            $query->where('genre', $genre);
        }

        if ($price === 'free') {
            $query->where('price', 0);
        }

        if ($sort === 'popular') {
            $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'desc');
        } elseif ($sort === 'trending') {
            $query->withCount('reviews')->orderBy('reviews_count', 'desc');
        }

        $games = $query->paginate(12)->appends($request->all());

        return view('search', compact('games','keyword','platform','genre','price','sort'));
    }

    public function autocomplete(Request $request)
    {
        // 1. Tangkap kata kunci yang diketik user dari Javascript
        $search = $request->query('query');

        if ($search) {
            $flexSearch = str_replace(' ', '%', $search);
            // 2. Cari di database, game yang namanya MIRIP (LIKE) dengan ketikan
            $games = Game::where('name', 'LIKE', "%{$flexSearch}%")
                        ->limit(5) // Batasi maksimal 5 game yang muncul di dropdown
                        ->get(['id', 'name', 'image', 'price']); // Ambil data yang dibutuhkan saja

            // 3. Kirim kembali datanya ke Javascript dalam bentuk JSON
            return response()->json($games);
        }

        // Jika kosong, kembalikan array kosong
        return response()->json([]);
    }
}