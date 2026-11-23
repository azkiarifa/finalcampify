<?php

namespace App\Http\Controllers\SellerController;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\SellerModels\Product_seller;
use App\Models\Store;
use Illuminate\Http\Request;

class ProductController_seller extends Controller
{
    /**
     * Tampilkan semua produk katalog seller (READ-ONLY)
     * Data produk tersinkronisasi otomatis dari modul Kelola Layanan.
     */
    public function index(Request $request)
    {
        $query = Product_seller::where('user_id', Auth::id())
            ->with(['service']);

        // filter jenis
        if ($request->filled('jenis')) {
            $query->where('jenis_produk', $request->jenis);
        }

        $products = $query->latest()->get();

        return view('SellerView.products.index_seller', compact('products'));
    }

    /**
     * Form tambah produk dialihkan ke Kelola Layanan (Centralized Input)
     */
    public function create()
    {
        return redirect()->route('seller.services.create')
            ->with('info', 'Input data produk baru kini terpusat melalui menu Kelola Layanan.');
    }

    /**
     * Simpan produk ditolak langsung demi menjaga integritas data
     */
    public function store(Request $request)
    {
        return redirect()->route('seller.services.create')
            ->with('error', 'Penambahan produk harus dilakukan melalui modul Kelola Layanan.');
    }

    /**
     * Form edit produk DITOLAK (Katalog bersifat Read-Only)
     */
    public function edit(Product_seller $product)
    {
        if ($product->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if ($product->service_id) {
            return redirect()->route('seller.services.edit', $product->service_id)
                ->with('info', 'Katalog produk bersifat Read-Only. Anda diarahkan ke menu Kelola Layanan untuk mengubah spesifikasi, detail, atau stok.');
        }

        return redirect()->route('seller.products.index')
            ->with('error', 'Katalog produk bersifat Read-Only untuk menjaga integritas data riwayat transaksi dan penyewaan. Silakan perbarui harga, spesifikasi, atau stok melalui menu Kelola Layanan.');
    }

    /**
     * Update produk DITOLAK (Katalog bersifat Read-Only)
     */
    public function update(Request $request, Product_seller $product)
    {
        abort(403, 'Aksi ditolak: Katalog produk bersifat Read-Only. Perubahan harga, detail, dan stok wajib dilakukan melalui modul Kelola Layanan.');
    }

    /**
     * Hapus produk dialihkan melalui penghapusan layanan terkait
     */
    public function destroy(Product_seller $product)
    {
        if ($product->user_id !== Auth::id()) {
            abort(403);
        }

        if ($product->service_id) {
            return redirect()->route('seller.services.index')
                ->with('error', 'Produk ini terhubung dengan Layanan. Silakan hapus atau nonaktifkan layanan terkait pada menu Kelola Layanan.');
        }

        // Hapus gambar jika ada
        if ($product->gambar || $product->image) {
            $oldFilename = basename($product->gambar ?: $product->image);
            $oldPath = public_path('assets/images/' . $oldFilename);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $product->delete();

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil dihapus!');
    }
}
