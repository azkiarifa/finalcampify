@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold m-0 text-dark">Ubah Status & Resi</h2>
            <p class="text-muted">Perbarui progres pengiriman pesanan pelanggan Anda.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('seller.orders.index') }}" class="btn btn-light rounded-pill px-4 border shadow-sm">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-modern border-0 shadow-sm p-4" style="border-radius:16px;">
            <div class="mb-4 pb-3 border-bottom">
                <h5 class="fw-bold mb-1 text-dark">{{ $order->buyer_name }}</h5>
                @php
                    $firstItem = $order->items->first();
                    $productName = $firstItem?->product?->name ?? $firstItem?->product?->nama_produk ?? optional($order->product)->nama_produk ?? '-';
                    $itemCount = $order->items->count();
                @endphp
                <p class="text-muted small mb-0">Produk: <strong class="text-emerald">{{ $productName }}{{ $itemCount > 1 ? " (+".($itemCount-1)." produk lainnya)" : "" }}</strong></p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-modern rounded-3 mb-4 p-3 shadow-sm">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <strong class="small">Periksa kembali data yang dimasukkan:</strong>
                    </div>
                    <ul class="mb-0 small ps-4">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/seller/orders/{{ $order->id }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @php
                    $activeStatus = old('status', request('status', $order->status));
                    if ($activeStatus === 'dikirim') $activeStatus = 'shipped';
                    if ($activeStatus === 'diproses') $activeStatus = 'processing';
                    if ($activeStatus === 'menunggu') $activeStatus = 'pending';
                    if ($activeStatus === 'selesai') $activeStatus = 'delivered';
                @endphp

                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small text-uppercase ls-1">Status Pesanan</label>
                    <select name="status" class="form-select border-0 bg-light rounded-3 px-3 py-2 shadow-sm">
                        <option value="pending" {{ $activeStatus == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="processing" {{ $activeStatus == 'processing' ? 'selected' : '' }}>Diproses</option>
                        <option value="shipped" {{ $activeStatus == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                        <option value="delivered" {{ $activeStatus == 'delivered' ? 'selected' : '' }}>Selesai / Delivered</option>
                        <option value="cancelled" {{ $activeStatus == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small text-uppercase ls-1">Nomor Resi Pengiriman <span class="text-danger">*</span></label>
                    <input type="text" name="resi" class="form-control border-0 bg-light rounded-3 px-3 py-2 shadow-sm" 
                           value="{{ old('resi', $order->resi ?? $order->no_resi ?? '') }}" 
                           placeholder="Masukkan nomor resi pengiriman (misal: JNE123456789)" required>
                    <small class="text-muted">Wajib diisi saat status diubah menjadi <strong>Dikirim</strong>.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small text-uppercase ls-1">Foto / Video Bukti Pengiriman (Opsional / Disarankan)</label>
                    <input type="file" name="video_pengiriman" accept="image/*,video/mp4,video/quicktime,video/x-msvideo,video/webm" class="form-control border-0 bg-light rounded-3 px-3 py-2 shadow-sm">
                    <small class="text-muted d-block mt-1">Dapat berupa foto paket dengan label resi atau rekaman video packing (Format: JPG, PNG, MP4. Maks: 20MB).</small>
                    @if($order->video_pengiriman)
                        @php
                            $ext = strtolower(pathinfo($order->video_pengiriman, PATHINFO_EXTENSION));
                            $isVideo = in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'mkv']);
                        @endphp
                        <div class="mt-3 p-3 bg-light rounded-3 border">
                            <small class="text-success fw-bold d-block mb-2"><i class="bi bi-check-circle-fill me-1"></i> Bukti Pengiriman Tersimpan:</small>
                            @if($isVideo)
                                <video controls class="rounded-3 shadow-sm w-100" style="max-height: 240px; background: #000;">
                                    <source src="{{ asset($order->video_pengiriman) }}">
                                    Browser Anda tidak mendukung tag video.
                                </video>
                            @else
                                <a href="{{ asset($order->video_pengiriman) }}" target="_blank">
                                    <img src="{{ asset($order->video_pengiriman) }}" alt="Bukti Pengiriman" class="img-fluid rounded-3 shadow-sm" style="max-height: 200px; object-fit: contain;">
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-emerald text-white rounded-pill px-5 py-2 fw-bold shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-modern border-0 p-4 bg-emerald text-white shadow-sm">
            <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Panduan Update</h6>
            <p class="small mb-2 opacity-75">1. Pastikan barang sudah siap sebelum mengubah ke status <strong>Diproses</strong>.</p>
            <p class="small mb-2 opacity-75">2. Masukkan nomor resi yang valid saat status diubah ke <strong>Dikirim</strong>.</p>
            <p class="small mb-0 opacity-75">3. Gunakan fitur chat untuk memberi tahu pembeli jika ada kendala.</p>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .text-emerald { color: #10B981 !important; }
    .bg-emerald { background-color: #10B981 !important; }
</style>
@endsection
