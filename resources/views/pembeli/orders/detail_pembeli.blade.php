@extends('layouts.app_pembeli')

@section('extra_css')
<style>
    .tracking-timeline-item::before {
        content: '';
        position: absolute;
        left: 7px;
        top: 20px;
        bottom: -8px;
        width: 2px;
        background-color: #e2e8f0;
    }
    .tracking-timeline-item:last-child::before {
        display: none;
    }
</style>
@endsection

@section('content')
<div class="pt-28 pb-20 bg-slate-50">
    <div class="max-w-4xl mx-auto px-4">

        <!-- BACK BUTTON -->
        @php
            $hasRental = false;
            if (isset($pesanan) && $pesanan->details->isNotEmpty()) {
                $hasRental = $pesanan->details->contains('type', 'rent');
            }
            $backUrl = $hasRental ? route('profile', ['tab' => 'rentals']) : route('profile', ['tab' => 'purchases']);
            $backLabel = $hasRental ? 'Kembali ke Pemesanan Sewa' : 'Kembali ke Pemesanan Produk';
        @endphp
        <div class="mb-6">
            <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 text-sm font-semibold text-green-600 hover:text-green-800 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> {{ $backLabel }}
            </a>
        </div>

        <!-- KYC WARNING FOR BUYER -->
        @if(!auth()->user()->ktp_verified_at)
            <div class="mb-6 p-5 bg-amber-50 border-2 border-amber-200 rounded-3xl flex gap-5 items-start">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                    <i data-lucide="shield-alert" class="w-6 h-6"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-black text-amber-900 uppercase tracking-widest text-sm mb-1">Verifikasi Identitas Diperlukan</h3>
                    <p class="text-xs text-amber-800 leading-relaxed">
                        Pesanan Anda saat ini <strong>tertahan sementara</strong> karena identitas (KTP) Anda belum diverifikasi. 
                        Penjual hanya dapat memproses pesanan setelah identitas Anda dikonfirmasi demi keamanan bersama.
                    </p>
                    <a href="{{ route('profile') }}" class="mt-3 inline-block text-xs font-bold text-amber-900 border-b-2 border-amber-900 hover:text-amber-700 transition-colors">Cek Status KTP Saya →</a>
                </div>
            </div>
        @endif
        <!-- RETURN STATUS ALERTS -->
        @if($pesanan->returnRequest)
            @php
                $rStatus = $pesanan->returnRequest->status;
            @endphp

            @if($rStatus === 'checking')
                <div class="mb-6 p-5 bg-amber-50 border-2 border-amber-200 rounded-3xl flex gap-5 items-start shadow-sm">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                        <i data-lucide="search" class="w-6 h-6"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-black text-amber-900 uppercase tracking-widest text-sm mb-1">Pemeriksaan Barang</h3>
                        <p class="text-xs text-amber-800 leading-relaxed">
                            Barang yang Anda kembalikan sedang diperiksa oleh penjual/admin untuk memastikan kondisinya. 
                            Status akan diperbarui segera setelah pemeriksaan selesai.
                        </p>
                    </div>
                </div>
            @elseif($rStatus === 'pending')
                <div class="mb-6 p-5 bg-blue-50 border-2 border-blue-200 rounded-3xl flex gap-5 items-start shadow-sm">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                        <i data-lucide="clock" class="w-6 h-6"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-black text-blue-900 uppercase tracking-widest text-sm mb-1">Menunggu Penjual</h3>
                        <p class="text-xs text-blue-800 leading-relaxed">
                            Permintaan pengembalian Anda telah dikirim. Menunggu konfirmasi atau respon dari pihak penjual.
                        </p>
                    </div>
                </div>
            @endif
        @endif

        <!-- HEADER -->
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-800">
                {{ isset($detailId) ? 'Detail Barang' : 'Detail Pesanan' }} #{{ $pesanan->order_number ?? $pesanan->id }}
            </h1>

            @php
                // Jika sedang melihat barang spesifik, ambil info rental khusus barang itu
                if (isset($detailId)) {
                    $item = $pesanan->details->first();
                    $rental = \App\Models\Pembeli\Rental_pembeli::where('order_id', $pesanan->id)
                                ->where('product_id', $item->product_id)
                                ->first();
                } else {
                    $rental = \App\Models\Pembeli\Rental_pembeli::where('order_id', $pesanan->id)->first();
                }

                $rawStatus = strtolower($pesanan->status ?? '');
                $rentalStatus = $rental ? strtolower($rental->status ?? '') : null;

                if ($rental && in_array($rentalStatus, ['active', 'returned', 'completed'])) {
                    $displayStatus = match($rentalStatus) {
                        'active' => 'Sedang Disewa',
                        'returned' => 'Dikembalikan',
                        'completed' => 'Selesai',
                        default => 'Penyewaan Aktif'
                    };
                    $statusColor = match($rentalStatus) {
                        'active' => 'bg-blue-100 text-blue-800 border border-blue-200',
                        'returned' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                        'completed' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                        default => 'bg-blue-100 text-blue-800'
                    };
                } else {
                    $displayStatus = match($rawStatus) {
                        'menunggu', 'pending' => 'Menunggu Verifikasi Pembayaran',
                        'diproses', 'processing' => 'Barang Dikemas',
                        'dikirim', 'shipping' => 'Dalam Pengiriman',
                        'selesai', 'completed', 'delivered' => 'Pesanan Selesai',
                        'dibatalkan', 'cancelled' => 'Dibatalkan',
                        default => ucfirst($rawStatus)
                    };
                    $statusColor = match($rawStatus) {
                        'menunggu', 'pending' => 'bg-amber-100 text-amber-800 border border-amber-200',
                        'diproses', 'processing' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                        'dikirim', 'shipping' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                        'selesai', 'completed', 'delivered' => 'bg-blue-100 text-blue-800 border border-blue-200',
                        'dibatalkan', 'cancelled' => 'bg-red-100 text-red-800 border border-red-200',
                        default => 'bg-slate-100 text-slate-700'
                    };
                }
            @endphp

            <div class="flex items-center gap-2">
                @if(isset($detailId))
                    @php $specificItem = $pesanan->details->first(); @endphp
                    <span class="px-3 py-1 {{ $specificItem->type === 'rent' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100' }} rounded-full text-[10px] font-black uppercase tracking-widest border">
                        {{ $specificItem->type === 'rent' ? 'Pemesanan Sewa' : 'Pemesanan Produk' }}
                    </span>
                @elseif($rental)
                    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100">Pemesanan Sewa</span>
                @else
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-100">Pemesanan Produk</span>
                @endif
                <span class="px-4 py-1 rounded-full text-sm font-bold uppercase {{ $statusColor }}">
                    {{ $displayStatus }}
                </span>
            </div>
        </div>

        <!-- CARD -->
        <div class="bg-white rounded-3xl shadow-lg p-6 space-y-6">

            <!-- INFO PENGIRIMAN & PEMBAYARAN -->
            <div class="grid md:grid-cols-2 gap-8 border-b pb-6">
                <div class="space-y-3">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Informasi Pengiriman</p>
                    <div class="space-y-1">
                        <p class="font-bold text-slate-800">{{ $pesanan->receiver_name }}</p>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            {{ $pesanan->shipping_address }}<br>
                            {{ $pesanan->shipping_district }}, {{ $pesanan->shipping_city }}<br>
                            {{ $pesanan->shipping_postal_code }}
                        </p>
                        <p class="text-sm text-slate-600 mt-2">
                            <span class="font-semibold text-slate-800">Telepon:</span> {{ $pesanan->shipping_phone }}
                        </p>
                    </div>
                    <div class="pt-2 flex flex-wrap items-center gap-2">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-lg">
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Kurir:</span>
                            <span class="text-xs font-bold text-slate-800 uppercase">{{ $pesanan->kurir ?? '-' }}</span>
                        </div>
                        @php
                            $realResi = $pesanan->no_resi;
                            if (!$realResi && $pesanan->sellerOrders && $pesanan->sellerOrders->isNotEmpty()) {
                                $realResi = $pesanan->sellerOrders->first(fn($so) => !empty($so->no_resi))?->no_resi;
                            }
                        @endphp
                        @if($realResi)
                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 border border-emerald-200 rounded-lg">
                                <span class="text-[10px] font-bold text-emerald-700 uppercase">No. Resi:</span>
                                <span class="text-xs font-mono font-black text-emerald-900" id="noResiText">{{ $realResi }}</span>
                                <button type="button" onclick="copyResi()" class="text-emerald-700 hover:text-emerald-900 text-[10px] font-bold underline ml-1 cursor-pointer">Salin</button>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-3">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Rincian Pembayaran</p>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Metode Pembayaran</span>
                            <span class="font-bold text-slate-800 uppercase">{{ $pesanan->metode_pembayaran }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Status Pembayaran</span>
                            @if(in_array($pesanan->status, ['diproses', 'dikirim', 'selesai', 'processing', 'shipped', 'delivered', 'completed']))
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[10px] font-bold uppercase">Terverifikasi (Lunas)</span>
                            @else
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded text-[10px] font-bold uppercase">Menunggu Verifikasi Penjual</span>
                            @endif
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t">
                            <span class="font-bold text-slate-800">Total Transaksi {{ isset($detailId) ? '(Barang Ini)' : '' }}</span>
                            <span class="text-xl font-black text-emerald-600">
                                Rp {{ number_format(isset($detailId) ? ($pesanan->details->first()->harga * $pesanan->details->first()->qty + ($pesanan->details->first()->type === 'rent' ? ($pesanan->details->first()->product->escrow_amount > 0 ? $pesanan->details->first()->product->escrow_amount : ($pesanan->details->first()->product->buy_price * 0.25)) : 0)) : ($pesanan->total ?? 0)) }}
                            </span>
                        </div>
                        @if($pesanan->bukti_pembayaran)
                        <div class="pt-4 mt-4 border-t border-dashed border-slate-200">
                            <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Bukti Pembayaran</p>
                            <a href="{{ asset($pesanan->bukti_pembayaran) }}" target="_blank" class="group block relative rounded-xl overflow-hidden border-2 border-slate-100 hover:border-emerald-500 transition-all">
                                <img src="{{ asset($pesanan->bukti_pembayaran) }}" class="w-full h-32 object-cover opacity-80 group-hover:opacity-100 transition-opacity">
                                <div class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="text-white text-xs font-bold px-3 py-1 bg-black/50 rounded-full">Lihat Full Image</span>
                                </div>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- LIVE LOGISTICS & API COURIER TRACKING SECTION -->
            @php
                $orderStatus = strtolower($pesanan->status ?? '');
                $stepIndex = 1;
                if (in_array($orderStatus, ['diproses', 'menunggu_pengiriman'])) {
                    $stepIndex = 2;
                } elseif (in_array($orderStatus, ['dikirim', 'shipping', 'otw'])) {
                    $stepIndex = 3;
                } elseif (in_array($orderStatus, ['selesai', 'delivered', 'completed'])) {
                    $stepIndex = 4;
                }
                $storeData = $pesanan->details->first()?->product?->store;
            @endphp
            <div class="pt-6 border-b pb-6" id="tracking-section">
                <!-- Header Pelacakan -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                Live Pelacakan API Kurir
                            </span>
                            <span class="text-xs font-bold text-slate-400">#{{ $pesanan->order_number ?? $pesanan->id }}</span>
                        </div>
                        <h2 class="text-lg font-black text-slate-800 flex items-center gap-2">
                            <span>Status Pengiriman Kurir Ekspedisi</span>
                        </h2>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($orderStatus === 'dikirim' || $orderStatus === 'shipping' || $orderStatus === 'otw')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500 text-white shadow-sm">
                                <i data-lucide="truck" class="w-3.5 h-3.5 animate-bounce"></i> Dalam Pengiriman
                            </span>
                        @elseif($orderStatus === 'diproses' || $orderStatus === 'processing')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                <i data-lucide="package" class="w-3.5 h-3.5"></i> Barang Dikemas
                            </span>
                        @elseif($orderStatus === 'selesai' || $orderStatus === 'delivered' || $orderStatus === 'completed')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Paket Telah Diterima
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                <i data-lucide="clock" class="w-3.5 h-3.5"></i> Menunggu Verifikasi
                            </span>
                        @endif
                    </div>
                </div>

                <!-- 4-Stage Milestone Stepper -->
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 sm:p-5 mb-5">
                    <div class="grid grid-cols-4 gap-2 sm:gap-4 relative">
                        <!-- Progress Line -->
                        <div class="absolute top-4 left-6 right-6 h-1 bg-slate-200 -z-0">
                            <div class="h-full bg-emerald-500 transition-all duration-700" style="width: {{ $stepIndex == 1 ? '15%' : ($stepIndex == 2 ? '45%' : ($stepIndex == 3 ? '78%' : '100%')) }};"></div>
                        </div>

                        <!-- Step 1 -->
                        <div class="text-center relative z-10">
                            <div class="w-8 h-8 mx-auto mb-2 rounded-full flex items-center justify-center font-bold text-xs transition-all {{ $stepIndex >= 1 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200 ring-4 ring-emerald-50' : 'bg-slate-200 text-slate-500' }}">
                                ✓
                            </div>
                            <p class="text-xs font-bold text-slate-800 leading-tight">Dibuat</p>
                            <p class="text-[10px] text-slate-400 hidden sm:block mt-0.5">Pesanan masuk</p>
                        </div>

                        <!-- Step 2 -->
                        <div class="text-center relative z-10">
                            <div class="w-8 h-8 mx-auto mb-2 rounded-full flex items-center justify-center font-bold text-xs transition-all {{ $stepIndex >= 2 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200 ring-4 ring-emerald-50' : 'bg-slate-200 text-slate-500' }}">
                                @if($stepIndex > 2) ✓ @elseif($stepIndex == 2) 2 @else 2 @endif
                            </div>
                            <p class="text-xs font-bold {{ $stepIndex >= 2 ? 'text-slate-800' : 'text-slate-400' }} leading-tight">Barang Dikemas</p>
                            <p class="text-[10px] text-slate-400 hidden sm:block mt-0.5">
                                @if($stepIndex >= 2) Disiapkan toko @else Menunggu verifikasi @endif
                            </p>
                        </div>

                        <!-- Step 3 -->
                        <div class="text-center relative z-10">
                            <div class="w-8 h-8 mx-auto mb-2 rounded-full flex items-center justify-center font-bold text-xs transition-all {{ $stepIndex >= 3 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200 ring-4 ring-emerald-50' : 'bg-slate-200 text-slate-500' }}">
                                @if($stepIndex > 3) ✓ @else 3 @endif
                            </div>
                            <p class="text-xs font-bold {{ $stepIndex >= 3 ? 'text-slate-800' : 'text-slate-400' }} leading-tight">Dikirim</p>
                            <p class="text-[10px] text-slate-400 hidden sm:block mt-0.5">Kurir perjalanan</p>
                        </div>

                        <!-- Step 4 -->
                        <div class="text-center relative z-10">
                            <div class="w-8 h-8 mx-auto mb-2 rounded-full flex items-center justify-center font-bold text-xs transition-all {{ $stepIndex >= 4 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200 ring-4 ring-emerald-50' : 'bg-slate-200 text-slate-500' }}">
                                @if($stepIndex >= 4) ✓ @else 4 @endif
                            </div>
                            <p class="text-xs font-bold {{ $stepIndex >= 4 ? 'text-slate-800' : 'text-slate-400' }} leading-tight">Selesai</p>
                            <p class="text-[10px] text-slate-400 hidden sm:block mt-0.5">Paket sampai</p>
                        </div>
                    </div>
                </div>

                <!-- COURIER TRACKING PANEL -->
                @if($realResi)
                    @php
                        $kurirCode = strtoupper($pesanan->kurir ?? '');
                        $officialTrackingUrl = 'https://www.google.com/search?q=' . urlencode('cek resi ' . ($pesanan->kurir ?? '') . ' ' . $realResi);
                        if (str_contains($kurirCode, 'JNE')) {
                            $officialTrackingUrl = 'https://www.jne.co.id/id/tracking/trace';
                        } elseif (str_contains($kurirCode, 'J&T') || str_contains($kurirCode, 'JNT')) {
                            $officialTrackingUrl = 'https://jet.co.id/track';
                        } elseif (str_contains($kurirCode, 'SICEPAT')) {
                            $officialTrackingUrl = 'https://www.sicepat.com/checkAwb';
                        } elseif (str_contains($kurirCode, 'POS')) {
                            $officialTrackingUrl = 'https://www.posindonesia.co.id/id/tracking';
                        } elseif (str_contains($kurirCode, 'ANTERAJA')) {
                            $officialTrackingUrl = 'https://anteraja.id/tracking';
                        }
                    @endphp
                    <div class="bg-white rounded-[24px] border border-slate-200/80 shadow-sm overflow-hidden">
                        <!-- Header Kurir & No. Resi -->
                        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl shrink-0 border border-emerald-100/80">
                                    🚚
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-base font-black text-slate-800 tracking-wide uppercase">
                                            {{ $pesanan->kurir ?? 'JNE Express' }}
                                        </h3>
                                        <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold">
                                            Reguler
                                        </span>
                                        <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Resi Terverifikasi
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs text-slate-400 font-medium">No. Resi:</span>
                                        <span id="noResiText" class="font-mono text-sm sm:text-base font-black text-slate-900 tracking-wider">
                                            {{ $realResi }}
                                        </span>
                                        <button type="button" onclick="copyResi()" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 py-0.5 px-2 rounded-md transition cursor-pointer">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            Salin
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ $officialTrackingUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm shadow-emerald-600/20 cursor-pointer">
                                    <span>Lacak di Web Resmi {{ $pesanan->kurir ?? 'Ekspedisi' }}</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </div>
                        </div>

                        <!-- Content Area -->
                        <div class="p-5 sm:p-6 space-y-4">
                            <!-- Visual Route Connection (Asal & Tujuan) -->
                            <div class="bg-slate-50/80 rounded-2xl p-4 sm:p-5 border border-slate-100">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Pengirim / Asal -->
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-white shadow-xs border border-slate-200/80 flex items-center justify-center text-lg shrink-0">
                                            🏪
                                        </div>
                                        <div class="min-w-0">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Asal Pengiriman (Toko)</span>
                                            <p class="text-sm font-bold text-slate-800 truncate mt-0.5">{{ $storeData->nama_toko ?? 'Mitra Toko Campify' }}</p>
                                            <p class="text-xs text-slate-500 truncate">{{ $storeData->kota ?? 'Kota Bandung' }}</p>
                                        </div>
                                    </div>

                                    <!-- Penerima / Tujuan -->
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-white shadow-xs border border-slate-200/80 flex items-center justify-center text-lg shrink-0">
                                            📍
                                        </div>
                                        <div class="min-w-0">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Tujuan Pengiriman</span>
                                            <p class="text-sm font-bold text-slate-800 truncate mt-0.5">{{ $pesanan->receiver_name }}</p>
                                            <p class="text-xs text-slate-500 truncate">{{ $pesanan->shipping_district ? $pesanan->shipping_district . ', ' : '' }}{{ $pesanan->shipping_city }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Pengiriman Aktual Banner -->
                            <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50/60 border border-emerald-100 flex items-start gap-3.5">
                                <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm font-bold shrink-0 mt-0.5 shadow-sm shadow-emerald-600/30">
                                    ✓
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1">
                                        <span class="text-xs font-black text-emerald-900 uppercase tracking-wide">
                                            Status: Paket Sedang Dalam Pengiriman
                                        </span>
                                        <span class="text-[11px] text-slate-400 font-medium">
                                            Diperbarui: {{ $pesanan->updated_at ? $pesanan->updated_at->format('d M Y, H:i') . ' WIB' : 'Hari ini' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-600 leading-relaxed">
                                        Paket telah diserahkan oleh pihak toko kepada pihak ekspedisi <strong class="text-slate-800">{{ $pesanan->kurir ?? 'ekspedisi' }}</strong> dengan nomor resi <span class="font-mono font-bold text-slate-800">{{ $realResi }}</span>. Anda dapat melacak posisi terkini paket langsung melalui website resmi ekspedisi.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- If no resi yet -->
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl mx-auto">
                            📦
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm">Nomor Resi Belum Diterbitkan</h4>
                            <p class="text-xs text-slate-500 max-w-md mx-auto mt-1 leading-relaxed">
                                Pesanan Anda sedang disiapkan dan dikemas oleh pihak toko. Riwayat perjalanan paket dan pelacakan live API kurir akan otomatis muncul di bagian ini setelah paket diserahkan ke kurir.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            @php
                $globalShippingMedia = $pesanan->video_pengiriman 
                    ?: ($pesanan->sellerOrders?->first(fn($so) => !empty($so->video_pengiriman))?->video_pengiriman);
            @endphp
            @if($globalShippingMedia)
                @php
                    $ext = strtolower(pathinfo($globalShippingMedia, PATHINFO_EXTENSION));
                    $isVideo = in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'mkv']);
                @endphp
                <div class="bg-white border border-slate-200 rounded-[28px] p-6 shadow-sm mb-6">
                    <div class="flex items-center justify-between mb-3 pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                <i data-lucide="{{ $isVideo ? 'video' : 'image' }}" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Bukti Pengiriman dari Penjual</h3>
                                <p class="text-[11px] text-slate-500">Dokumentasi fisik barang saat disiapkan dan diserahkan ke kurir</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                            {{ $isVideo ? 'Video Pengiriman' : 'Foto Pengiriman' }}
                        </span>
                    </div>

                    <div class="max-w-lg">
                        @if($isVideo)
                            <div class="rounded-2xl overflow-hidden shadow-sm border border-slate-200 bg-black">
                                <video controls class="w-full max-h-72" preload="metadata">
                                    <source src="{{ asset($globalShippingMedia) }}">
                                    Browser Anda tidak mendukung pemutar video.
                                </video>
                            </div>
                        @else
                            <a href="{{ asset($globalShippingMedia) }}" target="_blank" class="inline-block relative rounded-2xl overflow-hidden border border-slate-200 shadow-sm hover:opacity-95 transition group">
                                <img src="{{ asset($globalShippingMedia) }}" alt="Bukti Pengiriman" class="max-h-64 rounded-2xl object-contain bg-slate-50">
                                <span class="absolute bottom-2 right-2 bg-slate-900/75 text-white text-[10px] px-2.5 py-1 rounded-lg font-bold group-hover:bg-slate-900">
                                    Klik untuk perbesar ↗
                                </span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- DAFTAR ITEM -->
            <div>
                <h2 class="font-semibold text-lg mb-4 text-slate-800">Daftar Item</h2>

                <div class="space-y-6">
                    @foreach($pesanan->details as $item)
                        @php
                            $produk = $item->product;
                            $sellerOrderForItem = $pesanan->sellerOrders
                                ->first(fn($sellerOrder) => $sellerOrder->items->contains('order_detail_id', $item->id));
                            $itemDelivered = $sellerOrderForItem?->status === 'delivered' || $pesanan->status === 'selesai';
                            // Cek tipe: jika di DB 'buy' tapi nama ada '(Sewa)', anggap sewa agar tampilan benar
                            $isBuy = ($item->type === 'buy' && !str_contains(strtolower($produk->name ?? $produk->nama_produk), '(sewa)'));
                        @endphp

                        <div class="border rounded-[24px] p-5 bg-slate-50/50 hover:bg-white hover:shadow-md transition-all duration-300">
                            <div class="flex flex-col md:flex-row gap-6">
                                <!-- FOTO PRODUK -->
                                <div class="w-full md:w-32 h-32 flex-shrink-0">
                                    @if($produk->image_url)
                                        <img src="{{ $produk->image_url }}" class="w-full h-full object-cover rounded-2xl shadow-sm" alt="{{ $produk->name ?? $produk->nama_produk }}">
                                    @else
                                        <div class="w-full h-full bg-slate-100 rounded-2xl flex items-center justify-center text-3xl">📦</div>
                                    @endif
                                </div>

                                <!-- INFO UTAMA -->
                                <div class="flex-1 space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                            bg-emerald-100 text-emerald-700">
                                            {{ $isBuy ? 'Pembelian' : 'Penyewaan' }}
                                        </span>
                                        <h3 class="font-bold text-slate-900 text-lg">
                                            {{ $produk->name ?? 'Produk tidak ditemukan' }}
                                        </h3>
                                    </div>

                                    @if($isBuy)
                                        {{-- LAYOUT PEMBELIAN --}}
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-2">
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Jumlah</p>
                                                <p class="font-medium">{{ $item->qty }} Item</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Harga Satuan</p>
                                                <p class="font-medium text-emerald-600">Rp {{ number_format($item->harga) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Nomor Resi</p>
                                                <p class="font-medium text-emerald-600">{{ $sellerOrderForItem?->no_resi ?? $pesanan->no_resi ?? 'Menunggu Pengiriman' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Subtotal</p>
                                                <p class="font-bold text-slate-900">Rp {{ number_format($item->harga * $item->qty) }}</p>
                                            </div>
                                        </div>

                                        {{-- Retur Section for Buy --}}
                                        @php
                                            $buyReturn = \App\Models\Pembeli\Return_pembeli::where('order_id', $pesanan->id)
                                                ->where('type', 'jual_beli')
                                                ->first();
                                        @endphp
                                        <div class="mt-4 pt-4 border-t border-slate-200">
                                            @if(!$buyReturn)
                                                @if($itemDelivered)
                                                    <a href="{{ route('orders.return', $item->id) }}" class="inline-flex items-center justify-center rounded-xl border-2 border-red-200 px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50 transition">
                                                        <i data-lucide="refresh-ccw" class="w-3 h-3 me-2"></i> AJUKAN RETUR BARANG
                                                    </a>
                                                @endif
                                            @else
                                                <div class="p-4 bg-red-50 rounded-2xl border border-red-100 flex items-center justify-between">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                                                        <span class="text-[10px] font-black text-red-700 uppercase tracking-widest">Status Retur: {{ $buyReturn->status }}</span>
                                                    </div>
                                                    <a href="{{ route('orders.return', $item->id) }}" class="text-[10px] font-bold text-red-900 underline">Lihat Detail</a>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Rating & Review Section --}}
                                        @if($itemDelivered)
                                        <div class="mt-4 pt-4 border-t border-slate-200" id="review-section">
                                            <p class="text-sm font-bold text-slate-800 mb-3">Rating & Ulasan</p>
                                            @include('pembeli.produk.partials.review_pembeli', ['produk' => $produk])
                                        </div>
                                        @endif
                                    @else
                                        {{-- LAYOUT PENYEWAAN --}}
                                        @php
                                            $returnInfo = \App\Models\Pembeli\Return_pembeli::where('order_id', $pesanan->id)->where('type', 'sewa')->first();
                                            $rentalInfo = \App\Models\Pembeli\Rental_pembeli::where('order_id', $pesanan->id)->first();
                                            $canReturnRental = in_array($pesanan->status, ['selesai', 'sampai']) || ($rentalInfo && in_array($rentalInfo->status, ['active', 'selesai', 'completed']));
                                        @endphp
                                        
                                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 py-2">
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Durasi</p>
                                                <p class="font-medium">{{ $item->duration ?? '3' }} Hari</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Mulai Sewa</p>
                                                <p class="font-medium">{{ $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('d M Y') : \Carbon\Carbon::parse($pesanan->created_at)->format('d M Y') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Selesai Sewa</p>
                                                <p class="font-medium">{{ $item->start_date ? \Carbon\Carbon::parse($item->start_date)->addDays($item->duration ?? 3)->format('d M Y') : \Carbon\Carbon::parse($pesanan->created_at)->addDays(3)->format('d M Y') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Harga Sewa</p>
                                                <p class="font-medium text-emerald-600">Rp {{ number_format($item->harga) }}</p>
                                            </div>
                                            <div>
                                                @php
                                                    // Ambil deposit dari record return jika sudah ada, atau hitung manual
                                                    $deposit = ($returnInfo && $returnInfo->deposit_amount > 0) 
                                                        ? $returnInfo->deposit_amount 
                                                        : ($produk->escrow_amount > 0 ? $produk->escrow_amount : (($produk->buy_price ?? 0) * 0.25));
                                                    
                                                    // Jika tetap 0 tapi ada escrow_total di return, gunakan itu
                                                    if ($deposit <= 0 && $returnInfo && $returnInfo->escrow_total > 0) {
                                                        $deposit = $returnInfo->escrow_total;
                                                    }
                                                @endphp
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Dana Jaminan (Escrow)</p>
                                                <p class="font-bold text-blue-600">Rp {{ number_format($deposit) }}</p>
                                                <p class="text-[8px] text-slate-400 mt-0.5 leading-tight">Akan dikembalikan utuh jika barang kembali aman.</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Total Dibayar</p>
                                                <p class="font-bold text-slate-900">Rp {{ number_format($item->harga + $deposit) }}</p>
                                            </div>
                                        </div>

                                        {{-- Return & Review Section for Rental --}}
                                        <div class="mt-4 pt-4 border-t border-slate-200 space-y-4">
                                            @if($rentalInfo && $rentalInfo->condition_photo_handover)
                                                <div class="p-4 bg-emerald-50/50 rounded-2xl border border-emerald-100">
                                                    <p class="text-[10px] font-black text-emerald-800 uppercase tracking-widest mb-2"><i class="bi bi-camera me-1"></i> Foto Kondisi Barang Saat Mulai Sewa</p>
                                                    <a href="{{ asset($rentalInfo->condition_photo_handover) }}" target="_blank" class="inline-block relative rounded-xl overflow-hidden border border-emerald-200 max-w-xs hover:opacity-95 transition shadow-sm">
                                                        <img src="{{ asset($rentalInfo->condition_photo_handover) }}" class="max-h-44 object-cover" style="max-width: 100%;">
                                                    </a>
                                                </div>
                                            @endif
                                            <div class="flex flex-col gap-3">
                                                @if(!$returnInfo)
                                                    @if($canReturnRental)
                                                        <a href="{{ route('orders.return', $item->id) }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 transition shadow-lg shadow-slate-200">
                                                            <i data-lucide="rotate-ccw" class="w-3 h-3 me-2"></i> AJUKAN PENGEMBALIAN SEKARANG
                                                        </a>
                                                    @elseif($rentalInfo && $rentalInfo->status === 'completed')
                                                        <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100 flex items-center gap-3">
                                                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                                            </div>
                                                            <p class="text-[10px] text-emerald-800 font-bold uppercase tracking-widest">Penyewaan Selesai</p>
                                                        </div>
                                                    @else
                                                        <div class="p-4 bg-slate-100 rounded-2xl border border-slate-200">
                                                            <p class="text-[10px] text-slate-500 font-bold leading-relaxed uppercase tracking-widest text-center">
                                                                <i data-lucide="clock" class="w-3 h-3 inline me-1"></i> Form pengembalian muncul setelah status "AKTIF"
                                                            </p>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-3">
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex items-center gap-2">
                                                                <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                                                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Status Pengembalian</span>
                                                            </div>
                                                            <span class="px-2 py-0.5 {{ $returnInfo->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($returnInfo->status === 'waiting_refund' ? 'bg-amber-100 text-amber-700' : 'bg-indigo-100 text-indigo-700') }} rounded text-[9px] font-bold uppercase">{{ str_replace('_', ' ', $returnInfo->status) }}</span>
                                                        </div>
                                                        
                                                        @if($returnInfo->status === 'completed')
                                                            <div class="space-y-2 pt-2 border-t border-dashed border-slate-200">
                                                                <div class="flex justify-between text-xs">
                                                                    <span class="text-slate-500">Dana Jaminan Awal</span>
                                                                    <span class="font-bold text-slate-800">Rp {{ number_format($returnInfo->deposit_amount) }}</span>
                                                                </div>
                                                                <div class="flex justify-between text-xs">
                                                                    <span class="text-slate-500">Total Denda (Telat + Rusak)</span>
                                                                    <span class="font-bold text-red-600">- Rp {{ number_format($returnInfo->total_fines) }}</span>
                                                                </div>
                                                                <div class="flex justify-between items-center pt-2 border-t border-slate-200">
                                                                    <span class="text-sm font-bold text-emerald-700">Refund Cair ke Anda</span>
                                                                    <span class="text-lg font-black text-emerald-600">Rp {{ number_format($returnInfo->to_buyer) }}</span>
                                                                </div>
                                                                <p class="text-[9px] text-slate-400 text-center italic mt-2">Dana telah dikembalikan ke saldo/rekening Anda.</p>
                                                            </div>
                                                        @elseif($returnInfo->status === 'waiting_refund')
                                                            <div class="space-y-2 pt-2 border-t border-dashed border-slate-200">
                                                                <p class="text-[10px] text-slate-600 font-medium">Barang diterima seller. <strong>Menunggu proses transfer dana refund dari Admin.</strong></p>
                                                                <div class="flex justify-between items-center pt-2 border-t border-slate-200">
                                                                    <span class="text-xs font-bold text-amber-700">Estimasi Refund</span>
                                                                    <span class="text-md font-black text-amber-600">Rp {{ number_format($returnInfo->to_buyer) }}</span>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <p class="text-[10px] text-slate-500 italic">Barang dalam proses pengecekan oleh Admin & Penjual. Harap tunggu konfirmasi refund.</p>
                                                        @endif
                                                        <a href="{{ route('orders.return', $item->id) }}" class="mt-2 w-full inline-flex items-center justify-center rounded-xl border-2 border-slate-200 py-2 text-[10px] font-black text-slate-600 hover:bg-slate-50 transition uppercase tracking-widest">
                                                            Lihat Detail Pengembalian →
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            @if($pesanan->status == 'selesai')
                                            <div id="review-section">
                                                <p class="text-sm font-bold text-slate-800 mb-3">Rating & Ulasan Penyewaan</p>
                                                @include('pembeli.produk.partials.review_pembeli', ['produk' => $produk])
                                            </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- REVIEW TOKO -->
            @if($pesanan->status == 'selesai' && $pesanan->details->isNotEmpty())
                @php
                    $firstItem = $pesanan->details->first();
                    // Gunakan properti yang sesuai untuk mendapat store_id/seller_id, contoh sellerUserId()
                    $storeId = $firstItem->product->sellerUserId() ?? $firstItem->product->seller_id ?? $firstItem->product->user_id;
                    
                    $existingStoreReview = \App\Models\Pembeli\StoreRating_pembeli::where('user_id', \Illuminate\Support\Facades\Auth::id())
                        ->where('store_id', $storeId)
                        ->first();
                @endphp

                <div class="mt-8 pt-6 border-t border-slate-200">
                    <h2 class="font-semibold text-lg mb-4 text-slate-800">Rating & Ulasan Toko</h2>
                    @if($existingStoreReview)
                        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <div class="flex text-yellow-400 text-lg">
                                        @for($i=1; $i<=5; $i++)
                                            <span>{!! $i <= $existingStoreReview->rating ? '★' : '☆' !!}</span>
                                        @endfor
                                    </div>
                                    <span class="text-xs font-bold text-slate-400">{{ $existingStoreReview->created_at->format('d M Y') }}</span>
                                </div>
                                <p class="text-sm text-slate-700 font-medium">"{{ $existingStoreReview->comment }}"</p>
                                @if(!empty($existingStoreReview->reply))
                                    <div class="mt-3 p-3 bg-emerald-50 border border-emerald-100 rounded-xl text-xs">
                                        <div class="flex items-center gap-1.5 font-bold text-emerald-800 mb-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                            <span>Respon Toko:</span>
                                        </div>
                                        <p class="text-slate-700 leading-relaxed">{{ $existingStoreReview->reply }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
                            <form action="{{ route('store.review.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $pesanan->id }}">
                                <input type="hidden" name="store_id" value="{{ $storeId }}">
                                <input type="hidden" name="rating" id="store-rating-value" value="5">
                                
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-3">Berikan Rating Untuk Toko</label>
                                    <div class="flex gap-2 text-3xl" id="store-star-container">
                                        @for($i=1; $i<=5; $i++)
                                            <button type="button" 
                                                    onclick="setStoreRating({{ $i }})" 
                                                    class="store-star-btn text-yellow-400 transition-transform hover:scale-110 focus:outline-none"
                                                    data-value="{{ $i }}">
                                                ★
                                            </button>
                                        @endfor
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Tulis Ulasan Toko</label>
                                    <textarea name="comment" rows="3" class="w-full rounded-2xl border-slate-200 text-sm focus:ring-emerald-500 focus:border-emerald-500" placeholder="Bagaimana pelayanan dari toko ini?" required></textarea>
                                </div>
                                
                                <button type="submit" class="w-full md:w-auto bg-emerald-600 text-white px-8 py-3 rounded-xl text-sm font-bold hover:bg-emerald-700 shadow-lg shadow-emerald-100 transition-all">
                                    Kirim Ulasan Toko
                                </button>
                            </form>

                            <script>
                                function setStoreRating(value) {
                                    document.getElementById('store-rating-value').value = value;
                                    const stars = document.querySelectorAll('.store-star-btn');
                                    stars.forEach((star, index) => {
                                        if (index < value) {
                                            star.innerHTML = '★';
                                            star.classList.remove('text-slate-300');
                                            star.classList.add('text-yellow-400');
                                        } else {
                                            star.innerHTML = '☆';
                                            star.classList.remove('text-yellow-400');
                                            star.classList.add('text-slate-300');
                                        }
                                    });
                                }
                            </script>
                        </div>
                    @endif
                </div>
            @endif
            </div>

            <!-- ACTION -->
            <div class="flex flex-col gap-3">
                @php
                    $shippedSellerOrders = $pesanan->sellerOrders ? $pesanan->sellerOrders->where('status', 'shipped') : collect();
                @endphp

                @if($shippedSellerOrders->count() > 1)
                    <!-- Notifikasi jika pesanan terbagi dalam beberapa toko / paket -->
                    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl">
                        <div class="flex items-center gap-2 font-bold text-emerald-900 text-xs mb-1">
                            <i data-lucide="info" class="w-4 h-4 text-emerald-600"></i>
                            <span>Pesanan Berisi {{ $shippedSellerOrders->count() }} Paket Pengiriman</span>
                        </div>
                        <p class="text-[11px] text-emerald-800 leading-relaxed">
                            Produk dalam pesanan ini dikirim oleh beberapa mitra toko berbeda. Jika semua paket telah Anda terima, Anda dapat mengonfirmasi sekaligus di bawah ini.
                        </p>
                    </div>

                    <!-- Tombol Konfirmasi Sekaligus Semua Paket -->
                    <form action="{{ route('orders.confirm', $pesanan->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl font-black text-sm uppercase tracking-widest transition-all shadow-lg shadow-emerald-100 flex items-center justify-center gap-3">
                            <i data-lucide="check-check" class="w-5 h-5"></i>
                            Konfirmasi Semua Paket Telah Diterima
                        </button>
                    </form>

                    <!-- Opsi Konfirmasi Terpisah Per Toko -->
                    <div class="space-y-2 mt-2 pt-2 border-t border-slate-200">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Atau Konfirmasi Terpisah Per Paket Toko:</p>
                        @foreach($shippedSellerOrders as $sellerOrder)
                            @php
                                $storeName = $sellerOrder->store?->nama_toko 
                                    ?? $sellerOrder->items->first()?->product?->store?->nama_toko 
                                    ?? 'Toko ' . $sellerOrder->seller_order_number;
                            @endphp
                            <form action="{{ route('orders.confirm', $pesanan->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="seller_order_id" value="{{ $sellerOrder->id }}">
                                <button type="submit" class="w-full bg-white hover:bg-emerald-50/50 text-slate-700 border-2 border-slate-200 hover:border-emerald-500 py-3 rounded-2xl font-bold text-xs transition-all flex items-center justify-between px-4">
                                    <span class="flex items-center gap-2 text-left">
                                        <i data-lucide="package-check" class="w-4 h-4 text-emerald-600"></i>
                                        <span>Konfirmasi Paket dari <strong>{{ $storeName }}</strong></span>
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $sellerOrder->seller_order_number }}</span>
                                </button>
                            </form>
                        @endforeach
                    </div>
                @elseif($shippedSellerOrders->count() === 1)
                    @php
                        $singleSellerOrder = $shippedSellerOrders->first();
                    @endphp
                    <form action="{{ route('orders.confirm', $pesanan->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="seller_order_id" value="{{ $singleSellerOrder->id }}">
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl font-black text-sm uppercase tracking-widest transition-all shadow-lg shadow-emerald-100 flex items-center justify-center gap-3">
                            <i data-lucide="package-check" class="w-5 h-5"></i>
                            Konfirmasi Pesanan Diterima
                        </button>
                    </form>
                @elseif($pesanan->status == 'dikirim')
                    <form action="{{ route('orders.confirm', $pesanan->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl font-black text-sm uppercase tracking-widest transition-all shadow-lg shadow-emerald-100 flex items-center justify-center gap-3">
                            <i data-lucide="package-check" class="w-5 h-5"></i>
                            Konfirmasi Pesanan Diterima
                        </button>
                    </form>
                @endif

                @if(in_array($pesanan->status, ['menunggu', 'diproses']))
                    <form action="{{ route('orders.cancel', $pesanan->id) }}" method="POST">
                        @csrf
                        <button class="w-full bg-red-50 hover:bg-red-100 text-red-600 py-3 rounded-xl font-bold text-xs uppercase tracking-widest transition border border-red-100">
                            Batalkan Pesanan
                        </button>
                    </form>
                @endif
            </div>

        </div>


    </div>
</div>
@endsection

@section('extra_js')
<script>
    function copyResi() {
        const el = document.getElementById('noResiText');
        const text = el ? el.innerText.trim() : '{{ $realResi }}';
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Nomor resi berhasil disalin: ' + text);
            }).catch(() => {
                prompt('Salin nomor resi:', text);
            });
        } else {
            prompt('Salin nomor resi:', text);
        }
    }
</script>
@endsection
