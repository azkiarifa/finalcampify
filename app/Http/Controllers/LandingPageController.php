<?php

namespace App\Http\Controllers;

use App\Models\Pembeli\Product_pembeli;
use App\Models\Pembeli\Rental_pembeli;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandingPageController extends Controller
{
    /**
     * Tampilkan Landing Page Utama Campify.
     */
    public function index()
    {
        // Jika sudah login, jangan tampilkan landing page pengunjung, arahkan langsung ke dashboard sesuai role
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'seller') {
                return redirect()->route('seller.dashboard');
            } elseif ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('buyer.dashboard');
            }
        }

        // Produk Unggulan Sewa (Rent)
        $rentalProducts = Product_pembeli::where(function ($q) {
                $q->where('jenis_produk', 'sewa')
                  ->orWhere('rent_price', '>', 0);
            })
            ->orderBy('rating', 'desc')
            ->latest()
            ->take(4)
            ->get();

        // Jika belum ada filter rating, ambil produk sewa yang tersedia
        if ($rentalProducts->isEmpty()) {
            $rentalProducts = Product_pembeli::where('rent_price', '>', 0)
                ->orWhere('jenis_produk', 'sewa')
                ->latest()
                ->take(4)
                ->get();
        }

        // Produk Unggulan Beli (Buy)
        $buyProducts = Product_pembeli::where(function ($q) {
                $q->where('jenis_produk', 'jual')
                  ->orWhere('buy_price', '>', 0);
            })
            ->orderBy('rating', 'desc')
            ->latest()
            ->take(4)
            ->get();

        if ($buyProducts->isEmpty()) {
            $buyProducts = Product_pembeli::where('buy_price', '>', 0)
                ->orWhere('jenis_produk', 'jual')
                ->latest()
                ->take(4)
                ->get();
        }

        // Statistik Ekosistem Campify
        $totalProductsCount = Product_pembeli::count();
        $totalStoresCount = Store::count();
        $totalRentalsCount = Rental_pembeli::count();

        // Curated Categories dengan fotografi petualangan alam yang harmonis
        $featuredCategories = [
            [
                'key' => 'tenda',
                'name' => 'Tenda & Shelter',
                'icon' => 'tent',
                'desc' => 'Dome tent kapasitas 2-6 orang, flysheet tahan angin & pasak kokoh.',
                'image' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=600&q=80'
            ],
            [
                'key' => 'tas_gunung',
                'name' => 'Carrier & Tas Gunung',
                'icon' => 'backpack',
                'desc' => 'Tas carrier 40L - 75L dengan backsystem empuk & raincover waterproof.',
                'image' => 'https://images.unsplash.com/photo-1551632811-561732d1e306?auto=format&fit=crop&w=600&q=80'
            ],
            [
                'key' => 'alat_masak',
                'name' => 'Alat Masak & Kompor',
                'icon' => 'flame',
                'desc' => 'Kompor windproof portable, nesting teflon, dan perlengkapan makan alam.',
                'image' => 'https://images.unsplash.com/photo-1526772662000-3f88f10405ff?auto=format&fit=crop&w=600&q=80'
            ],
            [
                'key' => 'penerangan',
                'name' => 'Penerangan & Lentera',
                'icon' => 'flashlight',
                'desc' => 'Headlamp waterproof berdaya tahan tinggi, lentera gantung, & emergency gear.',
                'image' => 'https://images.unsplash.com/photo-1517824806704-9040b037703b?auto=format&fit=crop&w=600&q=80'
            ],
            [
                'key' => 'sepatu',
                'name' => 'Sepatu & Tracking Boots',
                'icon' => 'footprints',
                'desc' => 'Sepatu hiking grip kuat di medan basah & bebatuan terjal.',
                'image' => 'https://images.unsplash.com/photo-1520639888713-7851133b1ed0?auto=format&fit=crop&w=600&q=80'
            ],
            [
                'key' => 'aksesoris',
                'name' => 'Sleeping Gear & Matras',
                'icon' => 'bed',
                'desc' => 'Sleeping bag hangat, matras aluminium foil, & bantal angin praktis.',
                'image' => 'https://images.unsplash.com/photo-1517411032315-54ef2cb783bb?auto=format&fit=crop&w=600&q=80'
            ],
        ];

        // Urutkan kategori berdasarkan data transaksi kategori populer
        $popularCategoryNames = app(\App\Services\RecommendationService::class)->getPopularCategories(6);
        if ($popularCategoryNames->isNotEmpty()) {
            usort($featuredCategories, function ($a, $b) use ($popularCategoryNames) {
                $posA = $popularCategoryNames->search(function ($name) use ($a) {
                    return stripos($name, $a['key']) !== false || stripos($a['name'], $name) !== false;
                });
                $posB = $popularCategoryNames->search(function ($name) use ($b) {
                    return stripos($name, $b['key']) !== false || stripos($b['name'], $name) !== false;
                });
                $scoreA = $posA === false ? 999 : $posA;
                $scoreB = $posB === false ? 999 : $posB;
                return $scoreA <=> $scoreB;
            });
        }

        return view('landing.index', compact(
            'rentalProducts',
            'buyProducts',
            'totalProductsCount',
            'totalStoresCount',
            'totalRentalsCount',
            'featuredCategories'
        ));
    }
}
