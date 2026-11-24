@extends('layouts.app_pembeli')

@section('extra_css')
<style>
    .landing-hero-content-wrapper {
        padding-top: 195px !important;
        padding-bottom: 80px !important;
    }
    @media (max-width: 640px) {
        .landing-hero-content-wrapper {
            padding-top: 175px !important;
            padding-bottom: 60px !important;
        }
    }
    .landing-hero-desc, .landing-hero-desc p {
        color: #f8fafc !important;
    }
</style>
@endsection

@section('content')
<div class="bg-slate-50 text-slate-800 antialiased">

    {{-- ==================== 1. HERO SECTION ==================== --}}
    <section class="relative min-h-[780px] lg:min-h-[850px] overflow-hidden">
        {{-- Background Image: Foto Camping Malam Hari Premium (Sesuai Dashboard Pembeli) --}}
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1510312305653-8ed496efae75?auto=format&fit=crop&w=1920&q=80" 
                 alt="Camping Alam Bebas Campify" 
                 class="w-full h-full object-cover object-center brightness-90">
            
            {{-- 1. Top Vignette: Menjamin Navbar Transparan Terbaca Sempurna Tanpa Tabrakan --}}
            <div class="absolute inset-x-0 top-0 h-56 bg-gradient-to-b from-black/85 via-black/40 to-transparent pointer-events-none"></div>

            {{-- 2. Left High-Contrast Gradient: Menjamin Seluruh Teks Terbaca Sangat Jelas --}}
            <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/60 to-transparent"></div>

            {{-- 3. Bottom Soft Gradient: Transisi Halus ke Section Statistik --}}
            <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-slate-950/90 to-transparent pointer-events-none"></div>
        </div>

        {{-- Hero Content: Diberi Padding Top Pasti (195px) agar Berada Nyaman di Bawah Navbar --}}
        <div class="landing-hero-content-wrapper relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full" style="padding-top: 195px !important; padding-bottom: 80px !important;">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-12">
                
                {{-- Kolom Kiri: Teks & Aksi Utama --}}
                <div class="max-w-2xl">
                    {{-- Badge Tagline --}}
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full text-xs sm:text-sm font-bold mb-6 shadow-md"
                         style="background: rgba(16, 185, 129, 0.25) !important; color: #34d399 !important; border: 1px solid rgba(52, 211, 153, 0.45) !important; backdrop-filter: blur(10px);">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Platform Sewa & Jual Alat Camping #1 di Indonesia</span>
                    </div>

                    {{-- Headline --}}
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.15] mb-5 drop-shadow-2xl" style="color: #ffffff !important;">
                        Petualangan Nyaman, <br>
                        <span style="color: #34d399 !important;" class="drop-shadow-md">Tanpa Beban.</span>
                    </h1>

                    {{-- Subheadline (100% Kontras Tinggi dengan Teks Putih Terang) --}}
                    <p class="landing-hero-desc text-base sm:text-lg leading-relaxed mb-8 font-normal drop-shadow-md" style="color: #f8fafc !important;">
                        Lengkapi persiapan mendaki dan campingmu dengan perlengkapan terbaik. <span class="font-extrabold underline decoration-emerald-400 decoration-2 underline-offset-4" style="color: #ffffff !important;">Sewa hemat harian</span> bergaransi proteksi <span class="font-extrabold underline decoration-emerald-400 decoration-2 underline-offset-4" style="color: #6ee7b7 !important;">escrow</span>, atau <span class="font-extrabold underline decoration-emerald-400 decoration-2 underline-offset-4" style="color: #ffffff !important;">beli perlengkapan original</span> dari mitra toko terpercaya.
                    </p>

                    {{-- Dual CTA Buttons (Clean, Spacious & Rapi) --}}
                    <div class="flex flex-wrap items-center gap-4 mb-4">
                        <a href="{{ route('produk.rental') }}" 
                           class="inline-flex items-center justify-center gap-2.5 px-8 py-4 rounded-full font-bold text-sm sm:text-base shadow-xl transition-all hover:scale-105 active:scale-95"
                           style="background: #10b981 !important; color: #ffffff !important; box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.45) !important;">
                            <i data-lucide="tent" class="w-5 h-5"></i>
                            <span>Mulai Sewa Gear</span>
                        </a>

                        <a href="{{ route('produk.index') }}" 
                           class="inline-flex items-center justify-center gap-2.5 px-8 py-4 rounded-full font-bold text-sm sm:text-base transition-all hover:scale-105 active:scale-95 shadow-lg"
                           style="background: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.35) !important; backdrop-filter: blur(10px);">
                            <i data-lucide="shopping-bag" class="w-5 h-5" style="color: #6ee7b7 !important;"></i>
                            <span>Beli Perlengkapan</span>
                        </a>
                    </div>

                    {{-- Dedicated Mobile App Announcement Pill (Tidak Nabrak & Rapi) --}}
                    <div class="mb-9">
                        <div class="inline-flex flex-wrap items-center gap-2 sm:gap-2.5 px-4 py-2 rounded-full border shadow-sm text-xs"
                             style="background: rgba(7, 26, 20, 0.85); border: 1px solid rgba(52, 211, 153, 0.35); backdrop-filter: blur(12px);">
                            <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="font-medium text-slate-300">Aplikasi Android:</span>
                            <a href="https://drive.usercontent.google.com/download?id=1iQlWXE5wLJI-0OJv83ioRvMOK3ms1xKp&export=download" 
                               target="_blank"
                               rel="noopener noreferrer"
                               title="Download APK Campify untuk Android"
                               class="font-bold inline-flex items-center gap-1.5 transition-colors underline decoration-emerald-400 decoration-1 underline-offset-4"
                               style="color: #a7f3d0 !important;">
                                <i data-lucide="smartphone" class="w-3.5 h-3.5 text-emerald-400"></i>
                                <span>Download APK (v1.0)</span>
                                <i data-lucide="download" class="w-3 h-3 text-emerald-400"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Trust Pillars Strip (Glass Cards Rapi Samping-ke-Samping, 100% Kontras Tinggi) --}}
                    <div class="grid grid-cols-3 gap-2 sm:gap-3.5 pt-6 border-t border-white/20 max-w-2xl">
                        <div class="flex items-center gap-2 sm:gap-3 rounded-2xl p-2.5 sm:p-3.5 shadow-xl transition-all"
                             style="background: rgba(10, 25, 20, 0.75); border: 1px solid rgba(255, 255, 255, 0.2); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0"
                                 style="background: rgba(16, 185, 129, 0.25); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.4);">
                                <i data-lucide="shield-check" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs sm:text-sm font-black leading-tight truncate" style="color: #ffffff !important;">Escrow Aman</div>
                                <div class="text-[9px] sm:text-[11px] font-semibold mt-0.5 truncate" style="color: #a7f3d0 !important;">Jaminan kembali 100%</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 sm:gap-3 rounded-2xl p-2.5 sm:p-3.5 shadow-xl transition-all"
                             style="background: rgba(10, 25, 20, 0.75); border: 1px solid rgba(255, 255, 255, 0.2); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0"
                                 style="background: rgba(16, 185, 129, 0.25); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.4);">
                                <i data-lucide="sparkles" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs sm:text-sm font-black leading-tight truncate" style="color: #ffffff !important;">Gear Terawat</div>
                                <div class="text-[9px] sm:text-[11px] font-semibold mt-0.5 truncate" style="color: #a7f3d0 !important;">Bersih & siap pakai</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 sm:gap-3 rounded-2xl p-2.5 sm:p-3.5 shadow-xl transition-all"
                             style="background: rgba(10, 25, 20, 0.75); border: 1px solid rgba(255, 255, 255, 0.2); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0"
                                 style="background: rgba(16, 185, 129, 0.25); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.4);">
                                <i data-lucide="map-pin" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs sm:text-sm font-black leading-tight truncate" style="color: #ffffff !important;">Toko Resmi</div>
                                <div class="text-[9px] sm:text-[11px] font-semibold mt-0.5 truncate" style="color: #a7f3d0 !important;">Mitra terverifikasi</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Preview Card Outdoor Estetik (Mirip Dashboard Pembeli) --}}
                <div class="hidden lg:flex flex-col items-center justify-center max-w-sm w-full">
                    <div class="relative w-full rounded-3xl overflow-hidden border border-white/20 bg-white/10 backdrop-blur-xl p-4 shadow-2xl shadow-black/40 group hover:border-emerald-400/50 transition-all duration-500">
                        <div class="relative h-64 rounded-2xl overflow-hidden mb-4">
                            <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=760&q=80" 
                                 alt="Gunung & Hutan Campify" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                            <span class="absolute top-3 left-3 bg-emerald-600/90 backdrop-blur-md text-white text-[11px] font-bold px-3 py-1 rounded-full shadow-md flex items-center gap-1.5 border border-emerald-400/30">
                                <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                                Gear Siap Pakai
                            </span>
                            <div class="absolute bottom-3 left-3 right-3 text-white">
                                <p class="text-xs font-semibold text-emerald-300 uppercase tracking-wider">Penyewaan & Pembelian</p>
                                <p class="text-sm font-extrabold truncate">Paket Tenda Dome & Carrier 60L</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-white px-2 pb-1">
                            <div>
                                <p class="text-[11px] text-slate-300">Mulai dari</p>
                                <p class="text-base font-black text-emerald-400">Rp 25.000 <span class="text-xs font-normal text-slate-300">/hari</span></p>
                            </div>
                            <a href="{{ route('produk.rental') }}" class="px-4 py-2 rounded-xl bg-white/20 hover:bg-emerald-600 text-white text-xs font-bold transition-all border border-white/20 shadow-sm flex items-center gap-1">
                                <span>Lihat Gear</span>
                                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ==================== 2. STATS STRIP ==================== --}}
    <section class="bg-white border-b border-slate-100 py-6 relative z-10 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center divide-y md:divide-y-0 md:divide-x divide-slate-100">
                <div class="pt-2 md:pt-0">
                    <div class="text-3xl font-black text-slate-900 tracking-tight">
                        {{ max($totalProductsCount ?? 0, 50) }}<span class="text-emerald-600">+</span>
                    </div>
                    <div class="text-xs text-slate-500 mt-1 font-medium">Peralatan Outdoor Tersedia</div>
                </div>

                <div class="pt-4 md:pt-0">
                    <div class="text-3xl font-black text-slate-900 tracking-tight">
                        {{ max($totalStoresCount ?? 0, 10) }}<span class="text-emerald-600">+</span>
                    </div>
                    <div class="text-xs text-slate-500 mt-1 font-medium">Mitra Toko & Rental Resmi</div>
                </div>

                <div class="pt-4 md:pt-0">
                    <div class="text-3xl font-black text-emerald-600 tracking-tight">
                        100%
                    </div>
                    <div class="text-xs text-slate-500 mt-1 font-medium">Garansi Deposit Escrow Aman</div>
                </div>

                <div class="pt-4 md:pt-0">
                    <div class="text-3xl font-black text-slate-900 tracking-tight flex items-center justify-center gap-1">
                        <span>4.9</span>
                        <span class="text-amber-400 text-xl">★</span>
                    </div>
                    <div class="text-xs text-slate-500 mt-1 font-medium">Rating Kepuasan Petualang</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== 3. DUAL EXPERIENCE (Sewa vs Beli) ==================== --}}
    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3.5 py-1.5 rounded-full inline-block mb-3 border border-emerald-100">
                    DUA CARA PETUALANGAN
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Sewa Hemat atau Beli Permanen?
                </h2>
                <p class="mt-3 text-sm sm:text-base text-slate-500">
                    Pilih opsi yang paling pas dengan jadwal camping dan kebutuhan petualanganmu.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                {{-- CARD SEWA --}}
                <div class="bg-white rounded-3xl border border-emerald-100 p-8 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold mb-4">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                            <span>OPSI PALING HEMAT & FLEKSIBEL</span>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 mb-2">Sewa Alat Camping</h3>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            Pilihan cerdas untuk pendakian musiman, camping akhir pekan, atau acara kelompok. Nikmati gear terbaik tanpa beban beli mahal.
                        </p>

                        <ul class="space-y-3 mb-8 text-sm text-slate-600">
                            <li class="flex items-start gap-3">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span><strong>Bayar harian hemat</strong> sesuai lama waktu camping</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span>Tanpa repot mencuci, merawat, dan menyimpan gear di rumah</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span><strong>Dana jaminan (escrow)</strong> aman dan dikembalikan utuh</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span>Kondisi gear teruji, bersih, wangi, dan siap langsung pakai</span>
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('produk.rental') }}" 
                       class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-100 transition-all">
                        <span>Lihat Katalog Sewa</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>

                {{-- CARD BELI --}}
                <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold mb-4">
                            <i data-lucide="award" class="w-3.5 h-3.5"></i>
                            <span>INVESTASI HOBI PETUALANG</span>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 mb-2">Beli Perlengkapan Baru</h3>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            Khusus untuk kamu yang rutin mendaki setiap bulan atau hobi solo camping. Miliki gear impian original milik sendiri.
                        </p>

                        <ul class="space-y-3 mb-8 text-sm text-slate-600">
                            <li class="flex items-start gap-3">
                                <span class="text-slate-700 font-bold">✓</span>
                                <span><strong>100% Produk Original</strong> dari brand outdoor terkemuka</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-slate-700 font-bold">✓</span>
                                <span>Siap digunakan kapan pun tanpa batas durasi penyewaan</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-slate-700 font-bold">✓</span>
                                <span>Bebas kustomisasi dan penyesuaian gear sesukamu</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-slate-700 font-bold">✓</span>
                                <span>Pengiriman aman ke seluruh Indonesia dengan proteksi retur</span>
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('produk.index') }}" 
                       class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm shadow-md transition-all">
                        <span>Beli Perlengkapan Baru</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== 4. KEUNGGULAN UTAMA ==================== --}}
    <section class="py-16 bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3.5 py-1.5 rounded-full inline-block mb-3 border border-emerald-100">
                    KEUNGGULAN UTAMA
                </span>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    Mengapa Memilih Campify?
                </h2>
                <p class="mt-2 text-sm text-slate-500">
                    Solusi sewa dan jual beli alat outdoor yang transparan, aman, dan tanpa rasa cemas.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-4">
                        <i data-lucide="shield-check" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 mb-1.5">Proteksi Dana Escrow</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Dana deposit sewa disimpan aman di sistem penjamin dan otomatis dicairkan kembali saat barang kembali dalam kondisi baik.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-4">
                        <i data-lucide="sparkles" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 mb-1.5">Bersih & Higienis</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Semua tenda, sleeping bag, dan alat masak dipastikan telah dicuci bersih dan diinspeksi fungsinya sebelum diserahkan.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-4">
                        <i data-lucide="map-pin" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 mb-1.5">Toko Rental Terdekat</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Temukan mitra rental di kotamu untuk kemudahan pickup langsung atau pengiriman hemat biaya via kurir lokal.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-4">
                        <i data-lucide="box" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 mb-1.5">Stok Real-Time</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Ketersediaan unit tampil transparan dan akurat. Tidak ada lagi drama kehabisan perlengkapan di hari keberangkatan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== 5. KATEGORI POPULER ==================== --}}
    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3.5 py-1.5 rounded-full inline-block mb-3 border border-emerald-100">
                        KATALOG PERLENGKAPAN
                    </span>
                    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                        Eksplorasi Kategori Perlengkapan
                    </h2>
                </div>
                <a href="{{ route('produk.index') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-emerald-600 hover:text-emerald-700 transition">
                    <span>Lihat Semua Produk</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredCategories ?? [] as $cat)
                <a href="{{ route('produk.category', $cat['key']) }}" 
                   class="group relative overflow-hidden rounded-3xl h-64 border border-slate-200/80 shadow-sm hover:shadow-lg transition-all duration-300">
                    <img src="{{ $cat['image'] }}" 
                         alt="{{ $cat['name'] }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-900/40 to-transparent"></div>

                    <div class="absolute inset-0 p-6 flex flex-col justify-end">
                        <div class="flex items-center justify-between mb-1.5">
                            <h4 class="text-xl font-bold text-white group-hover:text-emerald-300 transition-colors">
                                {{ $cat['name'] }}
                            </h4>
                            <div class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white group-hover:bg-emerald-600 transition-colors">
                                <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <p class="text-xs text-slate-300 line-clamp-2 leading-relaxed">
                            {{ $cat['desc'] }}
                        </p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ==================== 6. CARA KERJA (3 LANGKAH) ==================== --}}
    <section class="py-20 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3.5 py-1.5 rounded-full inline-block mb-3 border border-emerald-100">
                    ALUR MUDAH
                </span>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    3 Langkah Sewa Alat Camping
                </h2>
                <p class="mt-2 text-sm text-slate-500">
                    Praktis dan cepat dari ponsel sebelum jadwal campingmu tiba.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 text-center flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-600 text-white font-black text-xl flex items-center justify-center mb-5 shadow-md shadow-emerald-100">
                        1
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Pilih Gear & Durasi</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Tentukan tenda, carrier, atau alat masak yang kamu butuhkan. Atur tanggal mulai dan durasi sewa harian.
                    </p>
                </div>

                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 text-center flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-600 text-white font-black text-xl flex items-center justify-center mb-5 shadow-md shadow-emerald-100">
                        2
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Titip Jaminan Escrow</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Bayar sewa dan titip uang deposit jaminan dengan aman melalui sistem penjamin Campify. Toko menyiapkan gear pesananmu.
                    </p>
                </div>

                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 text-center flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-600 text-white font-black text-xl flex items-center justify-center mb-5 shadow-md shadow-emerald-100">
                        3
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Kembalikan & Jaminan Cair</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Nikmati alam bebas! Setelah camping selesai, kembalikan barang dan uang depositmu langsung dikembalikan utuh 100%.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== 7. PRODUK PILIHAN ==================== --}}
    @php
        $highlightedProducts = $rentalProducts->take(2)->concat($buyProducts->take(2));
    @endphp
    @if($highlightedProducts->isNotEmpty())
    <section class="py-20 bg-slate-50 border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3.5 py-1.5 rounded-full inline-block mb-3 border border-emerald-100">
                        PRODUK PILIHAN
                    </span>
                    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                        Gear Siap Menemani Petualanganmu
                    </h2>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('produk.rental') }}" class="px-4 py-2 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold hover:bg-emerald-100 transition border border-emerald-200">
                        Katalog Sewa
                    </a>
                    <a href="{{ route('produk.index') }}" class="px-4 py-2 rounded-xl bg-white text-slate-700 text-xs font-bold hover:bg-slate-100 transition border border-slate-200">
                        Katalog Beli
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($highlightedProducts as $product)
                    @include('components.product-card', [
                        'product' => $product,
                        'isWishlisted' => false
                    ])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ==================== 8. SELLER CALLOUT ==================== --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl p-8 sm:p-12 lg:p-14 relative overflow-hidden shadow-2xl" 
                 style="background: linear-gradient(135deg, #071a14 0%, #0e2e24 50%, #064e3b 100%); border: 1px solid rgba(16, 185, 129, 0.35); box-shadow: 0 25px 50px -12px rgba(6, 78, 59, 0.4);">
                
                {{-- Decorative Ambient Glow --}}
                <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full pointer-events-none" style="background: radial-gradient(circle, rgba(16, 185, 129, 0.3) 0%, transparent 70%);"></div>

                <div class="max-w-2xl relative z-10">
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider mb-4 shadow-sm"
                          style="background: rgba(16, 185, 129, 0.25); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.4);">
                        <i data-lucide="store" class="w-4 h-4"></i>
                        <span>MITRA PENJUAL & RENTAL</span>
                    </span>

                    <h2 class="text-2xl sm:text-4xl font-black tracking-tight mb-4" style="color: #ffffff !important; line-height: 1.2;">
                        Punya Alat Camping yang Menganggur di Rumah?
                    </h2>
                    
                    <p class="text-sm sm:text-base leading-relaxed mb-8" style="color: #e2e8f0 !important; font-weight: 400;">
                        Buka toko rentalmu sendiri di Campify! Jangkau ratusan pendaki di kotamu. Nikmati sistem escrow anti-rugi, verifikasi KTP penyewa, dan pencairan dana langsung ke rekeningmu.
                    </p>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                        <a href="{{ route('register') }}" 
                           class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-full font-bold text-sm sm:text-base shadow-xl transition-all hover:scale-105 active:scale-95"
                           style="background: #10b981; color: #ffffff; box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.5);">
                            <span>Buka Toko Gratis di Campify</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                        <span class="text-xs sm:text-sm text-center sm:text-left font-semibold" style="color: #a7f3d0 !important;">
                            ✓ Pendaftaran instan tanpa biaya langganan
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== 9. MOBILE APP SHOWCASE & DOWNLOAD APK ==================== --}}
    <section id="download-app" class="py-20 relative overflow-hidden" style="background-color: #071913 !important; color: #ffffff !important;">
        {{-- Decorative Glow Elements --}}
        <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full blur-3xl pointer-events-none" style="background: rgba(16, 185, 129, 0.18);"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full blur-3xl pointer-events-none" style="background: rgba(5, 150, 105, 0.2);"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                {{-- Left Column: Information & Actions --}}
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold shadow-sm"
                         style="background: rgba(16, 185, 129, 0.2) !important; color: #34d399 !important; border: 1px solid rgba(52, 211, 153, 0.35) !important;">
                        <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>APLIKASI ANDROID (APK RESMI)</span>
                    </div>

                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight" style="color: #ffffff !important;">
                        Petualangan Nyaman, <br class="hidden sm:inline">
                        <span style="color: #34d399 !important;">
                            Kini Hadir di Genggaman.
                        </span>
                    </h2>

                    <p class="text-sm sm:text-base leading-relaxed max-w-xl mx-auto lg:mx-0 font-normal" style="color: #cbd5e1 !important;">
                        Nikmati kemudahan sewa gear camping, pantau jadwal rental, cek ketersediaan stok perlengkapan outdoor di kotamu, dan lakukan pembayaran dengan aman langsung dari smartphone Android Anda.
                    </p>

                    {{-- Feature Highlights --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 pt-2 max-w-xl mx-auto lg:mx-0 text-left">
                        <div class="p-4 rounded-2xl border" style="background: #0d2820 !important; border-color: rgba(52, 211, 153, 0.25) !important;">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold mb-2.5" style="background: rgba(16, 185, 129, 0.25) !important; color: #34d399 !important;">
                                <i data-lucide="zap" class="w-4 h-4"></i>
                            </div>
                            <div class="text-xs font-bold" style="color: #ffffff !important;">Booking Cepat</div>
                            <div class="text-[11px] mt-0.5" style="color: #94a3b8 !important;">Sewa & beli tanpa ribet</div>
                        </div>

                        <div class="p-4 rounded-2xl border" style="background: #0d2820 !important; border-color: rgba(52, 211, 153, 0.25) !important;">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold mb-2.5" style="background: rgba(16, 185, 129, 0.25) !important; color: #34d399 !important;">
                                <i data-lucide="bell" class="w-4 h-4"></i>
                            </div>
                            <div class="text-xs font-bold" style="color: #ffffff !important;">Notifikasi Real-time</div>
                            <div class="text-[11px] mt-0.5" style="color: #94a3b8 !important;">Pengingat masa sewa aktif</div>
                        </div>

                        <div class="p-4 rounded-2xl border" style="background: #0d2820 !important; border-color: rgba(52, 211, 153, 0.25) !important;">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold mb-2.5" style="background: rgba(16, 185, 129, 0.25) !important; color: #34d399 !important;">
                                <i data-lucide="shield-check" class="w-4 h-4"></i>
                            </div>
                            <div class="text-xs font-bold" style="color: #ffffff !important;">Proteksi Escrow</div>
                            <div class="text-[11px] mt-0.5" style="color: #94a3b8 !important;">Deposit jaminan aman 100%</div>
                        </div>
                    </div>

                    {{-- Download & QR Code Actions --}}
                    <div class="pt-4 flex flex-col sm:flex-row items-center gap-5 justify-center lg:justify-start">
                        <div class="space-y-2.5 w-full sm:w-auto text-center sm:text-left">
                            {{-- Primary Direct Download Button --}}
                            <a href="https://drive.usercontent.google.com/download?id=1iQlWXE5wLJI-0OJv83ioRvMOK3ms1xKp&export=download" 
                               target="_blank"
                               rel="noopener noreferrer"
                               class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-full font-extrabold text-base shadow-xl transition-all hover:scale-105 active:scale-95"
                               style="background: #10b981 !important; color: #ffffff !important; box-shadow: 0 12px 25px -5px rgba(16, 185, 129, 0.5) !important;">
                                <i data-lucide="download" class="w-5 h-5"></i>
                                <span>Download APK Android</span>
                            </a>
                            
                            {{-- Backup Google Drive Link --}}
                            <div class="text-xs" style="color: #94a3b8 !important;">
                                <span>Kesulitan unduh langsung?</span>
                                <a href="https://drive.google.com/file/d/1iQlWXE5wLJI-0OJv83ioRvMOK3ms1xKp/view?usp=sharing" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   class="font-bold hover:underline ml-1"
                                   style="color: #34d399 !important;">
                                    Buka via Google Drive ↗
                                </a>
                            </div>
                        </div>

                        {{-- Scan QR Code to Download on Mobile Device --}}
                        <div class="hidden sm:flex items-center gap-3.5 p-3 rounded-2xl border"
                             style="background: #0d2820 !important; border-color: rgba(52, 211, 153, 0.25) !important;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=https%3A%2F%2Fdrive.google.com%2Ffile%2Fd%2F1iQlWXE5wLJI-0OJv83ioRvMOK3ms1xKp%2Fview%3Fusp%3Dsharing" 
                                 alt="QR Code Download APK Campify" 
                                 class="w-16 h-16 rounded-xl bg-white p-1 shrink-0">
                            <div class="text-left">
                                <div class="text-xs font-bold" style="color: #ffffff !important;">Scan Pakai HP</div>
                                <div class="text-[10px] max-w-[120px] leading-tight mt-0.5" style="color: #94a3b8 !important;">Arahkan kamera HP Anda untuk download langsung</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Modern Smartphone Mockup --}}
                <div class="lg:col-span-5 flex justify-center relative">
                    <div class="relative w-64 sm:w-72 rounded-[42px] p-3 shadow-2xl border-2"
                         style="background: #0b1a15 !important; border-color: #1e3a30 !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7) !important;">
                        
                        {{-- Phone Notch / Speaker --}}
                        <div class="w-24 h-3.5 rounded-full mx-auto mb-3 flex items-center justify-center gap-1.5"
                             style="background: #06110e !important;">
                            <span class="w-2 h-2 rounded-full" style="background: #142821 !important;"></span>
                            <span class="w-6 h-1 rounded-full" style="background: #142821 !important;"></span>
                        </div>

                        {{-- Phone Screen Mockup Content --}}
                        <div class="rounded-[30px] p-4 text-left space-y-3.5 border"
                             style="background: #05120d !important; border-color: #163328 !important;">
                            
                            {{-- Mockup App Header --}}
                            <div class="flex items-center justify-between border-b pb-3" style="border-color: #142e24 !important;">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg text-white flex items-center justify-center font-black text-sm"
                                         style="background: #10b981 !important;">C</div>
                                    <span class="font-black text-sm" style="color: #ffffff !important;">Campify</span>
                                </div>
                                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full"
                                      style="background: rgba(16, 185, 129, 0.2) !important; color: #34d399 !important; border: 1px solid rgba(52, 211, 153, 0.35) !important;">v1.0 APK</span>
                            </div>

                            {{-- Mockup Search Card --}}
                            <div class="p-2.5 rounded-xl flex items-center gap-2 text-xs border"
                                 style="background: #0a1f18 !important; border-color: #17382c !important; color: #94a3b8 !important;">
                                <i data-lucide="search" class="w-3.5 h-3.5" style="color: #34d399 !important;"></i>
                                <span>Cari tenda dome, carrier...</span>
                            </div>

                            {{-- Mockup Mini Product Item 1 --}}
                            <div class="p-2.5 rounded-2xl flex items-center gap-3 border"
                                 style="background: #0c241c !important; border-color: #183e30 !important;">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl shrink-0"
                                     style="background: #133529 !important;">
                                    🏕️
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-xs font-bold truncate" style="color: #ffffff !important;">Tenda Dome 4P Pro</div>
                                    <div class="text-[11px] font-bold mt-0.5" style="color: #34d399 !important;">Rp 45.000 <span class="text-[9px] font-normal" style="color: #94a3b8 !important;">/hari</span></div>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-md font-bold"
                                      style="background: rgba(16, 185, 129, 0.2) !important; color: #6ee7b7 !important;">Sewa</span>
                            </div>

                            {{-- Mockup Mini Product Item 2 --}}
                            <div class="p-2.5 rounded-2xl flex items-center gap-3 border"
                                 style="background: #0c241c !important; border-color: #183e30 !important;">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl shrink-0"
                                     style="background: #133529 !important;">
                                    🎒
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-xs font-bold truncate" style="color: #ffffff !important;">Carrier 60L Ultralight</div>
                                    <div class="text-[11px] font-bold mt-0.5" style="color: #34d399 !important;">Rp 35.000 <span class="text-[9px] font-normal" style="color: #94a3b8 !important;">/hari</span></div>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-md font-bold"
                                      style="background: rgba(16, 185, 129, 0.2) !important; color: #6ee7b7 !important;">Sewa</span>
                            </div>

                            {{-- Mockup Escrow Badge --}}
                            <div class="p-2.5 rounded-xl border flex items-center gap-2.5"
                                 style="background: rgba(16, 185, 129, 0.12) !important; border-color: rgba(52, 211, 153, 0.3) !important;">
                                <i data-lucide="shield-check" class="w-4 h-4 shrink-0" style="color: #34d399 !important;"></i>
                                <div class="text-[10px] leading-tight" style="color: #a7f3d0 !important;">
                                    <strong class="font-bold" style="color: #ffffff !important;">Proteksi Escrow Aktif</strong>
                                    - Deposit jaminan aman 100%
                                </div>
                            </div>
                        </div>

                        {{-- Floating Badges --}}
                        <div class="mt-3 py-2 px-3 rounded-2xl border flex items-center justify-center gap-2 text-xs"
                             style="background: #0d2820 !important; border-color: rgba(52, 211, 153, 0.3) !important;">
                            <span class="text-emerald-400 font-bold">⚡ Ringan & Mudah:</span>
                            <span class="text-slate-300">Tinggal pasang di Android</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ==================== 10. FAQ SECTION ==================== --}}
    <section class="py-16 bg-slate-50 border-t border-slate-100">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3.5 py-1.5 rounded-full inline-block mb-3 border border-emerald-100">
                    TANYA JAWAB
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Pertanyaan yang Sering Diajukan
                </h2>
            </div>

            <div class="space-y-4">
                <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
                    <h4 class="text-base font-bold text-slate-900 mb-2 flex items-center justify-between">
                        <span>Bagaimana cara menginstall aplikasi Campify dari file APK?</span>
                        <i data-lucide="help-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                    </h4>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Setelah mendownload file APK dari tombol di atas, buka file tersebut di ponsel Android Anda. Jika muncul pemberitahuan keamanan saat instalasi pertama kali, pilih <strong>"Izinkan instalasi dari sumber ini / sumber tidak dikenal" (Allow from unknown sources)</strong> di pengaturan ponsel Anda, lalu lanjutkan instalasi hingga selesai.
                    </p>
                </div>
                <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
                    <h4 class="text-base font-bold text-slate-900 mb-2 flex items-center justify-between">
                        <span>Apa itu uang jaminan (escrow) saat menyewa alat?</span>
                        <i data-lucide="help-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                    </h4>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Uang jaminan (escrow deposit) adalah dana titipan keamanan yang disetor penyewa untuk menjamin keutuhan barang rental. Uang ini disimpan aman di sistem rekening bersama Campify dan akan dikembalikan 100% setelah barang dikembalikan dalam kondisi baik.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
                    <h4 class="text-base font-bold text-slate-900 mb-2 flex items-center justify-between">
                        <span>Apakah saya perlu memverifikasi KTP untuk menyewa alat?</span>
                        <i data-lucide="help-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                    </h4>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Ya. Demi menjaga keamanan bersama dan melindungi toko mitra, penyewa wajib mengunggah verifikasi KTP sebelum pesanan sewa diproses.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
                    <h4 class="text-base font-bold text-slate-900 mb-2 flex items-center justify-between">
                        <span>Bagaimana jika barang yang saya sewa mengalami kerusakan atau telat?</span>
                        <i data-lucide="help-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                    </h4>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Biaya perbaikan atau denda keterlambatan dihitung secara transparan dan dipotong langsung dari deposit jaminan. Sisa dana jaminan akan langsung dicairkan ke rekeningmu.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== 10. FINAL BOTTOM CALL TO ACTION ==================== --}}
    <section class="py-16 bg-slate-900 text-center relative overflow-hidden">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight mb-4">
                Siap Memulai Petualanganmu?
            </h2>
            <p class="text-slate-300 text-sm sm:text-base mb-8 max-w-lg mx-auto">
                Ratusan perlengkapan camping berkualitas menantimu. Sewa hemat sekarang atau lengkapi perlengkapan impianmu.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('produk.rental') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-lg shadow-emerald-900/40 transition hover:scale-105 active:scale-95">
                    Jelajahi Alat Sewa 🏕️
                </a>
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-sm transition hover:scale-105 active:scale-95">
                    Masuk ke Akun
                </a>
            </div>
        </div>
    </section>

</div>
@endsection
