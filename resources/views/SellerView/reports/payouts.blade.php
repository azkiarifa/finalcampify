@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <h2 class="fw-bold m-0 text-dark">Pencairan Dana ke Saldo</h2>
            <p class="text-muted">Pantau status dana pesanan dari escrow holding hingga siap dicairkan ke saldo dompet toko.</p>
        </div>
        <a href="{{ route('seller.reports.index') }}" class="btn btn-light rounded-pill px-4 border fw-bold text-muted">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Laporan
        </a>
    </div>
</div>

{{-- METRIC SUMMARY CARDS --}}
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card card-modern p-4 border-0 h-100 shadow-sm border-start border-4 border-success">
            <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1">Total Dana Masuk Saldo</small>
            <h3 class="fw-bold m-0 text-success">Rp {{ number_format($totalDisbursed, 0, ',', '.') }}</h3>
            <span class="text-muted small mt-2 d-block"><i class="bi bi-check-circle-fill text-success me-1"></i>Dana resmi cair di dompet toko</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-modern p-4 border-0 h-100 shadow-sm border-start border-4 border-primary">
            <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1">Siap Dicairkan ke Saldo</small>
            <h3 class="fw-bold m-0 text-primary">Rp {{ number_format($totalReady, 0, ',', '.') }}</h3>
            <span class="text-muted small mt-2 d-block"><i class="bi bi-info-circle-fill text-primary me-1"></i>Klik tombol "Cairkan ke Saldo" di tabel</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-modern p-4 border-0 h-100 shadow-sm border-start border-4 border-warning">
            <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1">Dana Tertahan (Hold / Komplain)</small>
            <h3 class="fw-bold m-0 text-warning">Rp {{ number_format($totalWaiting, 0, ',', '.') }}</h3>
            <span class="text-muted small mt-2 d-block"><i class="bi bi-clock-history text-warning me-1"></i>Masa garansi 2 hari atau ada komplain</span>
        </div>
    </div>
</div>

{{-- FILTER TABS --}}
<div class="card card-modern p-4 mb-5 border-0 shadow-sm">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
        <a href="{{ route('seller.reports.payouts') }}"
           class="btn rounded-pill px-4 py-2 fw-semibold {{ !$status ? 'btn-emerald' : 'btn-light text-muted' }}">
            Semua
        </a>
        <button type="submit" name="status" value="DISBURSED"
                class="btn rounded-pill px-4 py-2 fw-semibold {{ $status === 'DISBURSED' ? 'btn-emerald' : 'btn-light text-muted' }}">
            Sudah Masuk Saldo
        </button>
        <button type="submit" name="status" value="READY_TO_DISBURSE"
                class="btn rounded-pill px-4 py-2 fw-semibold {{ $status === 'READY_TO_DISBURSE' ? 'btn-primary' : 'btn-light text-muted' }}">
            Siap Dicairkan
        </button>
        <button type="submit" name="status" value="WAITING_HOLD"
                class="btn rounded-pill px-4 py-2 fw-semibold {{ $status === 'WAITING_HOLD' ? 'btn-warning text-dark' : 'btn-light text-muted' }}">
            Masa Hold (Garansi)
        </button>
        <button type="submit" name="status" value="WAITING_DELIVERY"
                class="btn rounded-pill px-4 py-2 fw-semibold {{ $status === 'WAITING_DELIVERY' ? 'btn-secondary' : 'btn-light text-muted' }}">
            Belum Diterima
        </button>
    </form>
</div>

{{-- FLASH NOTIFICATIONS --}}
@if(session('success'))
<div class="alert alert-success alert-modern d-flex align-items-center mb-4 shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
    <div>{{ session('success') }}</div>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-modern d-flex align-items-center mb-4 shadow-sm" role="alert">
    <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i>
    <div>{{ session('error') }}</div>
</div>
@endif

