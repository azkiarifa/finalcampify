<?php

namespace App\Http\Controllers\SellerController;

use App\Http\Controllers\Controller;
use App\Models\SellerModels\Rental_seller;
use App\Models\SellerModels\Product_seller;
use Illuminate\Http\Request;

class RentalController_seller extends Controller
{
    public function index(Request $request)
    {
        $query = $this->sellerRentals();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'like', '%' . $search . '%')
                        ->orWhere('nama_produk', 'like', '%' . $search . '%');
                  });
            });
        }

        $rentals = $query->latest()->get();
        return view('SellerView.rentals.index_seller', compact('rentals'));
    }

    public function show($id)
    {
        $rental = $this->sellerRentals()->findOrFail($id);
        return view('SellerView.rentals.show_seller', compact('rental'));
    }

    public function edit($id)
    {
        $rental = $this->sellerRentals()->findOrFail($id);

        // Hitung denda telat otomatis
        $endDate = \Carbon\Carbon::parse($rental->end_date)->startOfDay();
        $today = now()->startOfDay();
        $daysLate = max(0, $endDate->diffInDays($today, false));
        $dendaTelat = $daysLate * 10000;

        return view('SellerView.rentals.edit_seller', compact('rental', 'dendaTelat', 'daysLate'));
    }

    public function update(Request $request, $id)
    {
        $rental = $this->sellerRentals()->findOrFail($id);

        $request->validate([
            'status' => 'required|string',
            'catatan' => 'nullable|string',
            'foto_kondisi' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'status' => $request->status,
            'catatan' => $request->catatan ?? $rental->catatan
        ];

        // Jika mengubah ke aktif dan belum ada foto, wajib upload foto kondisi
        if ($request->status === 'active' && !$rental->condition_photo_handover) {
            $request->validate([
                'foto_kondisi' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);
        }

        if ($request->hasFile('foto_kondisi')) {
            if ($rental->condition_photo_handover) {
                $oldPath = public_path($rental->condition_photo_handover);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file('foto_kondisi');
            $filename = 'handover_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images'), $filename);
            $data['condition_photo_handover'] = 'assets/images/' . $filename;
        }

        $oldStatus = $rental->status;
        $rental->update($data);

        // Jika rental dibatalkan, kembalikan stok produk
        if ($oldStatus !== 'cancelled' && $request->status === 'cancelled') {
            $product = $rental->product;
            if ($product) {
                $currentStock = (int) ($product->stock ?? $product->stok ?? 0);
                $product->stock = $currentStock + 1;
                $product->stok = $product->stock;
                $product->save();
            }
        }

        if ($rental->order) {
            $statusMap = [
                'pending' => 'diproses',
                'active' => 'dikirim',
                'completed' => 'selesai',
                'cancelled' => 'dibatalkan',
            ]; 

            if (array_key_exists($request->status, $statusMap)) {
                $rental->order->update(['status' => $statusMap[$request->status]]);
            }
        }

        return redirect()->route('seller.rentals.show', $rental->id)->with('success', 'Data penyewaan berhasil diperbarui.');
    }

    public function approveRequest($id)
    {
        $rental = $this->sellerRentals()->findOrFail($id);
        $returnRequest = \App\Models\ReturnEscrow::where('order_id', $rental->order_id)->first();

        if ($returnRequest) {
            $returnRequest->update(['status' => 'approved']);
            $rental->update(['status' => 'approved_return']);
            return back()->with('success', 'Permintaan pengembalian sewa disetujui. Menunggu pembeli memasukkan resi pengiriman.');
        }

        return back()->with('error', 'Data pengembalian tidak ditemukan.');
    }

    public function receiveItem(Request $request, $id)
    {
        $rental = $this->sellerRentals()->findOrFail($id);
        $returnRequest = \App\Models\ReturnEscrow::where('order_id', $rental->order_id)->first();

        if (!$returnRequest) {
            return back()->with('error', 'Data pengembalian tidak ditemukan.');
        }

        $request->validate([
            'denda_kerusakan' => 'required|numeric|min:0',
            'kondisi_barang' => 'required|string|max:255',
        ]);

        // Set tanggal penerimaan barang
        $returnRequest->actual_date = now();
        $returnRequest->damage_fee = (string) $request->denda_kerusakan;
        $returnRequest->kondisi_barang = $request->kondisi_barang;
        $returnRequest->status = 'checking'; // sementara untuk auto calculation

        if (class_exists(\App\Services\ReturnSettlementService::class)) {
            $settlement = app(\App\Services\ReturnSettlementService::class);
            $settlement->applyAutoCalculations($returnRequest);
        }

        // Kembalikan stok produk jika barang fisik kembali diterima
        $alreadyReceived = in_array($rental->status, ['denda_pending', 'waiting_refund', 'completed'], true);
        if (!$alreadyReceived) {
            $product = $rental->product;
            if ($product) {
                $currentStock = (int) ($product->stock ?? $product->stok ?? 0);
                $product->stock = $currentStock + 1;
                $product->stok = $product->stock;
                $product->save();
            }
        }

        // Tentukan status akhir berdasarkan nilai defisit
        if ($returnRequest->deficit > 0) {
            $returnRequest->status = 'denda_pending';
            $rental->status = 'denda_pending';
            $returnRequest->save();
            $rental->save();

            return back()->with('success', 'Barang diterima. Denda melebihi deposit jaminan. Menunggu pembeli mentransfer sisa denda sebesar Rp ' . number_format($returnRequest->deficit, 0, ',', '.'));
        } else {
            $returnRequest->status = 'waiting_refund';
            $returnRequest->save();

            $rental->status = 'waiting_refund';
            $rental->save();

            return back()->with('success', 'Barang diterima dalam kondisi baik / tercover deposit. Menunggu Admin mentransfer dana refund.');
        }
    }

    public function verifyDendaPayment($id)
    {
        $rentalId = $id instanceof \App\Models\SellerModels\Rental_seller ? $id->id : $id;
        $rental = $this->sellerRentals()->findOrFail($rentalId);
        
        $returnRequest = \App\Models\ReturnEscrow::where('order_id', $rental->order_id)
            ->orWhere('rental_id', $rental->id)
            ->first();

        if (!$returnRequest) {
            return back()->with('error', 'Data pengembalian tidak ditemukan.');
        }

        $returnRequest->update(['status' => 'completed']);
        $rental->update(['status' => 'completed']);

        if ($rental->order) {
            $rental->order->update(['status' => 'selesai']);
        }

        return back()->with('success', 'Pembayaran sisa denda berhasil diverifikasi. Transaksi sewa selesai.');
    }


    public function reviewComplaint(Request $request, $return_id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'resolution_type' => 'required_if:action,approve|in:refund,replacement',
            'owner_notes' => 'nullable|string|max:500',
        ]);

        $return = \App\Models\Pembeli\Return_pembeli::findOrFail($return_id);

        if ($request->action === 'reject') {
            $return->update([
                'status' => 'rejected',
                'owner_notes' => $request->owner_notes,
            ]);

            $order = \App\Models\Pembeli\Order_pembeli::find($return->order_id);
            if ($order) {
                $order->update(['status' => 'selesai']);
            }

            return back()->with('success', 'Komplain pembeli telah ditolak.');
        }

        $resolution = $request->resolution_type;
        $order = \App\Models\Pembeli\Order_pembeli::with('details.product')->find($return->order_id);
        $orderDetail = $order?->details?->first();
        $product = $orderDetail?->product;
        $stock = $product ? ($product->stok ?? $product->stock ?? 0) : 0;

        // Cek stok barang pengganti
        if ($resolution === 'replacement' && $stock <= 0 && !$request->has('force_replacement')) {
            return back()->with('error', 'Stok barang pengganti saat ini kosong (0). Silakan tawarkan atau pilih resolusi Refund Dana sebagai alternatif.');
        }

        $status = ($resolution === 'refund') ? 'refund_pending' : 'approved';

        $return->update([
            'status' => $status,
            'resolution_type' => $resolution,
            'owner_notes' => $request->owner_notes,
            'stock_replacement_empty' => ($stock <= 0),
            'offer_refund_alternative' => ($resolution === 'refund' && $return->resolution_type === 'replacement'),
            'ongkir_bearer' => 'seller',
        ]);

        $msg = $resolution === 'refund' 
            ? 'Komplain disetujui dengan resolusi Refund Dana (Pengembalian Uang).' 
            : 'Komplain disetujui dengan resolusi Pengiriman Barang Pengganti (Tukar).';

        return back()->with('success', $msg);
    }

    public function confirmReceivedBySeller($return_id)
    {
        $return = \App\Models\Pembeli\Return_pembeli::findOrFail($return_id);

        $return->update([
            'status' => 'item_received',
            'actual_date' => now(),
        ]);

        return back()->with('success', 'Barang retur dari pembeli telah dikonfirmasi diterima di toko. Silakan kemas dan kirimkan unit produk pengganti.');
    }

    public function sendReplacementItem(Request $request, $return_id)
    {
        $request->validate([
            'resi_pengganti' => 'required|string|max:255',
            'kurir_pengganti' => 'nullable|string|max:100',
        ]);

        $return = \App\Models\Pembeli\Return_pembeli::findOrFail($return_id);

        // Kurangi stok produk jika mencukupi
        $order = \App\Models\Pembeli\Order_pembeli::with('details.product')->find($return->order_id);
        $orderDetail = $order?->details?->first();
        if ($orderDetail && $orderDetail->product) {
            $stock = $orderDetail->product->stok ?? $orderDetail->product->stock ?? 0;
            if ($stock > 0) {
                $orderDetail->product->decrement('stok', $orderDetail->qty ?? 1);
            }
        }

        $return->update([
            'status' => 'replacement_shipping',
            'resi_pengganti' => $request->resi_pengganti,
            'kurir_pengganti' => $request->kurir_pengganti ?: 'J&T Express',
            'ongkir_bearer' => 'seller',
        ]);

        if ($order) {
            $currentNumber = $order->order_number ?? $order->id;
            if (!str_ends_with((string)$currentNumber, '-RESEND')) {
                $order->update(['order_number' => $currentNumber . '-RESEND']);
            }
        }

        return back()->with('success', 'Barang pengganti telah dikirim dengan nomor resi ' . $request->resi_pengganti . ' via ' . ($request->kurir_pengganti ?: 'J&T Express'));
    }

    public function confirmRefundTransferred($return_id)
    {
        $return = \App\Models\Pembeli\Return_pembeli::findOrFail($return_id);
        $return->update([
            'status' => 'completed',
            'refund_disbursed_at' => now(),
        ]);

        $order = \App\Models\Pembeli\Order_pembeli::find($return->order_id);
        if ($order) {
            $order->update(['status' => 'selesai']);
        }

        return back()->with('success', 'Refund dana telah berhasil dikonfirmasi. Pengembalian selesai.');
    }

    private function sellerRentals()
    {
        return Rental_seller::with(['product', 'user', 'order'])
            ->whereHas('product', function ($query) {
                $query->where('user_id', \Illuminate\Support\Facades\Auth::id())
                    ->orWhereHas('store', fn ($store) => $store->where('user_id', \Illuminate\Support\Facades\Auth::id()));
            });
    }
}