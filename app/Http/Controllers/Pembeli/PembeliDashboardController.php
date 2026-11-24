<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Pembeli\Product_pembeli;
use App\Models\Pembeli\Wishlist_pembeli;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\Auth;

class PembeliDashboardController extends Controller
{
    public function index(RecommendationService $recommendationService)
    {
        $userId = Auth::id();

        // 1. Rekomendasi Produk: Sumber dari wishlist buyer (diperluas dengan kategori serupa / fallback produk terbaik)
        $produks = $recommendationService->getRecommendedProducts($userId, 8);

        // 2. Kategori Populer: Dihitung dari akumulasi transaksi valid (Beli + Sewa), di-cache berkala
        $categories = $recommendationService->getPopularCategories(6);

        // Produk populer untuk bagian bawah
        $popularProduks = Product_pembeli::orderBy('rating', 'desc')
            ->take(8)
            ->get()
            ->map(function ($item) {
                $item->type = $item->buy_price ? 'buy' : 'rent';
                return $item;
            });

        $wishlistProduksIds = [];
        $hasWishlist = false;
        if ($userId) {
            $wishlistProduksIds = Wishlist_pembeli::where('user_id', $userId)
                ->pluck('product_id')
                ->toArray();
            $hasWishlist = !empty($wishlistProduksIds);
        }

        $hotItems = Product_pembeli::orderBy('rating', 'desc')->take(3)->get();

        return view('pembeli.buyer.dashboard_pembeli', compact(
            'produks',
            'popularProduks',
            'wishlistProduksIds',
            'categories',
            'hotItems',
            'hasWishlist'
        ));
    }
}
