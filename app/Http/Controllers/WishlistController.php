<?php
namespace App\Http\Controllers;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function toggle(Request $request) {
        if (!Auth::check()) return response()->json(['status'=>'error','message'=>'Login dulu yuk!']);

        $game_id = $request->product_id ?? $request->game_id;
        $existing = Wishlist::where('user_id', Auth::id())->where('game_id', $game_id)->first();

        if ($existing) {
            $existing->delete();
            $total = Wishlist::where('user_id', Auth::id())->count();
            return response()->json(['status'=>'removed','message'=>'Dihapus dari Wishlist','wishlist_count'=>$total]);
        }

        Wishlist::create(['user_id'=>Auth::id(),'game_id'=>$game_id]);
        $total = Wishlist::where('user_id', Auth::id())->count();
        return response()->json(['status'=>'added','message'=>'Masuk Wishlist','wishlist_count'=>$total]);
    }

    public function get() {

        $data = Wishlist::with('game')->where('user_id', Auth::id())->latest()->get()->map(function($w) {
            return [
                'id'              => $w->game_id,
                'wishlist_id'     => $w->id,
                'name'            => $w->game->name,
                'image'           => 'assets/' . $w->game->image,
                'genre'           => $w->game->genre,
                'price'           => $w->game->price,
                'price_formatted' => $w->game->price == 0 ? 'Gratis' : 'Rp ' . number_format($w->game->price, 0, ',', '.'),
            ];
        });
        return response()->json(['status'=>'success','data'=>$data]);
    }
}