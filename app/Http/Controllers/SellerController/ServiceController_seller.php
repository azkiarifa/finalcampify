<?php

namespace App\Http\Controllers\SellerController;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceDetail;
use App\Models\ServiceStockPurchase;
use App\Models\Category;
use App\Models\SellerModels\Product_seller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceController_seller extends Controller
{
    /**
     * Tampilkan semua layanan milik seller login
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $store = Store::where('user_id', $userId)->first();
        $storeId = $store?->id;

        $query = Service::where(function ($q) use ($userId, $storeId) {
            $q->where('user_id', $userId);
            if ($storeId) {
                $q->orWhere('store_id', $storeId);
            }
        })->with(['category', 'details', 'sellerProduct', 'latestPurchase']);

        if ($request->filled('type')) {
            $query->where('service_type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $services = $query->latest()->get();
        $categories = Category::orderBy('name')->get();

        return view('SellerView.services.index_seller', compact('services', 'categories'));
    }

    /**
     * Form input layanan baru
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('SellerView.services.create_seller', compact('categories'));
    }

    /**
     * Simpan layanan baru, riwayat pengadaan stok, dan sinkronkan otomatis ke katalog produk
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'                 => 'required|string|max:255',
            'category_id'          => 'required|exists:categories,id',
            'service_type'         => 'required|in:jual,sewa',
            'base_price'           => 'required|numeric|min:0',
            'stock'                => 'required|integer|min:0',
            'escrow_deposit'       => 'nullable|numeric|min:0',
            'min_duration_days'    => 'nullable|integer|min:1',
            'description'          => 'nullable|string',
            'gambar'               => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            // Riwayat Pembelian / Pengadaan Unit
            'purchase_date'        => 'nullable|date',
            'purchase_store'       => 'nullable|string|max:255',
            'purchase_price'       => 'nullable|numeric|min:0',
            'condition'            => 'nullable|string|max:50',
            // Rincian Kelengkapan Paket
            'details'              => 'nullable|array',
            'details.*.item_name'  => 'required_with:details|string|max:255',
            'details.*.additional_price' => 'nullable|numeric|min:0',
        ]);

        $store = Store::where('user_id', Auth::id())->first();

        // Upload gambar
        $imagePath = null;
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images'), $filename);
            $imagePath = $filename;
        }

        $category = Category::find($request->category_id);

        DB::transaction(function () use ($request, $store, $category, $imagePath) {
            $purchasePrice = (int) ($request->purchase_price ?? 0);
            
            // Perhitungan otomatis escrow jaminan jika sewa dan seller tidak mengisi manual
            $escrowAmount = $request->escrow_deposit ?? 0;
            if ($request->service_type === 'sewa' && empty($request->escrow_deposit) && $purchasePrice > 0) {
                // Default jaminan = 50% dari harga beli modal
                $escrowAmount = (int) ($purchasePrice * 0.5);
            }

            // 1. Simpan Header Layanan
            $service = Service::create([
                'store_id'          => $store?->id,
                'user_id'           => Auth::id(),
                'category_id'       => $category->id,
                'name'              => $request->name,
                'service_type'      => $request->service_type,
                'base_price'        => $request->base_price,
                'stock'             => $request->stock,
                'escrow_deposit'    => $request->service_type === 'sewa' ? $escrowAmount : 0,
                'min_duration_days' => $request->service_type === 'sewa' ? ($request->min_duration_days ?? 1) : 1,
                'description'       => $request->description,
                'image'             => $imagePath,
                'status'            => 'active',
            ]);

            // 2. Simpan Riwayat Pengadaan / Pembelian Stok Unit
            if (!empty($request->purchase_store) || $purchasePrice > 0) {
                ServiceStockPurchase::create([
                    'service_id'     => $service->id,
                    'purchase_date'  => $request->purchase_date ?? now()->toDateString(),
                    'purchase_store' => $request->purchase_store ?? 'Toko / Distributor',
                    'purchase_price' => $purchasePrice,
                    'quantity'       => $request->stock,
                    'condition'      => $request->condition ?? 'baru',
                    'notes'          => 'Pengadaan stok awal unit',
                ]);
            }

            // 3. Simpan Detail Kelengkapan & Hitung Biaya Tambahan
            $totalAdditional = 0;
            if ($request->has('details') && is_array($request->details)) {
                foreach ($request->details as $item) {
                    if (!empty($item['item_name'])) {
                        $addPrice = isset($item['additional_price']) ? (int) $item['additional_price'] : 0;
                        $totalAdditional += $addPrice;

                        ServiceDetail::create([
                            'service_id'       => $service->id,
                            'item_name'        => $item['item_name'],
                            'specification'    => $item['specification'] ?? null,
                            'additional_price' => $addPrice,
                            'is_included'      => true,
                        ]);
                    }
                }
            }

            // 4. Kalkulasi Otomatis Total Harga Produk
            $finalPrice = (int) $service->base_price + $totalAdditional;

            // Evaluasi flag konten
            $flagReasons = \App\Models\Product::flagReasonsFor([
                'nama_produk' => $service->name,
                'harga'       => $finalPrice,
                'deskripsi'   => $service->description,
            ]);

            // 5. Sinkronisasi Otomatis ke Tabel Products (Read-Only Projection)
            Product_seller::create([
                'user_id'       => Auth::id(),
                'seller_id'     => Auth::id(),
                'store_id'      => $store?->id,
                'service_id'    => $service->id,
                'name'          => $service->name,
                'nama_produk'   => $service->name,
                'description'   => $service->description,
                'deskripsi'     => $service->description,
                'price'         => $finalPrice,
                'harga'         => $finalPrice,
                'buy_price'     => $service->service_type === 'jual' ? $finalPrice : 0,
                'rent_price'    => $service->service_type === 'sewa' ? $finalPrice : 0,
                'escrow_amount' => $service->service_type === 'sewa' ? $service->escrow_deposit : 0,
                'category'      => $category->name,
                'kategori'      => $category->name,
                'category_id'   => $category->id,
                'kategori_id'   => $category->id,
                'jenis_produk'  => $service->service_type,
                'is_rental'     => $service->service_type === 'sewa',
                'stock'         => $service->stock,
                'stok'          => $service->stock,
                'image'         => $imagePath,
                'gambar'        => $imagePath,
                'is_locked'     => true,
                'status'        => 'waiting',
                'flag_reason'   => $flagReasons ? implode(', ', $flagReasons) : null,
            ]);
        });

        return redirect()->route('seller.services.index')
            ->with('success', 'Layanan & riwayat pengadaan stok berhasil disimpan! Produk otomatis diterbitkan ke katalog.');
    }

    /**
     * Form edit layanan, stok, dan riwayat pengadaan
     */
    public function edit(Service $service)
    {
        $this->authorizeOwner($service);

        $categories = Category::orderBy('name')->get();
        $service->load(['details', 'stockPurchases']);

        return view('SellerView.services.edit_seller', compact('service', 'categories'));
    }

    /**
     * Update layanan, penyesuaian detail & restock batch, serta kalkulasi ulang harga katalog
     */
    public function update(Request $request, Service $service)
    {
        $this->authorizeOwner($service);

        $request->validate([
            'name'                 => 'required|string|max:255',
            'category_id'          => 'required|exists:categories,id',
            'service_type'         => 'required|in:jual,sewa',
            'base_price'           => 'required|numeric|min:0',
            'stock'                => 'required|integer|min:0',
            'escrow_deposit'       => 'nullable|numeric|min:0',
            'min_duration_days'    => 'nullable|integer|min:1',
            'description'          => 'nullable|string',
            'gambar'               => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            // Restock batch baru (opsional)
            'restock_quantity'     => 'nullable|integer|min:1',
            'restock_purchase_price' => 'nullable|numeric|min:0',
            'restock_purchase_store' => 'nullable|string|max:255',
            'restock_purchase_date'  => 'nullable|date',
            'restock_condition'      => 'nullable|string|max:50',
            // Details
            'details'              => 'nullable|array',
            'details.*.item_name'  => 'required_with:details|string|max:255',
            'details.*.additional_price' => 'nullable|numeric|min:0',
        ]);

        $category = Category::find($request->category_id);
        $imagePath = $service->image;

        if ($request->hasFile('gambar')) {
            if ($service->image) {
                $oldFile = public_path('assets/images/' . basename($service->image));
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images'), $filename);
            $imagePath = $filename;
        }

        DB::transaction(function () use ($request, $service, $category, $imagePath) {
            $currentStock = (int) $request->stock;

            // Jika ada penambahan batch restock unit baru
            if (!empty($request->restock_quantity) && $request->restock_quantity > 0) {
                $newQty = (int) $request->restock_quantity;
                $currentStock += $newQty;

                ServiceStockPurchase::create([
                    'service_id'     => $service->id,
                    'purchase_date'  => $request->restock_purchase_date ?? now()->toDateString(),
                    'purchase_store' => $request->restock_purchase_store ?? 'Restock Unit',
                    'purchase_price' => (int) ($request->restock_purchase_price ?? 0),
                    'quantity'       => $newQty,
                    'condition'      => $request->restock_condition ?? 'baru',
                    'notes'          => 'Penambahan stok unit baru (Restock)',
                ]);
            }

            // 1. Update Layanan Utama (termasuk stok baru & harga dasar)
            $service->update([
                'category_id'       => $category->id,
                'name'              => $request->name,
                'service_type'      => $request->service_type,
                'base_price'        => $request->base_price,
                'stock'             => $currentStock,
                'escrow_deposit'    => $request->service_type === 'sewa' ? ($request->escrow_deposit ?? 0) : 0,
                'min_duration_days' => $request->service_type === 'sewa' ? ($request->min_duration_days ?? 1) : 1,
                'description'       => $request->description,
                'image'             => $imagePath,
            ]);

            // 2. Re-sync Service Details
            $service->details()->delete();
            $totalAdditional = 0;
            if ($request->has('details') && is_array($request->details)) {
                foreach ($request->details as $item) {
                    if (!empty($item['item_name'])) {
                        $addPrice = isset($item['additional_price']) ? (int) $item['additional_price'] : 0;
                        $totalAdditional += $addPrice;

                        ServiceDetail::create([
                            'service_id'       => $service->id,
                            'item_name'        => $item['item_name'],
                            'specification'    => $item['specification'] ?? null,
                            'additional_price' => $addPrice,
                            'is_included'      => true,
                        ]);
                    }
                }
            }

            // 3. Kalkulasi Ulang Harga Katalog Produk
            $recalculatedPrice = (int) $service->base_price + $totalAdditional;

            // Evaluasi flag keamanan konten
            $flagReasons = \App\Models\Product::flagReasonsFor([
                'nama_produk' => $service->name,
                'harga'       => $recalculatedPrice,
                'deskripsi'   => $service->description,
            ]);

            // 4. Perbarui Otomatis ke Katalog Produk (Terkunci dari edit langsung)
            $productData = [
                'name'          => $service->name,
                'nama_produk'   => $service->name,
                'description'   => $service->description,
                'deskripsi'     => $service->description,
                'price'         => $recalculatedPrice,
                'harga'         => $recalculatedPrice,
                'buy_price'     => $service->service_type === 'jual' ? $recalculatedPrice : 0,
                'rent_price'    => $service->service_type === 'sewa' ? $recalculatedPrice : 0,
                'escrow_amount' => $service->service_type === 'sewa' ? $service->escrow_deposit : 0,
                'category'      => $category->name,
                'kategori'      => $category->name,
                'category_id'   => $category->id,
                'kategori_id'   => $category->id,
                'jenis_produk'  => $service->service_type,
                'is_rental'     => $service->service_type === 'sewa',
                'stock'         => $service->stock,
                'stok'          => $service->stock,
                'image'         => $imagePath,
                'gambar'        => $imagePath,
                'is_locked'     => true,
                'status'        => 'waiting',
                'flag_reason'   => $flagReasons ? implode(', ', $flagReasons) : null,
            ];

            Product_seller::updateOrCreate(
                ['service_id' => $service->id],
                array_merge($productData, [
                    'user_id'   => Auth::id(),
                    'seller_id' => Auth::id(),
                    'store_id'  => $service->store_id,
                ])
            );
        });

        return redirect()->route('seller.services.index')
            ->with('success', 'Layanan & stok berhasil diperbarui! Harga katalog produk telah dikalkulasi ulang secara otomatis.');
    }

    /**
     * Hapus layanan dan produk terkait
     */
    public function destroy(Service $service)
    {
        $this->authorizeOwner($service);

        DB::transaction(function () use ($service) {
            Product_seller::where('service_id', $service->id)->delete();
            $service->details()->delete();
            $service->stockPurchases()->delete();
            if ($service->image) {
                $file = public_path('assets/images/' . basename($service->image));
                if (file_exists($file)) {
                    @unlink($file);
                }
            }
            $service->delete();
        });

        return redirect()->route('seller.services.index')
            ->with('success', 'Layanan beserta item katalog produk terkait berhasil dihapus.');
    }

    private function authorizeOwner(Service $service): void
    {
        $userId = Auth::id();
        $store = Store::where('user_id', $userId)->first();
        if ($service->user_id !== $userId && $service->store_id !== $store?->id) {
            abort(403, 'Akses ditolak: Anda bukan pemilik layanan ini.');
        }
    }
}
