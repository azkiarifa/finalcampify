<?php

namespace App\Http\Controllers\SellerController;

use App\Http\Controllers\Controller;
use App\Models\SellerOrder;
use App\Services\SellerOrderService;
use Illuminate\Http\Request;

class OrderController_seller extends Controller
{
    public function __construct(private SellerOrderService $sellerOrderService)
    {
    }

    public function index(Request $request)
    {
        $query = $this->sellerOrders()->whereDoesntHave('items', fn ($query) => $query->where('type', 'rent'));

        if ($request->filled('status')) {
            $query->where('status', $this->sellerOrderService->mapOrderStatus($request->status));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                  ->orWhere('seller_order_number', 'like', '%' . $search . '%')
                  ->orWhere('order_id', 'like', '%' . $search . '%')
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('order_number', 'like', '%' . $search . '%')
                          ->orWhere('receiver_name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('order.buyer', function ($bq) use ($search) {
                      $bq->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('items.product', function ($pq) use ($search) {
                      $pq->where('nama_produk', 'like', '%' . $search . '%');
                  });
            });
        }

        $orders = $query->latest()->get();
        return view('SellerView.orders.index_seller', compact('orders'));
    }

    public function show($id)
    {
        $order = $this->sellerOrders()
            ->where(function ($q) use ($id) {
                $q->where('id', $id)
                  ->orWhere('order_id', $id)
                  ->orWhere('seller_order_number', $id);
            })
            ->firstOrFail();

        // Jika pesanan ini adalah rental dan memiliki data rental, redirect ke detail rental
        if ($order->items->contains('type', 'rent')) {
            $rental = \App\Models\SellerModels\Rental_seller::where('order_id', $order->order_id)->first();
            if ($rental) {
                return redirect()->route('seller.rentals.show', $rental->id);
            }
        }

        return view('SellerView.orders.show_seller', compact('order'));
    }

    public function edit($id)
    {
        $order = $this->sellerOrders()
            ->where(function ($q) use ($id) {
                $q->where('id', $id)
                  ->orWhere('order_id', $id)
                  ->orWhere('seller_order_number', $id);
            })
            ->firstOrFail();

        return view('SellerView.orders.edit_seller', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $order = $this->sellerOrders()
            ->where(function ($q) use ($id) {
                $q->where('id', $id)
                  ->orWhere('order_id', $id)
                  ->orWhere('seller_order_number', $id);
            })
            ->firstOrFail();

        // Map English status to Indonesian database values if necessary
        $statusMap = [
            'menunggu' => 'pending',
            'diproses' => 'processing',
            'dikirim' => 'shipped',
            'selesai' => 'delivered',
            'pending' => 'pending',
            'processing' => 'processing',
            'shipped' => 'shipped',
            'delivered' => 'delivered',
            'completed' => 'delivered',
            'cancelled' => 'cancelled',
            'dibatalkan' => 'cancelled',
        ];

        $request->validate([
            'status' => 'required',
            'resi' => 'nullable|string|max:255',
            'video_pengiriman' => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,mov,avi|max:20480',
        ]);

        $status = $statusMap[$request->status] ?? $request->status;
        $resi = $request->resi ?? $request->no_resi;

        // Jika mengubah status menjadi Dikirim, pastikan nomor resi terisi
        if ($status === SellerOrder::STATUS_SHIPPED && empty($resi)) {
            return back()->with('error', 'Nomor resi pengiriman wajib diisi saat mengubah status pesanan menjadi Dikirim.')->withInput();
        }

        $updateData = [
            'status' => $status,
            'no_resi' => $resi,
            'shipped_at' => $status === SellerOrder::STATUS_SHIPPED ? ($order->shipped_at ?? now()) : $order->shipped_at,
            'delivered_at' => $status === SellerOrder::STATUS_DELIVERED ? ($order->delivered_at ?? now()) : $order->delivered_at,
        ];

        // Jika ada file bukti pengiriman (foto atau video) yang diunggah
        if ($request->hasFile('video_pengiriman')) {
            $file = $request->file('video_pengiriman');
            $updateData['video_pengiriman_hash'] = md5_file($file->getRealPath());
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = 'kirim_' . time() . '_' . uniqid() . '.' . $ext;
            $dest = in_array($ext, ['mp4', 'mov', 'avi']) ? 'assets/videos' : 'assets/images';
            $file->move(public_path($dest), $filename);
            $updateData['video_pengiriman'] = $dest . '/' . $filename;
        }

        $prevStatus = $order->status;
        $order->update($updateData);

        // Jika dibatalkan, pulihkan stok produk
        if ($prevStatus !== SellerOrder::STATUS_CANCELLED && $status === SellerOrder::STATUS_CANCELLED) {
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $cur = (int) ($product->stock ?? $product->stok ?? 0);
                    $product->stock = $cur + $item->qty;
                    $product->stok = $product->stock;
                    $product->save();
                }
            }
        }

        app(\App\Services\SellerOrderService::class)->syncPayout($order->fresh(['payout']));
        $this->sellerOrderService->syncParentOrderStatus($order->order);

        return redirect('/seller/orders')->with('success', 'Pesanan berhasil diupdate');
    }

    public function updateStatus(Request $request, $order)
    {
        $request->validate([
            'status' => 'required|in:menunggu,diproses,dikirim,selesai,dibatalkan,pending,processing,shipped,delivered,cancelled',
        ]);

        $order = $this->sellerOrders()->findOrFail($order);
        $prevStatus = $order->status;
        $status = $this->sellerOrderService->mapOrderStatus($request->status);
        $order->update([
            'status' => $status,
            'shipped_at' => $status === SellerOrder::STATUS_SHIPPED ? ($order->shipped_at ?? now()) : $order->shipped_at,
            'delivered_at' => $status === SellerOrder::STATUS_DELIVERED ? ($order->delivered_at ?? now()) : $order->delivered_at,
        ]);

        // Jika dibatalkan, pulihkan stok produk
        if ($prevStatus !== SellerOrder::STATUS_CANCELLED && $status === SellerOrder::STATUS_CANCELLED) {
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $cur = (int) ($product->stock ?? $product->stok ?? 0);
                    $product->stock = $cur + $item->qty;
                    $product->stok = $product->stock;
                    $product->save();
                }
            }
        }
        $this->sellerOrderService->syncPayout($order->fresh(['payout']));
        $this->sellerOrderService->syncParentOrderStatus($order->order);

        return back()->with('success', 'Status pesanan berhasil diupdate');
    }

    public function updateResi(Request $request, $order)
    {
        $request->validate([
            'resi' => 'required|string|max:255',
            'video_pengiriman' => 'required|file|mimes:mp4,mov,avi|max:20480',
        ]);

        $order = $this->sellerOrders()->findOrFail($order);

        $videoPath = null;
        $videoHash = null;
        if ($request->hasFile('video_pengiriman')) {
            $file = $request->file('video_pengiriman');
            $videoHash = md5_file($file->getRealPath());
            $filename = 'kirim_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/videos'), $filename);
            $videoPath = 'assets/videos/' . $filename;
        }

        $order->update([
            'no_resi' => $request->resi,
            'status' => $order->status === SellerOrder::STATUS_DELIVERED ? SellerOrder::STATUS_DELIVERED : SellerOrder::STATUS_SHIPPED,
            'shipped_at' => $order->shipped_at ?? now(),
            'video_pengiriman' => $videoPath,
            'video_pengiriman_hash' => $videoHash,
        ]);
        $this->sellerOrderService->syncPayout($order->fresh(['payout']));
        $this->sellerOrderService->syncParentOrderStatus($order->order);

        return back()->with('success', 'Resi dan video bukti pengiriman berhasil diupdate');
    }

    private function sellerOrders()
    {
        $userId = \Illuminate\Support\Facades\Auth::id();

        return SellerOrder::with(['items.product', 'order.buyer', 'store.user', 'seller', 'payout'])
            ->where(function ($query) use ($userId) {
                $query->where('seller_id', $userId)
                    ->orWhereHas('store', fn ($store) => $store->where('user_id', $userId));
            });
    }
}
