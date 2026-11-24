@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h2 class="fw-bold m-0 text-dark">Edit Profil Toko</h2>
            <p class="text-muted mb-0">Kelola identitas, alamat, dan informasi rekening toko Anda di sini.</p>
        </div>
        <a href="{{ route('seller.store-profile.index') }}" class="btn btn-light border rounded-pill px-4 py-2 text-secondary fw-semibold">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Profil
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- LEFT: FORM EDIT --}}
    <div class="col-lg-8">
        <form method="POST" action="{{ route('seller.store-profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('POST')
            
            <div class="card card-modern border-0 p-5 mb-4">
                <h5 class="fw-bold mb-4">Informasi Dasar Toko</h5>
                
                {{-- FOTO / LOGO TOKO --}}
                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small text-uppercase ls-1">Foto / Logo Toko</label>
                    <div class="d-flex align-items-center gap-4 p-3 bg-light rounded-4">
                        <div class="position-relative overflow-hidden rounded-circle border border-3 border-emerald shadow-sm flex-shrink-0" style="width: 80px; height: 80px; background: var(--primary-emerald);">
                            @if(!empty($profile?->logo_url))
                                <img id="logo_preview" src="{{ $profile->logo_url }}" class="w-100 h-100 object-fit-cover">
                                <span id="logo_placeholder" class="w-100 h-100 d-none align-items-center justify-content-center text-white fw-bold fs-2">
                                    {{ substr($profile->nama_toko ?? auth()->user()->name, 0, 1) }}
                                </span>
                            @else
                                <span id="logo_placeholder" class="w-100 h-100 d-flex align-items-center justify-content-center text-white fw-bold fs-2">
                                    {{ substr($profile->nama_toko ?? auth()->user()->name, 0, 1) }}
                                </span>
                                <img id="logo_preview" src="" class="w-100 h-100 object-fit-cover d-none">
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <input type="file" name="logo" id="logo_input" class="form-control border-0 bg-white rounded-3 px-3 py-2 shadow-sm" accept="image/*">
                            <small class="text-muted d-block mt-2" style="font-size: 0.78rem;">
                                <i class="bi bi-info-circle me-1"></i>Format: JPG, PNG, WEBP (Maksimal 2MB). Disarankan foto persegi (1:1).
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Nama Toko</label>
                        <input type="text" name="nama_toko" class="form-control border-0 bg-light rounded-3 px-4 py-2" 
                               value="{{ $profile->nama_toko ?? '' }}" placeholder="Nama toko Anda" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">No. Telepon</label>
                        <input type="text" name="no_telp" class="form-control border-0 bg-light rounded-3 px-4 py-2" 
                               value="{{ $profile->no_telp ?? '' }}" placeholder="Contoh: 08123456789">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small text-uppercase ls-1">Alamat Lengkap</label>
                    <input type="text" name="alamat" class="form-control border-0 bg-light rounded-3 px-4 py-2" 
                           value="{{ $profile->alamat ?? '' }}" placeholder="Alamat fisik toko">
                </div>

                <div class="mb-0">
                    <label class="form-label fw-bold text-muted small text-uppercase ls-1">Deskripsi Toko</label>
                    <textarea name="deskripsi" class="form-control border-0 bg-light rounded-3 px-4 py-3" rows="4" 
                              placeholder="Ceritakan tentang toko Anda kepada pelanggan...">{{ $profile->deskripsi ?? '' }}</textarea>
                </div>
            </div>

            <div class="card card-modern border-0 p-5 mb-4">
                <h5 class="fw-bold mb-4">Informasi Rekening Bank</h5>
                <p class="text-muted small mb-4">Informasi ini digunakan untuk pencairan dana hasil penjualan Anda.</p>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Nama Bank</label>
                        <input type="text" name="bank_name" class="form-control border-0 bg-light rounded-3 px-4" 
                               value="{{ $profile->bank_name ?? '' }}" placeholder="BCA, Mandiri, dll">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Nomor Rekening</label>
                        <input type="text" name="bank_account_number" class="form-control border-0 bg-light rounded-3 px-4" 
                               value="{{ $profile->bank_account_number ?? '' }}" placeholder="1234567890">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Atas Nama</label>
                        <input type="text" name="bank_account_name" class="form-control border-0 bg-light rounded-3 px-4" 
                               value="{{ $profile->bank_account_name ?? '' }}" placeholder="Nama Pemilik">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('seller.store-profile.index') }}" class="btn btn-light border px-4 py-3 rounded-4 fw-semibold text-secondary">
                    <i class="bi bi-x-circle me-2"></i>Batal
                </a>
                <button type="submit" class="btn btn-emerald px-5 py-3 rounded-4 shadow-sm fw-bold">
                    <i class="bi bi-save me-2"></i>Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>

    {{-- RIGHT: PREVIEW & STATS --}}
    <div class="col-lg-4">
        {{-- STORE CARD PREVIEW --}}
        <div class="card card-modern border-0 p-5 mb-4 text-center overflow-hidden position-relative" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: white;">
            <div class="position-absolute top-0 end-0 p-4 opacity-10 fs-1">🏪</div>
            
            <div class="profile-avatar mx-auto mb-4 d-flex align-items-center justify-content-center text-white fw-bold rounded-circle border border-4 border-white border-opacity-10 shadow overflow-hidden" style="width: 100px; height: 100px; background: var(--primary-emerald); font-size: 2.5rem;">
                @if(!empty($profile?->logo_url))
                    <img id="side_logo_preview" src="{{ $profile->logo_url }}" class="w-100 h-100 object-fit-cover">
                    <span id="side_logo_initial" class="d-none">{{ substr($profile->nama_toko ?? auth()->user()->name, 0, 1) }}</span>
                @else
                    <img id="side_logo_preview" src="" class="w-100 h-100 object-fit-cover d-none">
                    <span id="side_logo_initial">{{ substr($profile->nama_toko ?? auth()->user()->name, 0, 1) }}</span>
                @endif
            </div>
            
            <h4 class="fw-bold mb-1">{{ $profile->nama_toko ?? 'Toko Saya' }}</h4>
            <p class="text-white-50 small mb-4">{{ auth()->user()->email }}</p>
            
            <div class="d-flex justify-content-center gap-2 mb-4">
                <span class="badge rounded-pill bg-white bg-opacity-10 text-white px-3 py-2">
                    <i class="bi bi-geo-alt me-1"></i> {{ Str::limit($profile->alamat ?? 'Lokasi belum diset', 20) }}
                </span>
            </div>

            @php
                $storeId = \Illuminate\Support\Facades\Auth::id();
                $avgStoreRating = \App\Models\SellerModels\StoreRating_seller::getAverageRating($storeId);
                $storeRatingCount = \App\Models\SellerModels\StoreRating_seller::getRatingCount($storeId);
            @endphp

            <div class="row border-top border-white border-opacity-10 pt-4 mt-2">
                <div class="col-6 border-end border-white border-opacity-10">
                    <h5 class="fw-bold m-0">{{ number_format($avgStoreRating, 1) }}</h5>
                    <small class="text-white-50">Rating Toko</small>
                </div>
                <div class="col-6">
                    <h5 class="fw-bold m-0">{{ $storeRatingCount }}</h5>
                    <small class="text-white-50">Ulasan</small>
                </div>
            </div>
        </div>

        {{-- RECENT REVIEWS --}}
        <div class="card card-modern border-0 p-4">
            <h6 class="fw-bold mb-4">Ulasan Toko Terbaru</h6>
            
            @php
                $storeRatings = \App\Models\SellerModels\StoreRating_seller::where('store_id', $storeId)
                    ->with('user:id,name')
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
            @endphp

            @forelse($storeRatings as $sr)
            <div class="review-item mb-4 pb-3 border-bottom last-border-0">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-bold small">{{ $sr->user->name ?? 'User' }}</span>
                    <small class="text-muted" style="font-size: 0.7rem;">{{ $sr->created_at->diffForHumans() }}</small>
                </div>
                <div class="text-warning small mb-2">
                    @for($i=1; $i<=5; $i++)
                        <i class="bi bi-star{{ $i <= $sr->rating ? '-fill' : '' }}"></i>
                    @endfor
                </div>
                <p class="text-muted small m-0 fst-italic">"{{ Str::limit($sr->ulasan ?? 'Tanpa ulasan teks', 60) }}"</p>
                @if(!empty($sr->reply))
                    <div class="mt-2 p-2 bg-light rounded-3 text-xs border-start border-3 border-emerald">
                        <small class="fw-bold text-emerald d-block"><i class="bi bi-reply-fill"></i> Balasan Anda:</small>
                        <span class="text-dark small">{{ $sr->reply }}</span>
                    </div>
                @endif
            </div>
            @empty
            <div class="text-center py-4">
                <p class="text-muted small mb-0">Belum ada ulasan toko.</p>
            </div>
            @endforelse
            
            <a href="/seller/ratings" class="btn btn-light w-100 rounded-pill py-2 small fw-bold text-muted">Lihat Semua Rating</a>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .last-border-0:last-child { border-bottom: none !important; margin-bottom: 0 !important; padding-bottom: 0 !important; }
    .object-fit-cover { object-fit: cover; }
    .border-emerald { border-color: #10B981 !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const logoInput = document.getElementById('logo_input');
    const logoPreview = document.getElementById('logo_preview');
    const logoPlaceholder = document.getElementById('logo_placeholder');
    const sideLogoPreview = document.getElementById('side_logo_preview');
    const sideLogoInitial = document.getElementById('side_logo_initial');

    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    if (logoPreview) {
                        logoPreview.src = evt.target.result;
                        logoPreview.classList.remove('d-none');
                    }
                    if (logoPlaceholder) {
                        logoPlaceholder.classList.add('d-none');
                        logoPlaceholder.classList.remove('d-flex');
                    }
                    if (sideLogoPreview) {
                        sideLogoPreview.src = evt.target.result;
                        sideLogoPreview.classList.remove('d-none');
                    }
                    if (sideLogoInitial) {
                        sideLogoInitial.classList.add('d-none');
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endsection
