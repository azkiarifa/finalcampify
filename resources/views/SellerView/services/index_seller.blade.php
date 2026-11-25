@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold m-0 text-dark">Kelola Layanan Alat Outdoor</h2>
            <p class="text-muted">Pusat kendali layanan sewa alat & jual beli, detail kelengkapan spesifikasi, dan stok barang.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('seller.services.create') }}" class="btn btn-emerald px-4 py-2 rounded-pill shadow-sm fw-bold">
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

{{-- FILTER & SEARCH BAR --}}
<div class="card card-modern p-3 mb-4 border-0 shadow-sm">
    <div class="row align-items-center g-3">
        <div class="col-lg-6 col-md-12">
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('seller.services.index', array_filter(['search' => request('search')])) }}" 
                   class="btn rounded-pill px-3 py-2 fw-semibold {{ request('type') == null ? 'btn-emerald' : 'btn-light text-muted' }}">
                   Semua Layanan
                </a>
                <a href="{{ route('seller.services.index', array_filter(['type' => 'sewa', 'search' => request('search')])) }}" 
                   class="btn rounded-pill px-3 py-2 fw-semibold {{ request('type') == 'sewa' ? 'btn-emerald' : 'btn-light text-muted' }}">
                   🏕️ Sewa Alat
                </a>
                <a href="{{ route('seller.services.index', array_filter(['type' => 'jual', 'search' => request('search')])) }}" 
                   class="btn rounded-pill px-3 py-2 fw-semibold {{ request('type') == 'jual' ? 'btn-emerald' : 'btn-light text-muted' }}">
                   🛍️ Jual Beli
                </a>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <form action="{{ route('seller.services.index') }}" method="GET" class="d-flex align-items-center justify-content-lg-end gap-2">
                @if(request('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                <div class="input-group" style="max-width: 320px;">
                    <span class="input-group-text bg-light border-0 ps-3 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-0 bg-light py-2" placeholder="Cari layanan, alat, vendor..." value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('seller.services.index', array_filter(['type' => request('type')])) }}" class="input-group-text bg-light border-0 text-muted" title="Hapus pencarian">
                            <i class="bi bi-x-circle-fill"></i>
                        </a>
                    @endif
                </div>
                <button type="submit" class="btn btn-emerald rounded-pill px-3 py-2 fw-bold shadow-sm">
                    Cari
                </button>
            </form>
        </div>
    </div>
</div>

