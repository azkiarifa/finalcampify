@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold m-0 text-dark">Kelola Layanan Alat Outdoor</h2>
            <p class="text-muted">Pusat kendali skema layanan alat (Jual Beli & Penyewaan), detail kelengkapan spesifikasi, dan stok barang.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('seller.services.create') }}" class="btn btn-emerald px-4 shadow-sm">
                <i class="bi bi-plus-circle me-2"></i>Tambah Layanan Baru
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- FILTER BUTTONS --}}
<div class="card card-modern p-3 mb-4 border-0">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('seller.services.index') }}" 
               class="btn rounded-pill px-4 py-2 fw-semibold {{ request('type') == null ? 'btn-emerald' : 'btn-light text-muted' }}">
               Semua Layanan
            </a>
            <a href="{{ route('seller.services.index', ['type' => 'sewa']) }}" 
               class="btn rounded-pill px-4 py-2 fw-semibold {{ request('type') == 'sewa' ? 'btn-emerald' : 'btn-light text-muted' }}">
               🏕️ Skema Penyewaan
            </a>
            <a href="{{ route('seller.services.index', ['type' => 'jual']) }}" 
               class="btn rounded-pill px-4 py-2 fw-semibold {{ request('type') == 'jual' ? 'btn-emerald' : 'btn-light text-muted' }}">
               🛍️ Skema Jual Beli
            </a>
        </div>
        <div>
            <span class="badge bg-emerald-subtle text-emerald px-3 py-2 rounded-pill">
                <i class="bi bi-arrow-repeat me-1"></i> Terhubung langsung ke Katalog Produk
            </span>
        </div>
    </div>
</div>

{{-- SERVICES LIST --}}
<div class="row g-4">
    @forelse ($services as $service)
    @php
        $finalPrice = $service->calculateFinalPrice();
    @endphp
    <div class="col-xl-6 col-md-12">
        <div class="card card-modern h-100 border-0 p-4">
            <div class="d-flex flex-column flex-sm-row gap-4">
                {{-- GAMBAR LAYANAN --}}
                <div class="flex-shrink-0" style="width: 140px; height: 140px;">
                    @if($service->image)
                        <img src="{{ asset('assets/images/' . $service->image) }}" class="w-100 h-100 object-fit-cover rounded-4 border" alt="{{ $service->name }}">
                    @else
                        <div class="w-100 h-100 bg-light rounded-4 d-flex flex-column align-items-center justify-content-center text-muted border">
                            <span class="fs-1">🏕️</span>
                        </div>
                    @endif
                </div>

                {{-- INFO UTAMA --}}
                <div class="flex-grow-1 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge {{ $service->service_type === 'sewa' ? 'bg-primary' : 'bg-success' }} rounded-pill px-3 py-1 mb-1">
                                {{ $service->service_type === 'sewa' ? 'PENYEWAAN ALAT' : 'JUAL BELI' }}
                            </span>
                            <span class="badge bg-light text-muted border rounded-pill px-3 py-1 ms-1">
                                {{ $service->category?->name ?? 'Kategori Umum' }}
                            </span>
                            <h5 class="fw-bold mt-2 mb-1 text-dark">{{ $service->name }}</h5>
                        </div>
                        <span class="badge bg-emerald text-white rounded-pill px-3 py-1">Aktif</span>
                    </div>

                    <p class="text-muted small mb-2">{{ Str::limit($service->description, 90) ?: 'Tidak ada deskripsi.' }}</p>

                    {{-- ASAL USUL PENGADAAN BARANG --}}
                    @if($service->latestPurchase)
                        <div class="mb-2">
                            <span class="badge bg-light text-secondary border px-2 py-1 small fw-normal">
                                <i class="bi bi-bag-check me-1 text-emerald"></i>Beli: <strong>{{ $service->latestPurchase->purchase_store }}</strong> 
                                ({{ \Carbon\Carbon::parse($service->latestPurchase->purchase_date)->format('d M Y') }}) • Modal: Rp {{ number_format($service->latestPurchase->purchase_price, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif

                    {{-- DETAIL KELENGKAPAN --}}
                    @if($service->details->count() > 0)
                        <div class="bg-light p-2 rounded-3 mb-3">
                            <small class="fw-bold text-dark d-block mb-1">Kelengkapan Paket ({{ $service->details->count() }} item):</small>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($service->details as $detail)
                                    <span class="badge bg-white text-dark border small">
                                        {{ $detail->item_name }}
                                        @if($detail->additional_price > 0)
                                            <span class="text-emerald">(+Rp {{ number_format($detail->additional_price, 0, ',', '.') }})</span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <small class="text-muted d-block">Harga Terkalkulasi (Tampil di Produk)</small>
                            <h4 class="fw-bold text-emerald m-0">
                                Rp {{ number_format($finalPrice, 0, ',', '.') }}
                                @if($service->service_type === 'sewa')
                                    <span class="fs-6 text-muted fw-normal">/hari</span>
                                @endif
                            </h4>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                Base: Rp {{ number_format($service->base_price, 0, ',', '.') }}
                                @if($finalPrice > $service->base_price)
                                    + Biaya Kelengkapan: Rp {{ number_format($finalPrice - $service->base_price, 0, ',', '.') }}
                                @endif
                            </small>
                        </div>

                        <div class="text-end">
                            <small class="text-muted d-block">Ketersediaan Stok</small>
                            <span class="badge {{ $service->stock > 0 ? 'bg-dark' : 'bg-danger' }} fs-6 px-3 py-2 rounded-pill">
                                {{ $service->stock }} Unit
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('seller.services.edit', $service->id) }}" class="btn btn-emerald flex-grow-1 rounded-3 fw-bold">
                    <i class="bi bi-sliders me-2"></i>Kelola Stok & Detail
                </a>
                <form action="{{ route('seller.services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Hapus layanan ini beserta produk katalognya?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger rounded-3 px-3 fw-bold">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card card-modern p-5 text-center border-0 bg-white">
            <div class="mb-4 fs-1 opacity-25">🛠️</div>
            <h4 class="fw-bold">Belum Ada Layanan Terdaftar</h4>
            <p class="text-muted">Mulailah dengan menambahkan layanan sewa atau jual beli baru untuk produk outdoor Anda.</p>
            <div class="mt-4">
                <a href="{{ route('seller.services.create') }}" class="btn btn-emerald px-5 py-3 rounded-pill">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Layanan Sekarang
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
