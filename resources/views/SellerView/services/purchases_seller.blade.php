@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-7">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('seller.services.index') }}" class="btn btn-light rounded-circle shadow-sm border p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h2 class="fw-bold m-0 text-dark">Riwayat Pasokan Stok & Vendor</h2>
                    <p class="text-muted small m-0">Catatan pengadaan unit masuk dari vendor/distributor. Penambahan stok otomatis menambah stok katalog produk.</p>
                </div>
            </div>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex justify-content-md-end gap-2">
            <button type="button" class="btn btn-emerald px-4 py-2 rounded-pill shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalRestock">
                <i class="bi bi-box-arrow-in-down me-2"></i>Pasok Stok Baru (+)
            </button>
            <a href="{{ route('seller.services.edit', $service->id) }}" class="btn btn-light rounded-pill px-3 py-2 border shadow-sm">
                <i class="bi bi-sliders me-1"></i>Edit Layanan
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

@if ($errors->any())
    <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">
        <ul class="mb-0 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- OVERVIEW CARD --}}
@php
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
@endphp
<div class="card card-modern border-0 p-4 mb-4 shadow-sm">
    <div class="row align-items-center g-4">
        <div class="col-lg-5">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-4 overflow-hidden border bg-light flex-shrink-0" style="width: 84px; height: 84px;">
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" class="w-100 h-100 object-fit-cover" alt="{{ $service->name }}">
                    @else
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted fs-3">🏕️</div>
                    @endif
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge {{ $service->service_type === 'sewa' ? 'bg-primary' : 'bg-success' }} rounded-pill px-2.5 py-1 text-uppercase" style="font-size: 0.7rem;">
                            {{ $service->service_type === 'sewa' ? 'Penyewaan Alat' : 'Jual Beli' }}
                        </span>
                        <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1" style="font-size: 0.7rem;">
                            {{ $service->category?->name ?? 'Kategori Umum' }}
                        </span>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ $service->name }}</h5>
                    <small class="text-muted">Harga: <strong>Rp {{ number_format($service->calculateFinalPrice(), 0, ',', '.') }}</strong> {{ $service->service_type === 'sewa' ? '/hari' : '' }}</small>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="row g-2 text-center text-md-start">
                <div class="col-sm-4">
                    <div class="p-3 bg-light rounded-4 border">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.68rem;">Stok Tersedia Saat Ini</small>
                        <h4 class="fw-bold m-0 text-emerald mt-1">{{ $service->stock }} <span class="fs-6 text-muted fw-normal">Unit</span></h4>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="p-3 bg-light rounded-4 border">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.68rem;">Total Unit Pernah Dipasok</small>
                        <h4 class="fw-bold m-0 text-dark mt-1">+{{ $totalPurchasedUnits }} <span class="fs-6 text-muted fw-normal">Unit</span></h4>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="p-3 bg-light rounded-4 border">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.68rem;">Total Modal Pengadaan</small>
                        <h4 class="fw-bold m-0 text-dark mt-1">Rp {{ number_format($totalCapital, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- BUKU PASOKAN VENDOR (INBOUND STOCK LEDGER) --}}
<div class="card card-modern border-0 p-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold text-dark m-0"><i class="bi bi-journal-text text-emerald me-2"></i>Buku Riwayat Pasokan Vendor</h5>
            <small class="text-muted">Daftar seluruh transaksi pengadaan stok unit masuk dari vendor pemasok.</small>
        </div>
        <span class="badge bg-light text-muted border px-3 py-2 rounded-pill fw-bold">
            Total: {{ $purchases->count() }} Catatan Pasokan
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="small text-uppercase text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <th class="ps-3">No</th>
                    <th>No. Faktur / Nota</th>
                    <th>Waktu Masuk</th>
                    <th>Asal Vendor / Pemasok</th>
                    <th class="text-center">Jumlah Masuk</th>
                    <th>Modal Satuan</th>
                    <th>Total Biaya</th>
                    <th>Kondisi</th>
                    <th>Bukti Nota</th>
                    <th class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $idx => $purchase)
                @php
                    $totalRowCost = $purchase->total_cost ?: ($purchase->purchase_price * $purchase->quantity);
                @endphp
                <tr>
                    <td class="ps-3 text-muted fw-bold">{{ $idx + 1 }}</td>
                    <td>
                        <span class="badge bg-light text-dark border px-2.5 py-1.5 font-monospace fw-bold">
                            {{ $purchase->invoice_number ?: ('PO-' . $purchase->id) }}
                        </span>
                    </td>
                    <td>
                        <strong class="d-block text-dark">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</strong>
                        <small class="text-muted" style="font-size: 0.72rem;">
                            <i class="bi bi-clock me-1"></i>{{ $purchase->received_at ? $purchase->received_at->format('H:i') : ($purchase->created_at ? $purchase->created_at->format('H:i') : '-') }} WIB
                        </small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-icon bg-emerald-subtle text-emerald rounded-3 p-1.5 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-shop fs-6"></i>
                            </div>
                            <div>
                                <span class="fw-bold text-dark d-block">{{ $purchase->purchase_store }}</span>
                                @if($purchase->vendor_phone)
                                    <small class="text-muted d-block" style="font-size: 0.72rem;"><i class="bi bi-telephone me-1"></i>{{ $purchase->vendor_phone }}</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-bold fs-6">
                            +{{ $purchase->quantity }} Unit
                        </span>
                    </td>
                    <td>
                        <span class="fw-semibold text-dark">Rp {{ number_format($purchase->purchase_price, 0, ',', '.') }}</span>
                    </td>
                    <td>
                        <strong class="text-emerald">Rp {{ number_format($totalRowCost, 0, ',', '.') }}</strong>
                    </td>
                    <td>
                        @php
                            $condClass = match($purchase->condition) {
                                'baru' => 'bg-emerald text-white',
                                'bekas_mulus' => 'bg-info-subtle text-info-emphasis border',
                                default => 'bg-secondary-subtle text-secondary'
                            };
                        @endphp
                        <span class="badge {{ $condClass }} rounded-pill px-2.5 py-1 text-capitalize" style="font-size: 0.7rem;">
                            {{ str_replace('_', ' ', $purchase->condition ?? 'baru') }}
                        </span>
                    </td>
                    <td>
                        @if($purchase->receipt_proof)
                            <a href="{{ $purchase->receipt_url }}" target="_blank" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-emerald fw-semibold" title="Lihat foto nota">
                                <i class="bi bi-receipt me-1"></i>Lihat
                            </a>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td class="text-end pe-3">
                        <button type="button" class="btn btn-sm btn-outline-emerald rounded-pill px-3 py-1.5 fw-bold" onclick="showPurchaseDetail({{ $purchase->id }})">
                            <i class="bi bi-eye me-1"></i>Detail Faktur
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5 text-muted">
                        <div class="py-4">
                            <i class="bi bi-box-seam fs-1 d-block mb-3 opacity-50"></i>
                            <h6 class="fw-bold text-dark">Belum Ada Riwayat Pasokan Vendor</h6>
                            <p class="text-muted small mb-3">Klik tombol "Pasok Stok Baru (+)" untuk mencatat pengadaan unit baru dari vendor.</p>
                            <button type="button" class="btn btn-emerald rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalRestock">
                                <i class="bi bi-plus-circle me-1"></i>Tambah Pasokan Pertama
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL PASOK STOK BARU (RESTOCK MODAL) --}}
<div class="modal fade" id="modalRestock" tabindex="-1" aria-labelledby="modalRestockLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header bg-emerald text-white p-4 border-0">
                <div>
                    <h5 class="modal-title fw-bold m-0" id="modalRestockLabel">
                        <i class="bi bi-box-arrow-in-down me-2"></i>Formulir Pasok Stok Baru dari Vendor
                    </h5>
                    <small class="text-white-50">Catat transaksi pengadaan unit masuk. Stok unit akan langsung bertambah ke sistem.</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('seller.services.restock', $service->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 p-md-5">
                    <div class="p-3 bg-light rounded-4 mb-4 border border-slate-200">
                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.68rem;">Unit Layanan Tujuan:</small>
                        <h6 class="fw-bold text-dark m-0 mt-1">{{ $service->name }}</h6>
                        <small class="text-muted">Stok aktif saat ini: <strong>{{ $service->stock }} Unit</strong></small>
                    </div>

                    <div class="row g-3">
                        {{-- IDENTITAS VENDOR --}}
                        <div class="col-12">
                            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-shop text-emerald me-1"></i>1. Data Vendor / Toko Pemasok</h6>
                            <hr class="mt-2 mb-3">
                        </div>

                        <div class="col-md-7">
                            <label class="form-label small fw-bold text-muted">Nama Vendor / Toko / Distributor <span class="text-danger">*</span></label>
                            <input type="text" name="purchase_store" class="form-control border-0 bg-light rounded-3 px-3 py-2.5" placeholder="Contoh: Eiger Flagship Store, Distributor Tenda Jaya" required>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-muted">No. Telepon / WhatsApp Vendor</label>
                            <input type="text" name="vendor_phone" class="form-control border-0 bg-light rounded-3 px-3 py-2.5" placeholder="Contoh: 08123456789">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Alamat / Lokasi Toko Vendor</label>
                            <input type="text" name="vendor_address" class="form-control border-0 bg-light rounded-3 px-3 py-2.5" placeholder="Contoh: Jl. Cihampelas No. 120, Kota Bandung">
                        </div>

                        {{-- DETAIL TRANSAKSI PASOKAN --}}
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-receipt text-emerald me-1"></i>2. Rincian Unit Masuk & Biaya</h6>
                            <hr class="mt-2 mb-3">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Tanggal Masuk Transaksi <span class="text-danger">*</span></label>
                            <input type="date" name="purchase_date" class="form-control border-0 bg-light rounded-3 px-3 py-2.5" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">No. Nota / Faktur Pembelian</label>
                            <input type="text" name="invoice_number" class="form-control border-0 bg-light rounded-3 px-3 py-2.5" placeholder="Contoh: INV-EIGER-8831 (Opsional)">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Kondisi Unit Masuk <span class="text-danger">*</span></label>
                            <select name="condition" class="form-select border-0 bg-light rounded-3 px-3 py-2.5" required>
                                <option value="baru" selected>Baru (Brand New)</option>
                                <option value="bekas_mulus">Bekas Mulus (Like New)</option>
                                <option value="rekondisi">Rekondisi</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Jumlah Unit Tambahan Masuk (+Qty) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 fw-bold text-emerald">+</span>
                                <input type="number" name="quantity" id="inputQuantity" class="form-control border-0 bg-light px-3 py-2.5" min="1" value="1" required oninput="calculateModalTotal()">
                                <span class="input-group-text bg-light border-0 text-muted">Unit</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Harga Beli Modal per Unit (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 fw-bold text-muted">Rp</span>
                                <input type="number" name="purchase_price" id="inputPrice" class="form-control border-0 bg-light px-3 py-2.5" min="0" placeholder="0" required oninput="calculateModalTotal()">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="p-3 rounded-3" style="background-color: #f0fdf4; border: 1px dashed #86efac;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold text-dark">Total Biaya Pengadaan Batch Ini:</span>
                                    <span class="fs-5 fw-bold text-emerald" id="previewTotalCost">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Unggah Foto Bukti Struk / Faktur Nota</label>
                            <input type="file" name="receipt_proof" class="form-control border-0 bg-light rounded-3 px-3 py-2" accept="image/*,application/pdf">
                            <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">Format: JPG, PNG, WEBP, PDF (Maks 3MB)</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Catatan Pengadaan / Serial Number</label>
                            <textarea name="notes" rows="2" class="form-control border-0 bg-light rounded-3 px-3 py-2" placeholder="Catatan batch pengadaan, garansi toko, nomor seri alat..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light p-3 border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-emerald rounded-pill px-5 fw-bold shadow-sm">
                        <i class="bi bi-check-circle me-1"></i>Simpan & Pasok Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL DETAIL FAKTUR PASOKAN (SYMMETRIC TO BUYER ORDER DETAIL) --}}
