@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold m-0 text-dark">Katalog Produk</h2>
            <p class="text-muted">Etalase inventaris barang jual dan sewa Anda yang tampil ke pembeli.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('seller.services.create') }}" class="btn btn-emerald px-4 shadow-sm">
                <i class="bi bi-plus-circle me-2"></i>Tambah di Kelola Layanan
            </a>
        </div>
    </div>
</div>

{{-- ALERT INFORMASI READ-ONLY --}}
<div class="alert alert-light border border-2 border-emerald-subtle rounded-4 p-4 mb-4 shadow-sm bg-white">
    <div class="d-flex align-items-center">
        <div class="me-3 fs-2 text-emerald">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <div class="flex-grow-1">
            <h6 class="fw-bold text-dark mb-1">Katalog Produk Bersifat Read-Only (Terkunci dari Edit Langsung)</h6>
            <p class="text-muted small mb-0">
                Untuk menjaga integritas data riwayat transaksi dan status penyewaan yang sedang berjalan, data pada halaman ini tidak dapat diedit secara langsung. Segala penyesuaian stok, kelengkapan paket, dan kalkulasi ulang harga dilakukan secara terpusat melalui modul <a href="{{ route('seller.services.index') }}" class="text-emerald fw-bold text-decoration-underline">Kelola Layanan</a>.
            </p>
        </div>
        <div class="ms-3 d-none d-md-block">
            <a href="{{ route('seller.services.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold">
                Buka Kelola Layanan <i class="bi bi-arrow-right ms-1"></i>
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
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('seller.products.index') }}" 
           class="btn rounded-pill px-4 py-2 fw-semibold {{ request('jenis') == null ? 'btn-emerald' : 'btn-light text-muted' }}">
           Semua Produk
        </a>
        <a href="{{ route('seller.products.index', ['jenis' => 'sewa']) }}" 
           class="btn rounded-pill px-4 py-2 fw-semibold {{ request('jenis') == 'sewa' ? 'btn-emerald' : 'btn-light text-muted' }}">
           🏕️ Penyewaan Alat
        </a>
        <a href="{{ route('seller.products.index', ['jenis' => 'jual']) }}" 
           class="btn rounded-pill px-4 py-2 fw-semibold {{ request('jenis') == 'jual' ? 'btn-emerald' : 'btn-light text-muted' }}">
           🛍️ Penjualan Barang
        </a>
    </div>
</div>

{{-- PRODUK GRID --}}
<div class="row g-4">
    @forelse ($products as $product)
    <div class="col-xl-4 col-md-6">
        <div class="card card-modern h-100 border-0 overflow-hidden">
            
            {{-- IMAGE SECTION --}}
            <div class="position-relative" style="height: 240px;">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" class="w-100 h-100 object-fit-cover" alt="{{ $product->nama_produk }}">
                @else
                    <div class="w-100 h-100 bg-light d-flex flex-column align-items-center justify-content-center">
                        <span class="fs-1 opacity-25">🏕️</span>
                        <small class="text-muted mt-2">No Image Preview</small>
                    </div>
                @endif

                {{-- BADGES OVERLAY --}}
                <div class="position-absolute top-0 start-0 p-3 d-flex flex-column gap-2">
                    @if($product->jenis_produk == 'sewa')
                        <span class="badge bg-primary rounded-pill px-3 py-2 shadow-sm border border-white border-2">SEWA ALAT</span>
                    @else
                        <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm border border-white border-2">JUAL BARANG</span>
                    @endif
                </div>

                <div class="position-absolute bottom-0 end-0 p-3">
                    @if($product->status === 'approved')
                        <span class="badge bg-emerald text-white rounded-pill px-3 py-2 shadow-sm" style="background: var(--primary-emerald);">Aktif</span>
                    @elseif($product->status === 'rejected')
                        <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm">Ditolak</span>
                    @else
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2 shadow-sm">Menunggu Review</span>
                    @endif
                </div>
            </div>

            <div class="card-body p-4 d-flex flex-column">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-emerald fw-bold text-uppercase ls-1" style="font-size: 0.75rem;">
                            {{ ucfirst(str_replace('_',' ',$product->kategori ?? 'Umum')) }}
                        </small>
                        <small class="badge bg-secondary-subtle text-secondary border rounded-pill">
                            <i class="bi bi-lock-fill"></i> Read-Only
                        </small>
                    </div>
                    <h5 class="fw-bold mt-1 mb-2">{{ $product->nama_produk }}</h5>
                    <p class="text-muted small mb-0">{{ Str::limit($product->deskripsi, 80) }}</p>
                </div>

                <div class="mt-auto pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Harga Terkalkulasi</small>
                            <h4 class="fw-bold text-emerald m-0">
                                Rp {{ number_format($product->harga, 0, ',', '.') }}
                                @if($product->jenis_produk == 'sewa')
                                    <span class="fs-6 text-muted fw-normal">/hari</span>
                                @endif
                            </h4>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Stok Terkini</small>
                            <span class="fw-bold {{ $product->stok > 0 ? 'text-dark' : 'text-danger' }}">
                                {{ $product->stok ?? 0 }} Unit
                            </span>
                        </div>
                    </div>

                    {{-- AKSI TERKUNCI & ARAHAN KE KELOLA LAYANAN --}}
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light rounded-3 text-muted border fw-semibold disabled" title="Edit terkunci demi integritas data riwayat transaksi. Perbarui melalui Kelola Layanan.">
                            <i class="bi bi-lock-fill text-secondary me-1"></i>Terkunci
                        </button>
                        
                        @if($product->service_id)
                            <a href="{{ route('seller.services.edit', $product->service_id) }}" class="btn btn-emerald rounded-3 flex-grow-1 fw-bold">
                                <i class="bi bi-sliders me-1"></i>Kelola Layanan
                            </a>
                        @else
                            <a href="{{ route('seller.services.index') }}" class="btn btn-outline-success rounded-3 flex-grow-1 fw-bold">
                                <i class="bi bi-sliders me-1"></i>Kelola Layanan
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card card-modern p-5 text-center border-0 bg-white">
            <div class="mb-4 fs-1 opacity-25">📦</div>
            <h4 class="fw-bold">Belum Ada Produk Terdaftar</h4>
            <p class="text-muted">Katalog produk otomatis terisi saat Anda menambahkan layanan baru di modul Kelola Layanan.</p>
            <div class="mt-4">
                <a href="{{ route('seller.services.create') }}" class="btn btn-emerald px-5 py-3 rounded-pill">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Layanan Sekarang
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .text-emerald { color: var(--primary-emerald); }
    .object-fit-cover { object-fit: cover; }
</style>
@endsection
