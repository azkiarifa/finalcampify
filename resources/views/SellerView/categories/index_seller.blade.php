@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold m-0 text-dark">Kelola Kategori Alat Outdoor</h2>
            <p class="text-muted">Kelola pengelompokan jenis alat outdoor (Tenda, Carrier, Alat Masak, dll) untuk toko Anda.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('seller.categories.create') }}" class="btn btn-emerald px-4 shadow-sm">
                <i class="bi bi-plus-circle me-2"></i>Tambah Kategori Baru
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

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card card-modern border-0 p-4 mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">No</th>
                    <th>Nama Kategori</th>
                    <th>Slug URL</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Layanan</th>
                    <th>Jumlah Produk</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $index => $category)
                <tr>
                    <td class="ps-4 fw-bold text-muted">{{ $index + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-icon bg-emerald-subtle text-emerald rounded-3 p-2 me-3">
                                <i class="bi {{ $category->icon ?? 'bi-tag' }} fs-5"></i>
                            </div>
                            <div>
                                <span class="fw-bold text-dark d-block">{{ $category->name }}</span>
                            </div>
                        </div>
                    </td>
                    <td><code>{{ $category->slug }}</code></td>
                    <td><small class="text-muted">{{ Str::limit($category->description, 60) ?: '-' }}</small></td>
                    <td>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                            {{ $category->services_count ?? 0 }} Layanan
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                            {{ $category->products_count ?? 0 }} Produk
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-inline-flex gap-2">
                            <a href="{{ route('seller.categories.edit', $category->id) }}" class="btn btn-sm btn-light border rounded-3 text-muted">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <form action="{{ route('seller.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" {{ ($category->services_count ?? 0) > 0 ? 'disabled title=Masih_memiliki_layanan' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-folder2-open fs-1 d-block mb-3 opacity-50"></i>
                        Belum ada kategori yang dibuat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
