<?php

namespace App\Services;

use App\Models\Pembeli\Product_pembeli;
use App\Models\Pembeli\Wishlist_pembeli;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     * Dapatkan produk rekomendasi berbasis wishlist buyer.
     * 
     * - Sumber utama: Produk yang tersimpan di wishlist buyer.
     * - Jika jumlah item wishlist < $limit, diperluas dengan produk dari kategori yang sama dengan wishlist.
     * - Jika buyer belum punya wishlist (atau wishlist kosong), fallback ke produk rating tertinggi / terbaru.
     */
    public function getRecommendedProducts(?int $userId = null, int $limit = 8): Collection
    {
        $wishlistProductIds = [];

        if ($userId) {
            $wishlistProductIds = Wishlist_pembeli::where('user_id', $userId)
                ->pluck('product_id')
                ->toArray();
        }

        $recommended = collect();

        if (!empty($wishlistProductIds)) {
            // 1. Ambil produk langsung dari wishlist buyer
            $wishlistProducts = Product_pembeli::whereIn('id', $wishlistProductIds)->get();
            $recommended = $recommended->concat($wishlistProducts);

            // 2. Jika wishlist belum mencapai limit, perluas dengan produk dari kategori serupa
            if ($recommended->count() < $limit) {
                $categories = $wishlistProducts->pluck('category')->filter()->unique();
                if ($categories->isNotEmpty()) {
                    $similarProducts = Product_pembeli::whereIn('category', $categories)
                        ->whereNotIn('id', $wishlistProductIds)
                        ->orderBy('rating', 'desc')
                        ->latest()
                        ->take($limit - $recommended->count())
                        ->get();

                    $recommended = $recommended->concat($similarProducts);
                }
            }
        }

        // 3. Fallback: Jika belum ada wishlist atau hasil kosong, tampilkan produk terlaris/terbaik
        if ($recommended->isEmpty()) {
            $recommended = Product_pembeli::orderBy('rating', 'desc')
                ->latest()
                ->take($limit)
                ->get();
        }

        // 4. Pastikan setiap item memiliki properti type ('buy' atau 'rent') untuk render kartu
        return $recommended->take($limit)->map(function ($item) {
            $item->type = $item->buy_price > 0 ? 'buy' : 'rent';
            return $item;
        });
    }

    /**
     * Dapatkan daftar kategori populer berdasarkan total transaksi valid (Beli + Sewa).
     * 
     * - Menghitung transaksi pembelian dari order_details (type: buy) dengan status order valid.
     * - Menghitung transaksi penyewaan dari order_details (type: rent) dan tabel rentals dengan status valid.
     * - Hanya transaksi terverifikasi (status: diproses, dikirim, selesai) yang dihitung; pesanan dibatalkan diabaikan.
     * - Hasil di-cache (default 6 jam) dan diperbarui secara berkala.
     */
    public function getPopularCategories(int $cacheHours = 6): Collection
    {
        return Cache::remember('popular_categories', now()->addHours($cacheHours), function () {
            // 1. Hitung transaksi pembelian (order_details type = buy dari orders yang valid)
            $buyCounts = DB::table('order_details')
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->join('products', 'order_details.product_id', '=', 'products.id')
                ->whereIn('orders.status', ['diproses', 'dikirim', 'selesai'])
                ->where('order_details.type', 'buy')
                ->whereNotNull('products.category')
                ->where('products.category', '!=', '')
                ->groupBy('products.category')
                ->select('products.category', DB::raw('SUM(order_details.qty) as total_qty'))
                ->pluck('total_qty', 'category');

            // 2. Hitung transaksi penyewaan dari order_details (type = rent)
            $rentCountsFromOrders = DB::table('order_details')
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->join('products', 'order_details.product_id', '=', 'products.id')
                ->whereIn('orders.status', ['diproses', 'dikirim', 'selesai'])
                ->where('order_details.type', 'rent')
                ->whereNotNull('products.category')
                ->where('products.category', '!=', '')
                ->groupBy('products.category')
                ->select('products.category', DB::raw('SUM(order_details.qty) as total_qty'))
                ->pluck('total_qty', 'category');

            // Hitung juga dari tabel rentals jika ada data rental aktif / selesai
            $rentCountsFromTable = DB::table('rentals')
                ->join('products', 'rentals.product_id', '=', 'products.id')
                ->whereNotIn('rentals.status', ['cancelled', 'dibatalkan'])
                ->whereNotNull('products.category')
                ->where('products.category', '!=', '')
                ->groupBy('products.category')
                ->select('products.category', DB::raw('COUNT(*) as total_rent'))
                ->pluck('total_rent', 'category');

            // 3. Ambil semua kategori produk yang aktif
            $allCategories = Product_pembeli::whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->pluck('category');

            // 4. Akumulasi skor transaksi per kategori (Beli + Sewa)
            $categoryScores = [];
            foreach ($allCategories as $cat) {
                $buy = (int) ($buyCounts[$cat] ?? 0);
                $rentFromOrders = (int) ($rentCountsFromOrders[$cat] ?? 0);
                $rentFromTable = (int) ($rentCountsFromTable[$cat] ?? 0);
                $rent = max($rentFromOrders, $rentFromTable);

                $categoryScores[$cat] = $buy + $rent;
            }

            // Urutkan kategori dari total transaksi terbanyak (descending)
            arsort($categoryScores);

            return collect(array_keys($categoryScores))->values();
        });
    }

    /**
     * Hapus cache kategori populer (misal saat ada event transaksi baru atau direset manual).
     */
    public function clearCategoryCache(): void
    {
        Cache::forget('popular_categories');
    }
}
