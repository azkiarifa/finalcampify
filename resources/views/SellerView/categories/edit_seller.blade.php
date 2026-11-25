@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold m-0 text-dark">Edit Kategori Alat Outdoor</h2>
            <p class="text-muted">Perbarui data pengelompokan jenis alat outdoor.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('seller.categories.index') }}" class="btn btn-light rounded-pill px-4 border">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-modern border-0 p-5">
            <form action="{{ route('seller.categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small text-uppercase ls-1">Nama Kategori</label>
                    <input type="text" name="name" class="form-control form-control-lg border-0 bg-light rounded-3 px-4 @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small text-uppercase ls-1">Deskripsi Kategori</label>
                    <textarea name="description" rows="4" class="form-control border-0 bg-light rounded-3 px-4 py-3">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-3 mt-5">
                    <a href="{{ route('seller.categories.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-emerald rounded-pill px-5">Perbarui Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