{{-- DATA TABLE --}}
<div class="card card-modern border-0 overflow-hidden shadow-sm">
    <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="fw-bold m-0 text-dark">Daftar Dana Pesanan & Aksi Pencairan</h5>
            <small class="text-muted">Dana otomatis berstatus Siap Dicairkan dalam 2 hari tanpa komplain atau melalui persetujuan admin.</small>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3 text-muted small text-uppercase">Pesanan</th>
                    <th class="px-4 py-3 text-muted small text-uppercase">Pembeli</th>
                    <th class="px-4 py-3 text-muted small text-uppercase">Status Pengiriman</th>
                    <th class="px-4 py-3 text-muted small text-uppercase">Status Dana</th>
                    <th class="px-4 py-3 text-muted small text-uppercase text-end">Nominal</th>
                    <th class="px-4 py-3 text-muted small text-uppercase">Waktu Selesai</th>
                    <th class="px-4 py-3 text-muted small text-uppercase text-center">Aksi Pencairan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payoutOrders as $order)
                    @php
                        $payout = $order->payout;
                        $payoutStatus = $payout?->status ?? 'WAITING_DELIVERY';

                        // Cek komplain aktif
                        $hasComplaint = \App\Models\SellerModels\Return_seller::where('order_id', $order->order_id)
                            ->whereIn('status', ['pending', 'review', 'in_progress', 'approved_by_seller', 'refund_pending'])
                            ->exists();
                    @endphp
                    <tr>
                        <td class="px-4 py-4">
                            <div class="fw-bold text-dark">{{ $order->seller_order_number ?? '#'.$order->id }}</div>
                            <small class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</small>
                        </td>
                        <td class="px-4 py-4">
                            <span class="fw-semibold text-dark">{{ $order->buyer->name ?? $order->buyer_name ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="badge bg-light text-dark border rounded-pill px-3 py-2 small">{{ $order->status_label }}</span>
                        </td>
                        <td class="px-4 py-4">
                            {{-- INDIKATOR STATUS BIASA (BUKAN GAYA BUTTON) --}}
                            @if($payoutStatus === 'DISBURSED')
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 fw-bold">
                                    <i class="bi bi-wallet2 me-1"></i>Sudah Masuk Saldo
                                </span>
                            @elseif($payoutStatus === 'READY_TO_DISBURSE')
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-bold">
                                    <i class="bi bi-check-circle-fill me-1"></i>Siap Dicairkan
                                </span>
                            @elseif($hasComplaint)
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-2 fw-bold">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Tertahan Komplain
                                </span>
                            @elseif($payoutStatus === 'WAITING_HOLD')
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2 fw-bold" title="Masa tunggu garansi 2 hari sejak barang diterima">
                                    <i class="bi bi-clock-history me-1"></i>Masa Garansi (2 Hari)
                                </span>
                            @else
                                <span class="badge bg-light text-muted border rounded-pill px-3 py-2 small">
                                    <i class="bi bi-truck me-1"></i>Belum Diterima
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-end fw-bold text-dark">
                            Rp {{ number_format($payout?->amount ?? $order->subtotal, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4">
                            @if($payout?->disbursed_at)
                                <span class="fw-semibold text-success d-block">{{ $payout->disbursed_at->format('d M Y') }}</span>
                                <small class="text-muted">{{ $payout->disbursed_at->format('H:i') }} WIB</small>
                            @elseif($payout?->ready_at)
                                <span class="fw-semibold text-dark d-block">Siap: {{ $payout->ready_at->format('d M Y') }}</span>
                                <small class="text-muted">{{ $payout->ready_at->format('H:i') }} WIB</small>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            {{-- TOMBOL AKSI PENCAIRAN TERPISAH SECARA VISUAL --}}
                            @if($payoutStatus === 'READY_TO_DISBURSE')
                                <form action="{{ route('seller.reports.payouts.claim', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Cairkan dana pesanan ini sebesar Rp {{ number_format($payout?->amount ?? $order->subtotal, 0, ',', '.') }} ke saldo dompet toko?')">
                                    @csrf
                                    <button type="submit" class="btn btn-emerald btn-sm rounded-pill px-3 fw-bold shadow-sm text-nowrap">
                                        <i class="bi bi-cash-stack me-1"></i>Cairkan ke Saldo
                                    </button>
                                </form>
                            @elseif($payoutStatus === 'DISBURSED')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill small fw-bold">
                                    <i class="bi bi-check2-all me-1"></i>Tersedia di Saldo
                                </span>
                            @elseif($hasComplaint)
                                <button class="btn btn-light btn-sm rounded-pill px-3 border text-danger small text-nowrap" disabled title="Ada komplain retur aktif yang sedang diproses">
                                    <i class="bi bi-lock-fill me-1"></i>Tertahan Komplain
                                </button>
                            @elseif($payoutStatus === 'WAITING_HOLD')
                                <button class="btn btn-light btn-sm rounded-pill px-3 border text-muted small text-nowrap" disabled title="Dana otomatis siap dicairkan 2 hari setelah barang diterima">
                                    <i class="bi bi-hourglass-split me-1"></i>Menunggu 2 Hari
                                </button>
                            @else
                                <a href="{{ route('seller.orders.show', $order->id) }}" class="btn btn-light btn-sm rounded-pill px-3 border text-muted small text-nowrap">
                                    Lihat Detail
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-wallet2 fs-1 text-muted d-block mb-3"></i>
                            <p class="text-muted mb-0">Belum ada data pencairan dana untuk filter ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($payoutOrders->hasPages())
        <div class="p-4 border-top bg-light">
            {{ $payoutOrders->links() }}
        </div>
    @endif
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .text-emerald { color: #10B981 !important; }
    .bg-emerald { background-color: #10B981 !important; color: white !important; }
    .btn-emerald { background-color: #10B981; color: white; border: none; }
    .btn-emerald:hover { background-color: #059669; color: white; transform: scale(1.02); }
    .bg-emerald-soft { background-color: #ecfdf5; }
    .bg-primary-subtle { background-color: #eff6ff !important; color: #1d4ed8 !important; }
    .bg-warning-subtle { background-color: #fffbeb !important; color: #b45309 !important; }
    .bg-success-subtle { background-color: #ecfdf5 !important; color: #047857 !important; }
    .bg-danger-subtle { background-color: #fef2f2 !important; color: #b91c1c !important; }
</style>
@endsection
