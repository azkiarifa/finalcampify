@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold m-0 text-dark">Tambah Layanan & Stok Alat Baru</h2>
            <p class="text-muted">Pusat kendali inventaris. Data pengadaan stok, spesifikasi, dan kelengkapan paket yang dimasukkan di sini otomatis menyinkronkan harga ke Katalog Produk.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('seller.services.index') }}" class="btn btn-light rounded-pill px-4 border">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('seller.services.store') }}" method="POST" enctype="multipart/form-data" id="serviceForm">
    @csrf
    <div class="row g-4">
        {{-- LEFT COLUMN: Informasi Layanan & Pengadaan Stok --}}
        <div class="col-lg-8">
            {{-- CARD 1: INFORMASI DASAR --}}
            <div class="card card-modern border-0 p-4 p-md-5 mb-4">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-info-circle text-emerald me-2"></i>Informasi Layanan Alat</h5>
                
                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small text-uppercase ls-1">Nama Layanan / Unit Alat</label>
                    <input type="text" name="name" class="form-control form-control-lg border-0 bg-light rounded-3 px-4" placeholder="Contoh: Paket Tenda Dome Kapasitas 4 Orang Lengkap Matras" value="{{ old('name') }}" required>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Kategori Alat</label>
                        <select name="category_id" class="form-select form-select-lg border-0 bg-light rounded-3 px-4" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted mt-1 d-block">
                            Kategori belum ada? <a href="{{ route('seller.categories.create') }}" target="_blank" class="text-emerald fw-semibold">Buat Kategori Baru</a>
                        </small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Tipe Layanan</label>
                        <select name="service_type" id="service_type" class="form-select form-select-lg border-0 bg-light rounded-3 px-4" required>
                            <option value="sewa" {{ old('service_type', 'sewa') == 'sewa' ? 'selected' : '' }}>🏕️ Sewa Alat</option>
                            <option value="jual" {{ old('service_type') == 'jual' ? 'selected' : '' }}>🛍️ Jual Beli</option>
                        </select>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-bold text-muted small text-uppercase ls-1">Deskripsi & Panduan Pemakaian</label>
                    <textarea name="description" rows="4" class="form-control border-0 bg-light rounded-3 px-4 py-3" placeholder="Jelaskan kondisi alat, spesifikasi ketahanan air, dan aturan sewa/beli...">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- CARD 2: PENCATATAN ASAL-USUL PENGADAAN & STOK FISIK --}}
            <div class="card card-modern border-0 p-4 p-md-5 mb-4 border-start border-4 border-emerald">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0 text-dark">
                        <i class="bi bi-receipt text-emerald me-2"></i>Rincian Pengadaan Stok (Asal Usul Barang)
                    </h5>
                    <span class="badge bg-emerald-subtle text-emerald px-3 py-2 rounded-pill small">
                        Mempengaruhi Dasar Harga
                    </span>
                </div>
                <p class="text-muted small mb-4">
                    Catat kapan dan di mana barang ini dibeli beserta harga modal aslinya. Data ini membantu menghitung harga sewa harian dan jaminan yang wajar.
                </p>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Tanggal Pembelian / Masuk</label>
                        <input type="date" name="purchase_date" id="purchase_date" class="form-control form-control-lg border-0 bg-light rounded-3 px-4" value="{{ old('purchase_date', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Nama Vendor / Toko Distributor</label>
                        <input type="text" name="purchase_store" id="purchase_store" class="form-control form-control-lg border-0 bg-light rounded-3 px-4" placeholder="Contoh: Eiger Official Store, Shopee, Toko Outdoor ABC" value="{{ old('purchase_store') }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">No. Kontak / Telp Vendor (Opsional)</label>
                        <input type="text" name="vendor_phone" class="form-control form-control-lg border-0 bg-light rounded-3 px-4" placeholder="Contoh: 08123456789" value="{{ old('vendor_phone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">No. Nota / Faktur Pembelian (Opsional)</label>
                        <input type="text" name="invoice_number" class="form-control form-control-lg border-0 bg-light rounded-3 px-4" placeholder="Contoh: INV-EIGER-001" value="{{ old('invoice_number') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Alamat / Kota Vendor (Opsional)</label>
                        <input type="text" name="vendor_address" class="form-control form-control-lg border-0 bg-light rounded-3 px-4" placeholder="Contoh: Jl. Cihampelas No. 120, Kota Bandung" value="{{ old('vendor_address') }}">
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Harga Beli Modal per Unit (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light rounded-start-3 px-3 fw-bold">Rp</span>
                            <input type="number" name="purchase_price" id="purchase_price" class="form-control form-control-lg border-0 bg-light rounded-end-3 px-4" placeholder="0" value="{{ old('purchase_price') }}" min="0">
                        </div>
                        <small class="text-muted mt-1 d-block" id="modalHint">Modal asli barang saat pertama kali Anda beli.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Kondisi Barang</label>
                        <select name="condition" class="form-select form-select-lg border-0 bg-light rounded-3 px-4">
                            <option value="baru" {{ old('condition') == 'baru' ? 'selected' : '' }}>Baru (Brand New)</option>
                            <option value="bekas_mulus" {{ old('condition') == 'bekas_mulus' ? 'selected' : '' }}>Bekas Sangat Mulus (90%+)</option>
                            <option value="layak_pakai" {{ old('condition') == 'layak_pakai' ? 'selected' : '' }}>Layak Pakai / Terawat</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Unggah Bukti Struk / Nota Pembelian (Opsional)</label>
                        <input type="file" name="receipt_proof" class="form-control border-0 bg-light rounded-3 px-4 py-2" accept="image/*,application/pdf">
                    </div>
                </div>
            </div>

            {{-- CARD 3: RINCIAN KELENGKAPAN PAKET --}}
            <div class="card card-modern border-0 p-4 p-md-5 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold m-0 text-dark"><i class="bi bi-box-seam text-emerald me-2"></i>Rincian Kelengkapan Paket</h5>
                        <p class="text-muted small m-0">Tambahkan detail aksesori pendukung jika ada (opsional). Setiap biaya tambahan kelengkapan akan otomatis menambah harga katalog produk.</p>
                    </div>
                    <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold" id="btnAddDetail">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Item
                    </button>
                </div>

                <div id="detailsContainer">
                    <div class="detail-row bg-light rounded-4 p-3 mb-3 border">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-5">
                                <label class="form-label small fw-bold text-muted mb-1">Nama Item Kelengkapan</label>
                                <input type="text" name="details[0][item_name]" class="form-control border-0 bg-white rounded-3" placeholder="Contoh: Unit Utama Tenda & Frame">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted mb-1">Biaya Tambahan (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-0 fw-bold">Rp</span>
                                    <input type="number" name="details[0][additional_price]" class="form-control border-0 bg-white rounded-end-3 detail-price" placeholder="0" value="0" min="0">
                                </div>
                            </div>
                            <div class="col-md-3 text-end pt-3 pt-md-4">
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-3 btnRemoveDetail" title="Hapus Item">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="noDetailsAlert" class="text-muted small py-3 px-3 rounded-3 bg-light text-center border border-dashed" style="display: none;">
                    <i class="bi bi-info-circle me-1"></i> Tidak ada rincian kelengkapan paket tambahan. Produk ini akan dijual/disewakan sebagai unit tunggal. Klik tombol <strong>+ Tambah Item</strong> di atas jika ingin menambahkan kelengkapan.
                </div>
            </div>

            {{-- CARD 4: HARGA FINAL & JUMLAH STOK --}}
            <div class="card card-modern border-0 p-4 p-md-5">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-cash-stack text-emerald me-2"></i>Pengaturan Tarif & Stok Tersedia</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between">
                            <label class="form-label fw-bold text-muted small text-uppercase ls-1">Harga Dasar (Base Price Rp)</label>
                            <span class="small text-emerald fw-bold cursor-pointer" id="applySuggestionBtn" style="display: none; cursor: pointer;">Gunakan Rekomendasi</span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light rounded-start-3 px-3 fw-bold">Rp</span>
                            <input type="number" name="base_price" id="base_price" class="form-control form-control-lg border-0 bg-light rounded-end-3 px-4" placeholder="0" value="{{ old('base_price', 0) }}" required min="0">
                        </div>
                        <small class="text-muted mt-1 d-block" id="priceCalculationHint">Tarif dasar harian sewa atau harga jual pokok barang.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Total Stok Unit Masuk</label>
                        <input type="number" name="stock" id="stock" class="form-control form-control-lg border-0 bg-light rounded-3 px-4" placeholder="0" value="{{ old('stock', 1) }}" required min="0">
                        <small class="text-muted mt-1 d-block">Jumlah unit fisik yang siap dipajang di katalog.</small>
                    </div>
                </div>

                {{-- BIDANG KHUSUS SEWA --}}
                <div id="rentalFields" class="row g-3 mt-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Jaminan Sewa (Escrow Deposit Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light rounded-start-3 px-3 fw-bold">Rp</span>
                            <input type="number" name="escrow_deposit" id="escrow_deposit" class="form-control form-control-lg border-0 bg-light rounded-end-3 px-4" placeholder="0" value="{{ old('escrow_deposit', 0) }}" min="0">
                        </div>
                        <small class="text-muted mt-1 d-block">Otomatis dihitung 50% dari tarif sewa (atau disesuaikan manual).</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Minimal Durasi Sewa (Hari)</label>
                        <input type="number" name="min_duration_days" class="form-control form-control-lg border-0 bg-light rounded-3 px-4" value="{{ old('min_duration_days', 1) }}" min="1">
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Foto & Live Calculation Preview --}}
        <div class="col-lg-4">
            {{-- LIVE PRICE PREVIEW BOX --}}
            <div class="card card-modern border-0 p-4 mb-4 text-white shadow-sm" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
                <div class="d-flex align-items-center mb-3">
                    <span class="fs-4 me-2">⚡</span>
                    <h5 class="fw-bold m-0">Kalkulasi Otomatis Produk</h5>
                </div>
                <p class="small opacity-90 mb-3">
                    Harga final di bawah akan otomatis terbit dan tampil pada <strong>Katalog Produk</strong>:
                </p>

                <div class="bg-white bg-opacity-20 p-3 rounded-4 mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small">Modal Beli Unit:</span>
                        <span class="fw-bold" id="previewModal">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small">Harga Dasar (Base):</span>
                        <span class="fw-bold" id="previewBasePrice">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small">Biaya Kelengkapan:</span>
                        <span class="fw-bold" id="previewAdditionalPrice">Rp 0</span>
                    </div>
                    <hr class="my-2 border-white opacity-25">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total Harga Katalog:</span>
                        <h4 class="fw-bold m-0" id="previewTotalPrice">Rp 0</h4>
                    </div>
                </div>

                <div class="bg-black bg-opacity-20 p-3 rounded-3 small mb-3" id="suggestionBox" style="display: none;">
                    <div class="fw-bold text-warning mb-1">💡 Rekomendasi Cerdas:</div>
                    <div id="recRentalRateWrap">Saran Tarif Sewa (7% modal): <strong id="recRentalRate">Rp 0</strong>/hari</div>
                    <div>Saran Jaminan Escrow (50% harga sewa): <strong id="recEscrow">Rp 0</strong></div>
                </div>

                <div class="small opacity-90">
                    <i class="bi bi-shield-lock-fill me-1"></i> Produk yang diterbitkan akan berstatus <strong>Read-Only</strong> dan perubahan selanjutnya wajib dilakukan melalui modul Kelola Layanan ini.
                </div>
            </div>

            {{-- FOTO PRODUK --}}
            <div class="card card-modern border-0 p-4 text-center">
                <h5 class="fw-bold mb-4 text-start text-dark">Foto Alat / Perlengkapan</h5>
                <div class="upload-area bg-light rounded-4 p-4 border-2 border-dashed d-flex flex-column align-items-center justify-content-center" style="min-height: 220px; border-color: #cbd5e1 !important;">
                    <i class="bi bi-cloud-arrow-up fs-1 text-emerald mb-2"></i>
                    <p class="small text-muted mb-3">Pilih foto alat terbaik Anda<br>(Format: JPG, PNG, WEBP - Max 2MB)</p>
                    <input type="file" name="gambar" class="form-control form-control-sm border-0" accept="image/*" required>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-emerald btn-lg w-100 rounded-pill py-3 fw-bold shadow-sm">
                    <i class="bi bi-check2-circle me-2"></i>Terbitkan Layanan & Produk
                </button>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const serviceType = document.getElementById('service_type');
    const rentalFields = document.getElementById('rentalFields');
    const basePriceInput = document.getElementById('base_price');
    const purchasePriceInput = document.getElementById('purchase_price');
    const escrowInput = document.getElementById('escrow_deposit');
    const detailsContainer = document.getElementById('detailsContainer');
    const btnAddDetail = document.getElementById('btnAddDetail');

    const previewModal = document.getElementById('previewModal');
    const previewBase = document.getElementById('previewBasePrice');
    const previewAdd = document.getElementById('previewAdditionalPrice');
    const previewTotal = document.getElementById('previewTotalPrice');

    const suggestionBox = document.getElementById('suggestionBox');
    const recRentalRate = document.getElementById('recRentalRate');
    const recRentalRateWrap = document.getElementById('recRentalRateWrap');
    const recEscrow = document.getElementById('recEscrow');
    const applySuggestionBtn = document.getElementById('applySuggestionBtn');

    let detailIndex = 1;
    let userEditedEscrow = false;

    escrowInput.addEventListener('input', function() {
        userEditedEscrow = true;
    });

    function toggleServiceType() {
        if (serviceType.value === 'sewa') {
            rentalFields.style.display = 'flex';
        } else {
            rentalFields.style.display = 'none';
        }
        recalculateLivePrice();
    }
    serviceType.addEventListener('change', toggleServiceType);
    toggleServiceType();

    function recalculateLivePrice() {
        const modal = parseFloat(purchasePriceInput.value) || 0;
        const base = parseFloat(basePriceInput.value) || 0;
        let additionals = 0;
        document.querySelectorAll('.detail-price').forEach(input => {
            additionals += parseFloat(input.value) || 0;
        });

        const total = base + additionals;
        previewModal.innerText = 'Rp ' + modal.toLocaleString('id-ID');
        previewBase.innerText = 'Rp ' + base.toLocaleString('id-ID');
        previewAdd.innerText = 'Rp ' + additionals.toLocaleString('id-ID');
        previewTotal.innerText = 'Rp ' + total.toLocaleString('id-ID');

        // Rekomendasi Cerdas untuk Sewa
        if (serviceType.value === 'sewa' && (modal > 0 || base > 0)) {
            suggestionBox.style.display = 'block';
            
            const suggestedDaily = modal > 0 ? Math.round(modal * 0.07) : 0; // 7% dari modal beli
            const activeRentalRate = base > 0 ? base : suggestedDaily;
            const suggestedEscrow = Math.round(activeRentalRate * 0.5); // 50% dari harga sewa

            if (suggestedDaily > 0) {
                recRentalRateWrap.style.display = 'block';
                recRentalRate.innerText = 'Rp ' + suggestedDaily.toLocaleString('id-ID');
            } else {
                recRentalRateWrap.style.display = 'none';
            }
            recEscrow.innerText = 'Rp ' + suggestedEscrow.toLocaleString('id-ID');

            // Auto-fill saran escrow jika belum diubah manual oleh seller
            if (!userEditedEscrow && (escrowInput.value === '' || parseFloat(escrowInput.value) === 0)) {
                escrowInput.value = suggestedEscrow;
            }

            if (base === 0 && modal > 0) {
                applySuggestionBtn.style.display = 'inline';
            } else {
                applySuggestionBtn.style.display = 'none';
            }
        } else {
            suggestionBox.style.display = 'none';
            applySuggestionBtn.style.display = 'none';
        }
    }

    applySuggestionBtn.addEventListener('click', function () {
        const modal = parseFloat(purchasePriceInput.value) || 0;
        if (modal > 0) {
            const suggestedDaily = Math.round(modal * 0.07);
            const suggestedEscrow = Math.round(suggestedDaily * 0.5); // 50% dari harga sewa
            basePriceInput.value = suggestedDaily;
            if (!userEditedEscrow) {
                escrowInput.value = suggestedEscrow;
            }
            recalculateLivePrice();
        }
    });

    purchasePriceInput.addEventListener('input', recalculateLivePrice);
    basePriceInput.addEventListener('input', recalculateLivePrice);
    detailsContainer.addEventListener('input', function (e) {
        if (e.target.classList.contains('detail-price')) {
            recalculateLivePrice();
        }
    });

    function checkDetailsEmpty() {
        const rows = detailsContainer.querySelectorAll('.detail-row');
        const alertBox = document.getElementById('noDetailsAlert');
        if (rows.length === 0) {
            if (alertBox) alertBox.style.display = 'block';
        } else {
            if (alertBox) alertBox.style.display = 'none';
        }
    }

    btnAddDetail.addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'detail-row bg-light rounded-4 p-3 mb-3 border';
        row.innerHTML = `
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-muted mb-1">Nama Item Kelengkapan</label>
                    <input type="text" name="details[${detailIndex}][item_name]" class="form-control border-0 bg-white rounded-3" placeholder="Contoh: Pasak Tambahan / Footprint">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Biaya Tambahan (Rp)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-0 fw-bold">Rp</span>
                        <input type="number" name="details[${detailIndex}][additional_price]" class="form-control border-0 bg-white rounded-end-3 detail-price" placeholder="0" value="0" min="0">
                    </div>
                </div>
                <div class="col-md-3 text-end pt-3 pt-md-4">
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-3 btnRemoveDetail" title="Hapus Item">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        detailsContainer.appendChild(row);
        detailIndex++;
        checkDetailsEmpty();
        recalculateLivePrice();
    });

    detailsContainer.addEventListener('click', function (e) {
        const btn = e.target.closest('.btnRemoveDetail');
        if (btn) {
            btn.closest('.detail-row').remove();
            checkDetailsEmpty();
            recalculateLivePrice();
        }
    });

    recalculateLivePrice();
});
</script>
@endsection