{{-- SERVICES GRID --}}
<div class="row g-4">
    @forelse ($services as $service)
    @php
        $finalPrice = $service->calculateFinalPrice();
        $imagePath = $service->image;
        $imageUrl = null;
        if ($imagePath) {
            if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
                $imageUrl = $imagePath;
            } elseif (str_starts_with($imagePath, 'assets/images/')) {
                $imageUrl = asset($imagePath);
            } elseif (file_exists(public_path('assets/images/' . basename($imagePath)))) {
                $imageUrl = asset('assets/images/' . basename($imagePath));
            } else {
                $imageUrl = asset($imagePath);
            }
        }
        $prodStatus = $service->product?->status ?? $service->sellerProduct?->status ?? 'waiting';
    @endphp
    <div class="col-xl-6 col-lg-6 col-md-12">
        <div class="card card-modern h-100 border-0 overflow-hidden shadow-sm d-flex flex-column hover-lift">
            
            {{-- IMAGE BANNER SECTION --}}
            <div class="position-relative bg-light" style="height: 220px; overflow: hidden;">
                @if($imageUrl)
                    <img src="{{ $imageUrl }}" class="w-100 h-100 object-fit-cover transition-zoom" alt="{{ $service->name }}">
                @else
                    <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                        <span class="fs-1 opacity-50">🏕️</span>
                        <small class="text-muted mt-2 fw-medium">Foto Layanan Belum Diunggah</small>
                    </div>
                @endif

                {{-- TOP OVERLAY BAR: LEFT = TYPE, RIGHT = STATUS --}}
                <div class="position-absolute top-0 start-0 end-0 p-3 d-flex justify-content-between align-items-center">
                    <span class="badge {{ $service->service_type === 'sewa' ? 'bg-primary' : 'bg-success' }} rounded-pill px-3 py-2 shadow-sm fw-bold border border-white border-opacity-25">
                        {{ $service->service_type === 'sewa' ? 'PENYEWAAN ALAT' : 'JUAL BELI' }}
                    </span>

                    <div>
                        @if($prodStatus === 'approved')
                            <span class="badge bg-emerald text-white rounded-pill px-3 py-2 shadow-sm fw-bold">
                                <i class="bi bi-check-circle-fill me-1"></i>Aktif
                            </span>
                        @elseif($prodStatus === 'rejected')
                            <span class="badge bg-danger text-white rounded-pill px-3 py-2 shadow-sm fw-bold">
                                <i class="bi bi-x-circle-fill me-1"></i>Ditolak
                            </span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2 shadow-sm fw-bold border border-white border-opacity-50">
                                <i class="bi bi-hourglass-split me-1"></i>Menunggu Review
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- BODY SECTION --}}
            <div class="card-body p-4 d-flex flex-column flex-grow-1">
                {{-- KATEGORI BADGE --}}
                <div class="mb-2">
                    <span class="badge bg-light text-emerald border rounded-pill px-3 py-1 fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <i class="bi bi-tag-fill me-1"></i>{{ $service->category?->name ?? 'Kategori Umum' }}
                    </span>
                </div>

                {{-- TITLE & DESCRIPTION --}}
                <div class="mb-3">
                    <h5 class="fw-bold text-dark mb-2" title="{{ $service->name }}" style="line-height: 1.4;">
                        {{ $service->name }}
                    </h5>
                    <p class="text-muted small mb-0" style="line-height: 1.5;">
                        {{ Str::limit($service->description, 130) ?: 'Belum ada deskripsi layanan.' }}
                    </p>
                </div>

                {{-- ASAL USUL PENGADAAN BARANG (RIWAYAT MODAL) --}}
                @if($service->latestPurchase)
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1 flex-wrap gap-2">
                            <div class="d-flex align-items-center text-muted small">
                                <i class="bi bi-shop text-emerald fs-6 me-2"></i>
                                <span>Vendor: <strong class="text-dark">{{ $service->latestPurchase->purchase_store }}</strong></span>
                            </div>
                            <span class="badge bg-white text-dark border px-2 py-1 fw-bold">
                                Modal: Rp {{ number_format($service->latestPurchase->purchase_price, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mt-1">
                            <small class="text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-calendar-event me-1"></i>Pasokan Terakhir: {{ \Carbon\Carbon::parse($service->latestPurchase->purchase_date)->format('d M Y') }}
                            </small>
                            <a href="{{ route('seller.services.purchases', $service->id) }}" class="text-emerald small fw-bold text-decoration-none">
                                Riwayat Vendor ({{ $service->stockPurchases->count() }}) <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="p-2.5 bg-light rounded-3 border mb-3 d-flex justify-content-between align-items-center">
                        <small class="text-muted"><i class="bi bi-shop me-1"></i>Belum ada data pasokan vendor.</small>
                        <a href="{{ route('seller.services.purchases', $service->id) }}" class="text-emerald small fw-bold text-decoration-none">
                            + Pasok Vendor
                        </a>
                    </div>
                @endif

                {{-- KELENGKAPAN PAKET SPESIFIKASI --}}
                @if($service->details->count() > 0)
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <small class="text-muted fw-bold text-uppercase ls-1" style="font-size: 0.7rem;">
                                <i class="bi bi-boxes text-emerald me-1"></i>Kelengkapan Paket ({{ $service->details->count() }} item)
                            </small>
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($service->details as $detail)
                                <span class="badge bg-light text-dark border px-2 py-1 small fw-normal">
                                    {{ $detail->item_name }}
                                    @if($detail->additional_price > 0)
                                        <strong class="text-emerald">(+Rp {{ number_format($detail->additional_price, 0, ',', '.') }})</strong>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- BOX HARGA TERKALKULASI & STOK --}}
                <div class="p-3 rounded-4 mb-3 mt-auto" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block fw-bold text-uppercase ls-1" style="font-size: 0.68rem;">
                                Harga Terkalkulasi
                            </small>
                            <div class="d-flex align-items-baseline gap-1 mt-1">
                                <span class="h4 fw-bold m-0" style="color: #059669;">
                                    Rp {{ number_format($finalPrice, 0, ',', '.') }}
                                </span>
                                @if($service->service_type === 'sewa')
                                    <span class="text-muted small fw-medium">/hari</span>
                                @endif
                            </div>
                            @if($finalPrice > $service->base_price)
                                <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">
                                    Base: Rp {{ number_format($service->base_price, 0, ',', '.') }} + Item: Rp {{ number_format($finalPrice - $service->base_price, 0, ',', '.') }}
                                </small>
                            @else
                                <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">
                                    Harga Dasar Layanan
                                </small>
                            @endif
                        </div>

                        <div class="text-end ps-3 border-start">
                            <small class="text-muted d-block fw-bold text-uppercase ls-1 mb-1" style="font-size: 0.68rem;">
                                Stok
                            </small>
                            <span class="badge {{ $service->stock > 0 ? 'bg-dark' : 'bg-danger' }} rounded-pill px-3 py-2 fw-bold fs-6 shadow-sm">
                                {{ $service->stock }} Unit
                            </span>
                        </div>
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="d-flex gap-2 pt-2 border-top">
                    <a href="{{ route('seller.services.purchases', $service->id) }}" class="btn btn-outline-emerald flex-grow-1 rounded-3 py-2 fw-bold d-flex align-items-center justify-content-center shadow-sm" title="Riwayat pengadaan dari vendor & pasok unit baru">
                        <i class="bi bi-box-arrow-in-down me-1.5"></i>Pasok & Riwayat Vendor
                    </a>
                    <a href="{{ route('seller.services.edit', $service->id) }}" class="btn btn-light border rounded-3 px-3 py-2 fw-bold text-dark d-flex align-items-center justify-content-center shadow-sm" title="Kelola Stok & Detail">
                        <i class="bi bi-sliders"></i>
                    </a>
                    <form action="{{ route('seller.services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Hapus layanan ini beserta produk katalognya?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger rounded-3 px-3 py-2 fw-bold" title="Hapus Layanan">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card card-modern p-5 text-center border-0 bg-white shadow-sm">
            @if(request('search'))
                <div class="mb-4 fs-1 opacity-25">🔍</div>
                <h4 class="fw-bold">Layanan Tidak Ditemukan</h4>
                <p class="text-muted">Tidak ada layanan yang cocok dengan kata kunci pencarian "<strong>{{ request('search') }}</strong>".</p>
                <div class="mt-4">
                    <a href="{{ route('seller.services.index', array_filter(['type' => request('type')])) }}" class="btn btn-light rounded-pill px-4 border shadow-sm fw-bold">
                        <i class="bi bi-arrow-repeat me-1"></i>Reset Pencarian
                    </a>
                </div>
            @else
                <div class="mb-4 fs-1 opacity-25">🛠️</div>
                <h4 class="fw-bold">Belum Ada Layanan Terdaftar</h4>
                <p class="text-muted">Mulailah dengan menambahkan layanan sewa atau jual beli baru untuk produk outdoor Anda.</p>
                <div class="mt-4">
                    <a href="{{ route('seller.services.create') }}" class="btn btn-emerald px-5 py-3 rounded-pill shadow-sm fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Layanan Sekarang
                    </a>
                </div>
            @endif
        </div>
    </div>
    @endforelse
</div>

<style>
    .ls-1 { letter-spacing: 0.5px; }
    .text-emerald { color: #10B981 !important; }
    .bg-emerald { background-color: #10B981 !important; }
    .bg-emerald-soft { background-color: #ecfdf5 !important; }
    .btn-emerald { background-color: #10B981; color: white; border: none; transition: all 0.2s ease; }
    .btn-emerald:hover { background-color: #059669; color: white; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25); }
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .hover-lift {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08) !important;
    }
    .transition-zoom {
        transition: transform 0.4s ease;
    }
    .hover-lift:hover .transition-zoom {
        transform: scale(1.04);
    }
</style>
@endsection
