<?php
namespace App\Http\Controllers;
use App\Models\Keranjang;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function get() {
        $items = Keranjang::with('game')->where('user_id', Auth::id())->get()->map(function($k) {
            return [
                'cart_id'         => $k->id,
                'game_id'         => $k->game_id,
                'quantity'        => $k->quantity,
                'name'            => $k->game->name,
                'genre'           => explode(',', $k->game->genre)[0] ?? 'N/A',
                'platform'        => explode(',', $k->game->platform)[0] ?? 'PC',
                'image'           => asset('assets/' . $k->game->image),
                'price'           => $k->game->price,
                'price_formatted' => $k->game->price == 0 ? 'Gratis' : 'Rp ' . number_format($k->game->price, 0, ',', '.'),
            ];
        });
        $total = Keranjang::where('user_id', Auth::id())->count();
        return response()->json(['status'=>'success','data'=>$items, 'cart_count'=>$total]);
    }

    public function add(Request $request) {
        $game = Game::find($request->product_id ?? $request->game_id);
        if (!$game || $game->stok < 1) return response()->json(['status'=>'error','message'=>'Stok tidak mencukupi']);

        $keranjang = Keranjang::where('user_id', Auth::id())->where('game_id', $game->id)->first();
        if ($keranjang) {
            $total = Keranjang::where('user_id', Auth::id())->count();
            return response()->json(['status'=>'success','message'=>'Game sudah ada di keranjang','cart_count'=>$total]);
        } else {
            Keranjang::create(['user_id'=>Auth::id(),'game_id'=>$game->id,'quantity'=>1]);
        }

        $total = Keranjang::where('user_id', Auth::id())->count();
        return response()->json(['status'=>'success','message'=>'Game berhasil masuk keranjang','cart_count'=>$total]);
    }

    public function remove(Request $request) {
        // FIX 2: BISA MENGHAPUS PAKAI ID KERANJANG MAUPUN ID GAME 
        $id = $request->cart_id ?? $request->id ?? $request->game_id;
        
        Keranjang::where('user_id', Auth::id())
            ->where(function($q) use ($id) {
                $q->where('id', $id)->orWhere('game_id', $id);
            })->delete();
        
        $total = Keranjang::where('user_id', Auth::id())->count();
        return response()->json(['status'=>'success','message'=>'Berhasil dihapus','cart_count'=>$total]);
    }
}