<div class="modal fade" id="modalPurchaseDetail" tabindex="-1" aria-labelledby="modalPurchaseDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-icon bg-white bg-opacity-20 text-white rounded-circle p-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-receipt-cutoff fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold m-0" id="detailInvoiceTitle">Faktur Pasokan Vendor</h5>
                        <small class="text-white-50" id="detailReceivedAt">-</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 p-md-5">
                {{-- TOP STATUS BAR --}}
                <div class="d-flex justify-content-between align-items-center p-3 rounded-4 bg-light border mb-4">
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.68rem;">Status Transaksi Pasokan:</small>
                        <span class="badge bg-emerald text-white rounded-pill px-3 py-1.5 fw-bold mt-1">
                            <i class="bi bi-check-circle-fill me-1"></i>Stok Berhasil Masuk (+ Unit)
                        </span>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.68rem;">No. Faktur / PO:</small>
                        <span class="font-monospace fw-bold fs-6 text-dark" id="detailInvoiceNumber">-</span>
                    </div>
                </div>

                {{-- INFORMASI VENDOR & TANGGAL --}}
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-4 border h-100">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-shop text-emerald me-2"></i>Informasi Vendor Pemasok</h6>
                            <p class="fw-bold text-dark m-0 mb-1" id="detailVendorName">-</p>
                            <small class="text-muted d-block mb-1" id="detailVendorPhone"><i class="bi bi-telephone me-1"></i>-</small>
                            <small class="text-muted d-block" id="detailVendorAddress"><i class="bi bi-geo-alt me-1"></i>-</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-4 border h-100">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-calendar-check text-emerald me-2"></i>Waktu & Detail Pengadaan</h6>
                            <small class="text-muted d-block mb-1">Tanggal Transaksi: <strong class="text-dark" id="detailPurchaseDate">-</strong></small>
                            <small class="text-muted d-block mb-1">Waktu Masuk Sistem: <strong class="text-dark" id="detailSystemTime">-</strong></small>
                            <small class="text-muted d-block">Kondisi Barang: <span class="badge bg-white text-dark border" id="detailCondition">-</span></small>
                        </div>
                    </div>
                </div>

                {{-- TABEL RINCIAN UNIT & BIAYA MODAL --}}
                <div class="card border rounded-4 overflow-hidden mb-4">
                    <table class="table align-middle mb-0">
                        <thead class="table-light small text-muted text-uppercase">
                            <tr>
                                <th class="ps-3">Nama Alat Outdoor</th>
                                <th class="text-center">Kuantitas Masuk</th>
                                <th class="text-end">Harga Beli Satuan</th>
                                <th class="text-end pe-3">Subtotal Modal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-3 fw-bold text-dark" id="detailServiceName">{{ $service->name }}</td>
                                <td class="text-center">
                                    <span class="badge bg-success text-white px-2.5 py-1 rounded-pill" id="detailQty">+1 Unit</span>
                                </td>
                                <td class="text-end" id="detailPrice">Rp 0</td>
                                <td class="text-end pe-3 fw-bold text-emerald" id="detailSubtotal">Rp 0</td>
                            </tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total Biaya Pengadaan Vendor:</th>
                                <th class="text-end pe-3 fs-5 text-emerald fw-bold" id="detailTotalCost">Rp 0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- CATATAN & BUKTI NOTA --}}
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="small fw-bold text-muted mb-1">Catatan Pengadaan / Keterangan:</label>
                        <div class="p-3 bg-light rounded-3 border text-dark small" id="detailNotes">
                            -
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label class="small fw-bold text-muted mb-1">Foto Bukti Struk / Nota:</label>
                        <div id="detailReceiptContainer" class="p-2 border rounded-3 text-center bg-light">
                            <span class="text-muted small">Tidak ada foto nota.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light p-3 border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function calculateModalTotal() {
        const qty = parseInt(document.getElementById('inputQuantity').value) || 0;
        const price = parseInt(document.getElementById('inputPrice').value) || 0;
        const total = qty * price;
        document.getElementById('previewTotalCost').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    function showPurchaseDetail(purchaseId) {
        const url = "{{ url('/seller/services/' . $service->id . '/purchases') }}/" + purchaseId;
        
        fetch(url)
            .then(res => res.json())
            .then(data => {
                document.getElementById('detailInvoiceTitle').textContent = 'Faktur Pasokan: ' + data.invoice_number;
                document.getElementById('detailInvoiceNumber').textContent = data.invoice_number;
                document.getElementById('detailReceivedAt').textContent = 'Diterima pada ' + data.received_at;
                
                document.getElementById('detailVendorName').textContent = data.purchase_store;
                document.getElementById('detailVendorPhone').innerHTML = '<i class="bi bi-telephone me-1"></i>' + (data.vendor_phone || 'Tidak dicatat');
                document.getElementById('detailVendorAddress').innerHTML = '<i class="bi bi-geo-alt me-1"></i>' + (data.vendor_address || 'Tidak dicatat');
                
                document.getElementById('detailPurchaseDate').textContent = data.purchase_date;
                document.getElementById('detailSystemTime').textContent = data.received_at;
                document.getElementById('detailCondition').textContent = data.condition;
                
                document.getElementById('detailServiceName').textContent = data.service_name;
                document.getElementById('detailQty').textContent = '+' + data.quantity + ' Unit';
                document.getElementById('detailPrice').textContent = data.formatted_price;
                document.getElementById('detailSubtotal').textContent = data.formatted_total;
                document.getElementById('detailTotalCost').textContent = data.formatted_total;
                
                document.getElementById('detailNotes').textContent = data.notes;
                
                const receiptBox = document.getElementById('detailReceiptContainer');
                if (data.receipt_url) {
                    receiptBox.innerHTML = `
                        <a href="${data.receipt_url}" target="_blank">
                            <img src="${data.receipt_url}" class="img-fluid rounded border shadow-sm" style="max-height: 140px;" alt="Bukti Nota">
                        </a>
                        <small class="d-block mt-1"><a href="${data.receipt_url}" target="_blank" class="text-emerald fw-semibold">Klik untuk memperbesar</a></small>
                    `;
                } else {
                    receiptBox.innerHTML = '<span class="text-muted small py-3 d-block">Tidak ada foto nota/struk.</span>';
                }
                
                const detailModal = new bootstrap.Modal(document.getElementById('modalPurchaseDetail'));
                detailModal.show();
            })
            .catch(err => {
                alert('Gagal memuat detail pasokan vendor.');
            });
    }
</script>
@endsection
