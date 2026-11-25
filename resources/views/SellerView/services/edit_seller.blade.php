@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold m-0 text-dark">Kelola Layanan, Stok & Spesifikasi</h2>
            <p class="text-muted">Perubahan stok (restock unit), harga dasar, atau kelengkapan paket di sini akan secara otomatis mengkalkulasi ulang data pada Katalog Produk.</p>
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

<form action="{{ route('seller.services.update', $service->id) }}" method="POST" enctype="multipart/form-data" id="serviceEditForm">
    @csrf
    @method('PUT')

    <div class="row g-4">
        {{-- LEFT COLUMN: Informasi Layanan & Detail --}}
        <div class="col-lg-8">
            {{-- CARD 1: INFORMASI DASAR --}}
            <div class="card card-modern border-0 p-4 p-md-5 mb-4">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-info-circle text-emerald me-2"></i>Informasi Layanan</h5>
                
                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small text-uppercase ls-1">Nama Layanan / Unit Alat</label>
                    <input type="text" name="name" class="form-control form-control-lg border-0 bg-light rounded-3 px-4" value="{{ old('name', $service->name) }}" required>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Kategori Alat</label>
                        <select name="category_id" class="form-select form-select-lg border-0 bg-light rounded-3 px-4" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $service->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Tipe Layanan</label>
                        <select name="service_type" id="service_type" class="form-select form-select-lg border-0 bg-light rounded-3 px-4" required>
                            <option value="sewa" {{ old('service_type', $service->service_type) == 'sewa' ? 'selected' : '' }}>🏕️ Sewa Alat</option>
                            <option value="jual" {{ old('service_type', $service->service_type) == 'jual' ? 'selected' : '' }}>🛍️ Jual Beli</option>
                        </select>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-bold text-muted small text-uppercase ls-1">Deskripsi & Aturan Layanan</label>
                    <textarea name="description" rows="4" class="form-control border-0 bg-light rounded-3 px-4 py-3">{{ old('description', $service->description) }}</textarea>
                </div>
            </div>

            {{-- CARD 2: RIWAYAT PENGADAAN & PENAMBAHAN STOK UNIT (RESTOCK) --}}
            <div class="card card-modern border-0 p-4 p-md-5 mb-4 border-start border-4 border-emerald">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold m-0 text-dark">
                            <i class="bi bi-clock-history text-emerald me-2"></i>Riwayat Pengadaan & Tambah Stok (Restock)
                        </h5>
                        <p class="text-muted small m-0 mt-1">
                            Data pembelian dari vendor menambah stok alat secara otomatis.
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-emerald text-white rounded-pill px-3 py-1.5 fw-bold">
                            Stok Saat Ini: {{ $service->stock }} Unit
                        </span>
                        <a href="{{ route('seller.services.purchases', $service->id) }}" class="btn btn-sm btn-outline-emerald rounded-pill px-3 fw-bold">
                            <i class="bi bi-journal-text me-1"></i>Buku Pasokan Vendor &rarr;
                        </a>
                    </div>
                </div>

                {{-- TABEL RIWAYAT PENGADAAN LALU --}}
                @if($service->stockPurchases && $service->stockPurchases->count() > 0)
                    <div class="table-responsive mb-4 bg-light p-3 rounded-4 border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="fw-bold text-dark d-block">Riwayat Pasokan Vendor Sebelumnya:</small>
                            <a href="{{ route('seller.services.purchases', $service->id) }}" class="small text-emerald fw-bold text-decoration-none">
                                Lihat Semua ({{ $service->stockPurchases->count() }})
                            </a>
                        </div>
                        <table class="table table-sm table-borderless align-middle mb-0 small">
                            <thead>
                                <tr class="text-muted">
                                    <th>No. Faktur</th>
                                    <th>Tanggal Beli</th>
                                    <th>Vendor / Toko Asal</th>
                                    <th>Harga Modal</th>
                                    <th>Jumlah Masuk</th>
                                    <th>Kondisi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($service->stockPurchases->take(4) as $purchase)
                                <tr>
                                    <td><code>{{ $purchase->invoice_number ?: ('PO-' . $purchase->id) }}</code></td>
                                    <td><strong>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</strong></td>
                                    <td>{{ $purchase->purchase_store }}</td>
                                    <td>Rp {{ number_format($purchase->purchase_price, 0, ',', '.') }}</td>
                                    <td><span class="badge bg-white text-success border border-success-subtle fw-bold">+{{ $purchase->quantity }} Unit</span></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary">{{ ucfirst(str_replace('_',' ',$purchase->condition)) }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- FORM RESTOCK BATCH BARU --}}
                <div class="bg-emerald-subtle bg-opacity-25 p-3 rounded-4 border border-emerald">
                    <h6 class="fw-bold text-emerald mb-2"><i class="bi bi-plus-circle me-1"></i>Catat Penambahan Unit Baru (Restock dari Vendor):</h6>
                    <small class="text-muted d-block mb-3">Isi formulir ini jika Anda baru saja membeli unit tambahan dari toko/distributor/vendor.</small>
                    
                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1">Tanggal Masuk Beli</label>
                            <input type="date" name="restock_purchase_date" class="form-control form-control-sm border-0 bg-white" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-muted mb-1">Nama Vendor / Toko Distributor</label>
                            <input type="text" name="restock_purchase_store" class="form-control form-control-sm border-0 bg-white" placeholder="Contoh: Shopee Official / Eiger Store">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted mb-1">Kondisi</label>
                            <select name="restock_condition" class="form-select form-select-sm border-0 bg-white">
                                <option value="baru">Baru</option>
                                <option value="bekas_mulus">Bekas Mulus</option>
                                <option value="rekondisi">Rekondisi</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1">No. Kontak / Telp Vendor (Opsional)</label>
                            <input type="text" name="restock_vendor_phone" class="form-control form-control-sm border-0 bg-white" placeholder="Contoh: 08123456789">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1">No. Nota / Faktur (Opsional)</label>
                            <input type="text" name="restock_invoice_number" class="form-control form-control-sm border-0 bg-white" placeholder="Contoh: INV-EIGER-002">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1">Alamat Toko Vendor (Opsional)</label>
                            <input type="text" name="restock_vendor_address" class="form-control form-control-sm border-0 bg-white" placeholder="Kota / Alamat Toko">
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">Harga Beli Modal per Unit (Rp)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-0 fw-bold">Rp</span>
                                <input type="number" name="restock_purchase_price" id="restock_purchase_price" class="form-control border-0 bg-white" placeholder="0" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted mb-1">Jumlah Unit yang Dibeli (+Unit)</label>
                            <input type="number" name="restock_quantity" id="restock_quantity" class="form-control form-control-sm border-0 bg-white fw-bold text-emerald" placeholder="0 (Kosongkan jika tidak ada restock)" min="0">
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted mb-1">Unggah Foto Bukti Struk / Faktur Nota (Opsional)</label>
                            <input type="file" name="restock_receipt_proof" class="form-control form-control-sm border-0 bg-white" accept="image/*,application/pdf">
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 3: RINCIAN KELENGKAPAN PAKET --}}
            <div class="card card-modern border-0 p-4 p-md-5 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold m-0 text-dark"><i class="bi bi-box-seam text-emerald me-2"></i>Rincian Kelengkapan Paket</h5>
                        <p class="text-muted small m-0">Menambah atau mengubah biaya kelengkapan otomatis menghitung ulang harga jual/sewa katalog.</p>
                    </div>
                    <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold" id="btnAddDetail">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Item
                    </button>
                </div>

                <div id="detailsContainer">
                    @forelse($service->details as $idx => $detail)
                        <div class="detail-row bg-light rounded-4 p-3 mb-3 border">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold text-muted mb-1">Nama Item Kelengkapan</label>
                                    <input type="text" name="details[{{ $idx }}][item_name]" class="form-control border-0 bg-white rounded-3" value="{{ $detail->item_name }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted mb-1">Biaya Tambahan (Rp)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-0 fw-bold">Rp</span>
                                        <input type="number" name="details[{{ $idx }}][additional_price]" class="form-control border-0 bg-white rounded-end-3 detail-price" value="{{ $detail->additional_price }}" min="0">
                                    </div>
                                </div>
                                <div class="col-md-3 text-end pt-3 pt-md-4">
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-3 btnRemoveDetail">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="detail-row bg-light rounded-4 p-3 mb-3 border">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold text-muted mb-1">Nama Item Kelengkapan</label>
                                    <input type="text" name="details[0][item_name]" class="form-control border-0 bg-white rounded-3" placeholder="Contoh: Unit Utama & Pasak Cadangan" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted mb-1">Biaya Tambahan (Rp)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-0 fw-bold">Rp</span>
                                        <input type="number" name="details[0][additional_price]" class="form-control border-0 bg-white rounded-end-3 detail-price" value="0" min="0">
                                    </div>
                                </div>
                                <div class="col-md-3 text-end pt-3 pt-md-4">
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-3 btnRemoveDetail" disabled>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- CARD 4: HARGA DASAR & STOK TOTAL --}}
            <div class="card card-modern border-0 p-4 p-md-5">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-currency-dollar text-emerald me-2"></i>Update Stok & Harga Dasar</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Harga Dasar (Base Price Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light rounded-start-3 px-3 fw-bold">Rp</span>
                            <input type="number" name="base_price" id="base_price" class="form-control form-control-lg border-0 bg-light rounded-end-3 px-4" value="{{ old('base_price', $service->base_price) }}" required min="0">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Stok Fisik Awal (Unit)</label>
                        <input type="number" name="stock" id="stock" class="form-control form-control-lg border-0 bg-light rounded-3 px-4 fw-bold" value="{{ old('stock', $service->stock) }}" required min="0">
                        <small class="text-muted mt-1 d-block" id="stockCalcInfo">
                            Stok di atas akan otomatis bertambah jika Anda mengisi batch restock unit di kartu pengadaan.
                        </small>
                    </div>
                </div>

                {{-- BIDANG KHUSUS SEWA --}}
                <div id="rentalFields" class="row g-3 mt-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Jaminan Sewa (Escrow Deposit Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light rounded-start-3 px-3 fw-bold">Rp</span>
                            <input type="number" name="escrow_deposit" id="escrow_deposit" class="form-control form-control-lg border-0 bg-light rounded-end-3 px-4" value="{{ old('escrow_deposit', $service->escrow_deposit) }}" min="0">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase ls-1">Minimal Durasi Sewa (Hari)</label>
                        <input type="number" name="min_duration_days" class="form-control form-control-lg border-0 bg-light rounded-3 px-4" value="{{ old('min_duration_days', $service->min_duration_days ?? 1) }}" min="1">
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
                    Harga baru yang akan langsung diperbarui ke <strong>Katalog Produk</strong>:
                </p>

                <div class="bg-white bg-opacity-20 p-3 rounded-4 mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small">Harga Dasar:</span>
                        <span class="fw-bold" id="previewBasePrice">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small">Biaya Kelengkapan:</span>
                        <span class="fw-bold" id="previewAdditionalPrice">Rp 0</span>
                    </div>
                    <hr class="my-2 border-white opacity-25">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Harga Hasil Kalkulasi:</span>
                        <h4 class="fw-bold m-0" id="previewTotalPrice">Rp 0</h4>
                    </div>
                </div>

                <div class="bg-black bg-opacity-20 p-3 rounded-3 small mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Total Stok Etalase:</span>
                        <strong class="text-warning" id="previewTotalStock">{{ $service->stock }} Unit</strong>
                    </div>
                </div>

                <div class="small opacity-90">
                    <i class="bi bi-arrow-repeat me-1"></i> Menyimpan formulir ini akan mengeksekusi sinkronisasi transaksi database atomik ke katalog produk.
                </div>
            </div>

            {{-- FOTO PRODUK --}}
            <div class="card card-modern border-0 p-4 text-center">
                <h5 class="fw-bold mb-3 text-start text-dark">Foto Alat</h5>
                @if($service->image)
                    <div class="mb-3">
                        <img src="{{ asset('assets/images/' . $service->image) }}" class="w-100 rounded-4 object-fit-cover border shadow-sm" style="height: 180px;" alt="{{ $service->name }}">
                        <small class="text-muted d-block mt-2">Foto saat ini</small>
                    </div>
                @endif
                <div class="upload-area bg-light rounded-4 p-3 border-2 border-dashed d-flex flex-column align-items-center justify-content-center">
                    <p class="small text-muted mb-2">Ganti foto alat jika diperlukan (Max 2MB)</p>
                    <input type="file" name="gambar" class="form-control form-control-sm border-0" accept="image/*">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-emerald btn-lg w-100 rounded-pill py-3 fw-bold shadow-sm">
                    <i class="bi bi-arrow-clockwise me-2"></i>Perbarui & Sinkronkan Produk
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
    const stockInput = document.getElementById('stock');
    const restockQtyInput = document.getElementById('restock_quantity');
    const detailsContainer = document.getElementById('detailsContainer');
    const btnAddDetail = document.getElementById('btnAddDetail');

    const previewBase = document.getElementById('previewBasePrice');
    const previewAdd = document.getElementById('previewAdditionalPrice');
    const previewTotal = document.getElementById('previewTotalPrice');
    const previewTotalStock = document.getElementById('previewTotalStock');

    let detailIndex = {{ $service->details->count() + 10 }};

    function toggleServiceType() {
        if (serviceType.value === 'sewa') {
            rentalFields.style.display = 'flex';
        } else {
            rentalFields.style.display = 'none';
        }
    }
    serviceType.addEventListener('change', toggleServiceType);
    toggleServiceType();

    function recalculateLivePrice() {
        const base = parseFloat(basePriceInput.value) || 0;
        let additionals = 0;
        document.querySelectorAll('.detail-price').forEach(input => {
            additionals += parseFloat(input.value) || 0;
        });

        const total = base + additionals;
        previewBase.innerText = 'Rp ' + base.toLocaleString('id-ID');
        previewAdd.innerText = 'Rp ' + additionals.toLocaleString('id-ID');
        previewTotal.innerText = 'Rp ' + total.toLocaleString('id-ID');

        const initialStock = parseInt(stockInput.value) || 0;
        const restockQty = parseInt(restockQtyInput ? restockQtyInput.value : 0) || 0;
        previewTotalStock.innerText = (initialStock + restockQty) + ' Unit';
    }

    basePriceInput.addEventListener('input', recalculateLivePrice);
    stockInput.addEventListener('input', recalculateLivePrice);
    if (restockQtyInput) {
        restockQtyInput.addEventListener('input', recalculateLivePrice);
    }

    detailsContainer.addEventListener('input', function (e) {
        if (e.target.classList.contains('detail-price')) {
            recalculateLivePrice();
        }
    });

    btnAddDetail.addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'detail-row bg-light rounded-4 p-3 mb-3 border';
        row.innerHTML = `
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-muted mb-1">Nama Item Kelengkapan</label>
                    <input type="text" name="details[${detailIndex}][item_name]" class="form-control border-0 bg-white rounded-3" placeholder="Contoh: Pasak Cadangan" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Biaya Tambahan (Rp)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-0 fw-bold">Rp</span>
                        <input type="number" name="details[${detailIndex}][additional_price]" class="form-control border-0 bg-white rounded-end-3 detail-price" placeholder="0" value="0" min="0">
                    </div>
                </div>
                <div class="col-md-3 text-end pt-3 pt-md-4">
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-3 btnRemoveDetail">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        detailsContainer.appendChild(row);
        detailIndex++;
        recalculateLivePrice();
    });

    detailsContainer.addEventListener('click', function (e) {
        const btn = e.target.closest('.btnRemoveDetail');
        if (btn) {
            btn.closest('.detail-row').remove();
            recalculateLivePrice();
        }
    });

    recalculateLivePrice();
});
</script>
@endsection
