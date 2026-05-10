<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Auth::user()->wishlists()->with('product')->get();
        return view('wishlist', compact('wishlists'));
    }

    public function add(Product $product)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())
                          ->where('product_id', $product->id)
                          ->first();

        if (!$wishlist) {
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
            ]);
            return back()->with('success', 'Product added to wishlist!');
        } else {
            $wishlist->delete();
            return back()->with('success', 'Product removed from wishlist!');
        }
    }

    public function remove(Wishlist $wishlist)
    {
        if ($wishlist->user_id == Auth::id()) {
            $wishlist->delete();
            return back()->with('success', 'Product removed from wishlist!');
        }
        return back()->with('error', 'Unauthorized action.');
    }
}
