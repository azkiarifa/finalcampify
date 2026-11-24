@extends('layouts.app_pembeli')

@section('content')
<div class="pt-8 pb-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4">

        <div class="mb-6 flex items-center justify-between gap-4">
            <a href="{{ route('produk.rental') }}" class="text-sm text-green-600 hover:text-green-800 font-semibold">← Kembali ke Produk Sewa</a>
            <div class="flex items-center gap-3 text-sm text-gray-500">
                <span>{{ $produk->category }}</span>
                <span>•</span>
                <span class="{{ ($produk->stock ?? 0) > 0 ? 'text-emerald-600 font-semibold' : 'text-red-500 font-semibold' }}">
                    {{ ($produk->stock ?? 0) > 0 ? 'Stok Sewa Tersedia: ' . $produk->stock . ' unit' : 'Stok Sewa Habis' }}
                </span>
            </div>
        </div>

        <div class="grid gap-10 xl:grid-cols-[1.5fr_1fr]">

            <div class="space-y-6">
                <div class="rounded-[32px] overflow-hidden shadow-lg bg-white">
                    @if($produk->image_url)
                        <img src="{{ $produk->image_url }}"
                             alt="{{ $produk->name ?? $produk->nama_produk }}"
                             class="w-full h-[520px] object-contain p-4 bg-slate-50">
                    @else
                        <div class="w-full h-[520px] bg-slate-100 flex items-center justify-center text-6xl">📦</div>
                    @endif
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="bg-white rounded-3xl p-6 shadow-sm">
                        <h2 class="text-xl font-bold mb-4">Detail Produk</h2>
                        <p class="text-sm leading-7 text-slate-600">{{ $produk->description }}</p>
                    </div>
                    <div class="bg-white rounded-3xl p-6 shadow-sm">
                        <h2 class="text-xl font-bold mb-4">Rating & Review Produk</h2>
                        <div class="flex items-center gap-6 mb-8 p-4 bg-slate-50 rounded-2xl">
                            <div>
                                <p class="text-5xl font-black text-slate-900">{{ number_format($produk->productRatings->avg('rating') ?: 0, 1) }}</p>
                                <p class="text-xs text-slate-500 mt-1">dari 5.0</p>
                            </div>
                            <div class="flex-1">
                                <div class="flex text-yellow-400 text-lg mb-1">
                                    @for($i=1; $i<=5; $i++)
                                        <span>{!! $i <= round($produk->productRatings->avg('rating') ?: 0) ? '★' : '☆' !!}</span>
                                    @endfor
                                </div>
                                <p class="text-xs font-bold text-slate-600 uppercase tracking-wider">{{ $produk->productRatings->count() }} Ulasan Pelanggan</p>
                            </div>
                        </div>

                        <!-- LIST REVIEW PRODUK -->
                        <div class="space-y-6 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                            @forelse($produk->productRatings as $rating)
                                <div class="border-b border-slate-100 pb-4 last:border-0">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-xs font-bold text-emerald-700">
                                                {{ substr($rating->user->name ?? 'U', 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-800">{{ $rating->user->name ?? 'Anonim' }}</p>
                                                <div class="flex text-yellow-400 text-[10px]">
                                                    @for($i=1; $i<=5; $i++)
                                                        <span>{!! $i <= $rating->rating ? '★' : '☆' !!}</span>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-[10px] text-slate-400">{{ $rating->created_at->format('d M Y') }}</span>
                                    </div>
                                    <p class="text-sm text-slate-600 italic">"{{ $rating->comment }}"</p>
                                    @if(!empty($rating->reply))
                                        <div class="mt-3 p-3 bg-emerald-50/70 border border-emerald-100 rounded-xl text-xs">
                                            <div class="flex items-center gap-1.5 font-bold text-emerald-800 mb-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                                <span>Respon Penjual ({{ $produk->store?->nama_toko ?? 'Toko' }}):</span>
                                            </div>
                                            <p class="text-slate-700 leading-relaxed">{{ $rating->reply }}</p>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-slate-400 text-center py-4">Belum ada ulasan untuk produk ini.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- RATING TOKO -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm">
                        <h2 class="text-xl font-bold mb-4">Rating & Reputasi Toko</h2>
                        @php
                            $avgStoreRating = $storeRatings->avg('rating') ?? 0;
                            $totalStoreReviews = $storeRatings->count();
                        @endphp
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-16 h-16 rounded-2xl bg-emerald-600 flex items-center justify-center text-2xl shadow-lg shadow-emerald-100">
                                🏪
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800">{{ $produk->store?->nama_toko ?? 'Toko Penjual' }}</h3>
                                <div class="flex items-center gap-2">
                                    <div class="flex text-yellow-400 text-sm">
                                        @for($i=1; $i<=5; $i++)
                                            <span>{!! $i <= round($avgStoreRating) ? '★' : '☆' !!}</span>
                                        @endfor
                                    </div>
                                    <span class="text-xs font-bold text-slate-500">{{ number_format($avgStoreRating, 1) }} ({{ $totalStoreReviews }} Rating)</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                            @forelse($storeRatings->take(5) as $sRating)
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <div class="flex justify-between items-center mb-1">
                                        <p class="text-[10px] font-bold text-slate-700">{{ $sRating->user->name ?? 'Pembeli' }}</p>
                                        <div class="flex text-yellow-400 text-[8px]">
                                            @for($i=1; $i<=5; $i++)
                                                <span>{!! $i <= $sRating->rating ? '★' : '☆' !!}</span>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-500 line-clamp-2">"{{ $sRating->comment }}"</p>
                                    @if(!empty($sRating->reply))
                                        <div class="mt-2 p-2 bg-emerald-50/70 border border-emerald-100 rounded-lg text-[11px]">
                                            <p class="font-bold text-emerald-800 mb-0.5">Respon Toko:</p>
                                            <p class="text-slate-700">{{ $sRating->reply }}</p>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 text-center py-2">Toko belum memiliki ulasan.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <div class="flex items-start justify-between gap-4 mb-6">
                        <div class="flex-1 min-w-0 pr-2">
                            <span class="inline-block text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md mb-2">
                                Detail Penyewaan
                            </span>
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-snug break-words">
                                {{ $produk->name }}
                            </h1>
                            <div class="flex items-center gap-1.5 text-sm text-slate-500 mt-2">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span>Penjual:</span>
                                <span class="font-semibold text-slate-700 truncate">{{ $produk->store?->nama_toko ?? $produk->seller?->name ?? $produk->owner?->name ?? 'Toko Resmi' }}</span>
                            </div>
                        </div>

                        <!-- Action Buttons: Favorit & Chat -->
                        <div class="flex items-center gap-2 flex-shrink-0 pt-0.5">
                            @auth
                                <form action="{{ route('wishlist.toggle') }}" method="POST" class="inline-flex m-0">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $produk->id }}">
                                    <button type="submit" 
                                            class="group inline-flex h-11 w-11 items-center justify-center rounded-2xl border transition-all duration-200
                                            {{ in_array($produk->id, $wishlistProductIds ?? []) 
                                                ? 'bg-rose-50 border-rose-200 text-rose-500 shadow-sm hover:bg-rose-100 hover:border-rose-300' 
                                                : 'bg-white border-slate-200 text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50/50 shadow-sm' }}"
                                            title="{{ in_array($produk->id, $wishlistProductIds ?? []) ? 'Hapus dari Favorit' : 'Tambah ke Favorit' }}">
                                        @if(in_array($produk->id, $wishlistProductIds ?? []))
                                            <svg class="w-5 h-5 fill-current text-rose-500 transition-transform duration-200 group-hover:scale-110" viewBox="0 0 24 24">
                                                <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                            </svg>
                                        @endif
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="group inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50/50 shadow-sm transition-all duration-200" 
                                   title="Login untuk favorit">
                                    <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                </a>
                            @endauth

                            <a href="{{ route('chat.product.start', $produk->id) }}" 
                               class="group inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 hover:text-emerald-600 hover:border-emerald-200 hover:bg-emerald-50/50 shadow-sm transition-all duration-200" 
                               title="Hubungi Penjual">
                                <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                            </a>
                        </div>
                    </div>



                    <div class="grid gap-4 mb-6">
                        <div class="rounded-3xl bg-slate-50 p-4">
                            <p class="text-sm text-slate-500 uppercase font-bold tracking-wider mb-1">Harga Sewa</p>
                            <p class="text-3xl font-black text-emerald-600">Rp {{ number_format($produk->rent_price) }}<span class="text-sm font-bold text-slate-400">/hari</span></p>
                        </div>
                        <div class="rounded-3xl bg-blue-50 p-4 border border-blue-100">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] text-blue-600 uppercase font-black tracking-wider">Dana Jaminan (Keamanan)</span>
                                <div class="group relative">
                                    <span class="cursor-help text-blue-400 text-xs">ⓘ</span>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-48 p-2 bg-slate-900 text-white text-[10px] rounded-lg shadow-xl z-20">
                                        Dana ini adalah deposit 25% dari harga beli produk (Rp {{ number_format($produk->buy_price) }}) yang akan dikembalikan utuh jika barang kembali aman.
                                    </div>
                                </div>
                            </div>
                            <p class="text-xl font-black text-blue-700">Rp {{ number_format($produk->buy_price * 0.25) }}</p>
                            <p class="text-[9px] text-blue-500 italic mt-1">*Dibayarkan sekali di awal, kembali utuh di akhir.</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-slate-50 rounded-3xl p-5 border border-slate-200">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-semibold text-slate-900">Sewa Sekarang</h3>
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ ($produk->stock ?? 0) > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
                                    Sisa Stok: {{ $produk->stock ?? 0 }} unit
                                </span>
                            </div>

                            @if(($produk->stock ?? 0) > 0)
                                <form action="{{ route('sewa.form', $produk->id) }}" method="GET" class="space-y-4">
                                    <p class="text-sm text-slate-500 mb-3 leading-relaxed">Klik tombol di bawah untuk mengisi formulir penyewaan. <strong>Catatan:</strong> Anda wajib mengunggah foto KTP untuk keamanan transaksi.</p>
                                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-4 text-base font-bold text-white hover:bg-emerald-700 w-full shadow-lg shadow-emerald-100 transition-all">Lanjutkan ke Formulir Sewa</button>
                                </form>
                            @else
                                <div class="p-4 bg-red-50 border border-red-100 rounded-2xl text-center">
                                    <p class="text-sm font-bold text-red-600 mb-1">Maaf, Stok Sewa Sedang Habis</p>
                                    <p class="text-xs text-red-500">Barang ini sedang disewa seluruhnya atau belum tersedia kembali.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8 pt-8 border-t border-slate-100">
                        @auth
                            @if(auth()->user()->role === 'buyer')
                                <form method="POST" action="{{ route('products.report', $produk->id) }}" class="mb-4 grid gap-2 sm:grid-cols-[160px_1fr_auto]">
                                    @csrf
                                    <select name="reason" class="rounded-2xl border border-slate-200 px-3 py-2 text-sm">
                                        <option value="Produk mencurigakan">Produk mencurigakan</option>
                                        <option value="Harga tidak wajar">Harga tidak wajar</option>
                                        <option value="Deskripsi menyesatkan">Deskripsi menyesatkan</option>
                                    </select>
                                    <input name="description" class="rounded-2xl border border-slate-200 px-3 py-2 text-sm" placeholder="Detail laporan (opsional)">
                                    <button class="rounded-2xl border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">Laporkan</button>
                                </form>
                            @endif
                        @endauth

                        @if($produk->store_id)
                            @auth
                                @if(auth()->user()->role === 'buyer')
                                    <form method="POST" action="{{ route('stores.report', $produk->store_id) }}" class="grid gap-2 sm:grid-cols-[160px_1fr_auto]">
                                        @csrf
                                        <select name="reason" class="rounded-2xl border border-slate-200 px-3 py-2 text-sm">
                                            <option value="Toko mencurigakan">Toko mencurigakan</option>
                                            <option value="Pelayanan buruk">Pelayanan buruk</option>
                                            <option value="Informasi toko tidak valid">Informasi toko tidak valid</option>
                                        </select>
                                        <input name="description" class="rounded-2xl border border-slate-200 px-3 py-2 text-sm" placeholder="Detail laporan toko (opsional)">
                                        <button class="rounded-2xl border border-orange-200 px-4 py-2 text-sm font-semibold text-orange-600 hover:bg-orange-50">Laporkan Toko</button>
                                    </form>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
