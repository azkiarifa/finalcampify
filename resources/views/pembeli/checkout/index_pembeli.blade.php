@extends('layouts.app_pembeli')

@section('content')
<div class="py-10 max-w-4xl mx-auto px-4 pb-16">

    <a href="{{ route('cart.index') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 mb-4 inline-flex items-center gap-1 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Keranjang
    </a>

    <div class="mb-6">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Checkout Pesanan</h1>
        <p class="text-sm text-slate-500 mt-1">Periksa detail barang, pilih alamat dan kurir, lalu selesaikan pembayaran.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-rose-50 border-2 border-rose-200 rounded-2xl text-rose-800 shadow-sm">
            <div class="flex items-center gap-2 font-bold mb-1">
                <span>⚠️</span>
                <span>Mohon lengkapi data berikut:</span>
            </div>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-sm font-semibold flex items-center gap-2">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('checkout.process') }}" method="POST" enctype="multipart/form-data" id="checkout-form" class="space-y-6">
        @csrf

        {{-- Hidden inputs for selected items --}}
        @foreach($cart as $item)
            <input type="hidden" name="selected_items[]" value="{{ $item->id }}">
        @endforeach

        {{-- ================= 1. DETAIL BARANG (Dahulukan Detail Barang) ================= --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <h2 class="text-lg font-bold text-slate-900 mb-4">
                Detail Barang yang Dipesan
            </h2>

            <div class="divide-y divide-slate-100">
                @php $totalSubtotal = 0; @endphp
                @foreach($cart as $item)
                    @php
                        $price = $item->type === 'buy' ? $item->product->buy_price : ($item->product->rent_price * $item->duration);
                        $itemSubtotal = $price * $item->qty;
                        $totalSubtotal += $itemSubtotal;
                    @endphp
                    <div class="py-3.5 flex items-center gap-4 first:pt-0 last:pb-0">
                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded-xl border border-slate-100 bg-slate-50 flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] font-black px-2 py-0.5 rounded uppercase tracking-wider {{ $item->type === 'buy' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ $item->type === 'buy' ? 'Beli' : 'Sewa' }}
                                </span>
                                @if($item->type === 'rent')
                                    <span class="text-[10px] text-slate-400 font-medium">{{ $item->duration }} Hari</span>
                                @endif
                            </div>
                            <h4 class="font-bold text-slate-900 text-sm truncate">{{ $item->product->name }}</h4>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $item->qty }} unit x Rp {{ number_format($price, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-black text-slate-800">Rp {{ number_format($itemSubtotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ================= 2. ALAMAT PENGIRIMAN (Setelah Detail Barang) ================= --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        Alamat Pengiriman
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Nama penerima paket terpisah dari nama profil akun Anda.</p>
                </div>
                <button type="button" onclick="toggleNewAddressModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold transition border border-emerald-200 cursor-pointer self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Alamat Lain
                </button>
            </div>

            {{-- Kartu Pilihan Alamat Tersimpan --}}
            @if(isset($addresses) && $addresses->isNotEmpty())
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2.5">Pilih dari Alamat Tersimpan:</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="address-card-list">
                        @foreach($addresses as $idx => $addr)
                            @php
                                $isFirst = $idx === 0;
                            @endphp
                            <label class="relative flex flex-col p-4 rounded-xl border-2 transition cursor-pointer {{ $isFirst ? 'border-emerald-500 bg-emerald-50/40' : 'border-slate-200 hover:border-slate-300 bg-white' }} address-radio-card">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="selected_address_id" value="{{ $addr->id }}" {{ $isFirst ? 'checked' : '' }} 
                                               class="address-picker text-emerald-600 focus:ring-emerald-500"
                                               data-nama="{{ $addr->receiver_name }}"
                                               data-telepon="{{ $addr->phone }}"
                                               data-alamat="{{ $addr->address }}"
                                               data-kota="{{ $addr->city }}"
                                               data-kecamatan="{{ $addr->district }}"
                                               data-kodepos="{{ $addr->postal_code }}">
                                        <span class="text-xs font-black text-slate-800 uppercase tracking-wider">{{ $addr->label ?: 'Alamat' }}</span>
                                    </div>
                                    @if($addr->is_primary)
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Utama</span>
                                    @endif
                                </div>
                                <div class="text-xs space-y-1 text-slate-600">
                                    <p class="font-bold text-slate-900 text-sm">{{ $addr->receiver_name }}</p>
                                    <p class="text-slate-500">{{ $addr->phone }}</p>
                                    <p class="line-clamp-2">{{ $addr->address }}</p>
                                    <p class="text-slate-500 font-medium">{{ $addr->district ? $addr->district . ', ' : '' }}{{ $addr->city }} {{ $addr->postal_code }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Bar Tombol Ubah / Edit Alamat --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 pb-2 border-t border-slate-100">
                    <p class="text-xs text-slate-500 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Pesanan ini otomatis dikirim ke alamat yang Anda pilih di atas.
                    </p>
                    <button type="button" onclick="toggleEditAddress()" id="btn-toggle-edit-address" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-3.5 py-1.5 rounded-xl transition cursor-pointer self-start sm:self-auto">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        <span id="btn-toggle-edit-text">Edit Alamat Pesanan Ini</span>
                    </button>
                </div>
            @endif

            {{-- Form Input Detail Alamat (Hanya muncul jika klik Edit atau jika belum ada alamat tersimpan) --}}
            <div id="manual-address-details" class="pt-4 border-t border-slate-100 {{ (isset($addresses) && $addresses->isNotEmpty()) ? 'hidden' : '' }}">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Detail Penerima Pesanan Ini:</p>
                    @if(isset($addresses) && $addresses->isNotEmpty())
                        <button type="button" onclick="toggleEditAddress()" class="text-xs text-slate-400 hover:text-slate-600 font-medium cursor-pointer">✕ Sembunyikan</button>
                    @endif
                </div>
                <div class="grid gap-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Penerima (Nama di Alamat) <span class="text-rose-500">*</span>
                            </label>
                            @php
                                $initialReceiver = old('nama', (isset($addresses) && $addresses->first()) ? $addresses->first()->receiver_name : (auth()->user()->receiver_name ?: auth()->user()->name));
                            @endphp
                            <input type="text" name="nama" id="receiver-name-input" value="{{ $initialReceiver }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" placeholder="Nama lengkap penerima barang" required>
                            <p class="text-[10px] text-slate-400 mt-1">Nama ini dicantumkan pada label resi pengiriman (Profil: <strong>{{ auth()->user()->name }}</strong>).</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                No. Telepon Penerima <span class="text-rose-500">*</span>
                            </label>
                            @php
                                $initialPhone = old('telepon', (isset($addresses) && $addresses->first()) ? $addresses->first()->phone : auth()->user()->phone);
                            @endphp
                            <input type="tel" name="telepon" id="receiver-phone-input" value="{{ $initialPhone }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" placeholder="08xxxxxxxxxx" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Alamat Lengkap Pengiriman <span class="text-rose-500">*</span>
                        </label>
                        @php
                            $initialAddress = old('alamat', (isset($addresses) && $addresses->first()) ? $addresses->first()->address : auth()->user()->address);
                        @endphp
                        <textarea name="alamat" id="receiver-address-input" rows="2" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" placeholder="Nama jalan, nomor rumah, RT/RW, patokan" required>{{ $initialAddress }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kota / Kabupaten <span class="text-rose-500">*</span></label>
                            <select name="kota" id="city-checkout" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-white" required>
                                <option value="">Pilih Kota</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kecamatan <span class="text-rose-500">*</span></label>
                            <select name="kecamatan" id="district-checkout" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-white" required>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kode Pos <span class="text-rose-500">*</span></label>
                            @php
                                $initialPostal = old('kode_pos', (isset($addresses) && $addresses->first()) ? $addresses->first()->postal_code : auth()->user()->postal_code);
                            @endphp
                            <input type="text" name="kode_pos" id="postal_code-checkout" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-slate-50" required value="{{ $initialPostal }}" placeholder="Kode Pos" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= 3. OPSI PENGIRIMAN ================= --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <h2 class="text-lg font-bold text-slate-900 mb-4">
                Pilih Opsi Pengiriman
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($couriers as $index => $courier)
                <label class="flex items-center justify-between p-4 border-2 rounded-xl cursor-pointer hover:border-emerald-300 transition-all courier-card {{ $index === 0 ? 'border-emerald-500 bg-emerald-50/30' : 'border-slate-200 bg-white' }}">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="shipping_method" value="{{ $courier->service }}" data-price="{{ $courier->price }}" {{ $index === 0 ? 'checked' : '' }} class="courier-radio text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <p class="font-bold text-sm text-slate-900">{{ $courier->service }}</p>
                            <p class="text-[11px] text-slate-500">{{ $courier->name }} &bull; Estimasi {{ $courier->estimate ?? '-' }}</p>
                        </div>
                    </div>
                    <span class="font-black text-sm text-emerald-700">Rp {{ number_format($courier->price, 0, ',', '.') }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- ================= 4. PEMBAYARAN, BUKTI TRANSFER & RINGKASAN SUBMIT ================= --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <h2 class="text-lg font-bold text-slate-900 mb-1">
                Pembayaran & Bukti Transfer
            </h2>
            <p class="text-xs text-slate-500 mb-5">Lakukan transfer ke rekening resmi yang telah ditentukan oleh Admin di bawah ini, lalu unggah bukti transfer.</p>

            {{-- Kartu Info Rekening Bank Dari Input Admin --}}
            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-lg flex-shrink-0">
                            🏦
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-slate-900">Transfer Bank Escrow Resmi</h3>
                            <p class="text-xs text-slate-500">Dana ditampung aman oleh sistem hingga barang Anda terima.</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold self-start sm:self-auto">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Terverifikasi Admin
                    </span>
                </div>

                {{-- Rekening Details Dari Input Admin --}}
                @php
                    $bankName = $admin?->bank_name ?: 'BANK BCA';
                    $bankAccNumber = $admin?->bank_account_number ?: '872091823401';
                    $bankAccName = $admin?->bank_account_name ?: ($admin?->name ?: 'ADMIN CAMPIFY');
                @endphp
                <div class="mt-4 p-4 bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-[10px] font-black uppercase text-emerald-600 tracking-wider">{{ $bankName }}</span>
                        <p class="text-2xl font-black text-slate-900 tracking-wider font-mono my-0.5">{{ $bankAccNumber }}</p>
                        <p class="text-xs text-slate-500">A/N: <strong class="text-slate-800">{{ $bankAccName }}</strong></p>
                    </div>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ $bankAccNumber }}').then(() => alert('Nomor rekening berhasil disalin!'))" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition border border-slate-200 cursor-pointer self-start sm:self-center shadow-sm">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Salin Rekening
                    </button>
                </div>

                <input type="hidden" name="metode_pembayaran" value="transfer">
            </div>

            {{-- UPLOAD BUKTI PEMBAYARAN --}}
            <div class="pt-2">
                <label class="block text-sm font-bold text-slate-900 mb-1">
                    Upload Bukti Pembayaran <span class="text-rose-500">*</span>
                </label>
                <p class="text-xs text-slate-500 mb-3">Unggah foto/screenshot bukti transfer (JPG, PNG, WebP, maks. 2MB) setelah memilih pengiriman.</p>
                
                <input type="file" name="bukti_pembayaran" id="bukti_pembayaran_input" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200 cursor-pointer" required>
                
                <div id="proof_preview_box" class="mt-3 hidden">
                    <p class="text-xs font-semibold text-slate-600 mb-1.5">Pratinjau Bukti Transfer:</p>
                    <div class="p-2 border border-slate-200 rounded-xl bg-slate-50 w-fit">
                        <img id="proof_preview_img" src="" alt="Pratinjau Bukti" class="h-36 object-contain rounded-lg">
                    </div>
                </div>
            </div>

            {{-- ================= KOLOM RINGKASAN PEMBAYARAN & TOMBOL SUBMIT (DI BAWAH UPLOAD BUKTI BAYAR) ================= --}}
            @php
                $selectedCourierPrice = $couriers->first()->price ?? 0;
                $initialGrandTotal = $totalSubtotal + $selectedCourierPrice;
            @endphp
            <div class="mt-8 pt-6 border-t border-slate-200">
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 space-y-3 mb-6">
                    <div class="flex justify-between items-center text-sm text-slate-600">
                        <span>Subtotal Produk:</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm text-slate-600">
                        <span>Biaya Pengiriman (Ongkir):</span>
                        <span class="font-bold text-slate-800" id="shippingTotal">Rp {{ number_format($selectedCourierPrice, 0, ',', '.') }}</span>
                    </div>
                    <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <span class="text-sm font-bold text-slate-700 uppercase tracking-wider">Total Pembayaran:</span>
                        <span class="text-3xl font-black text-emerald-600 tracking-tight" id="grandTotal">
                            Rp {{ number_format($initialGrandTotal, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <button type="submit" id="submitOrderBtn" class="w-full inline-flex items-center justify-center gap-2.5 bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-xl font-bold text-base transition-all shadow-lg shadow-emerald-200 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Submit Bukti Bayar
                </button>
            </div>
        </div>

    </form>
</div>

{{-- MODAL TAMBAH ALAMAT BARU --}}
<div id="newAddressModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-8 relative max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="text-lg font-bold text-slate-900">Tambah Alamat Baru</h3>
            <button type="button" onclick="toggleNewAddressModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('checkout.address.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Label Alamat (Contoh: Rumah, Kantor, Kost)</label>
                <input type="text" name="label" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm focus:border-emerald-500" placeholder="Rumah" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Penerima (Nama di Alamat) <span class="text-rose-500">*</span></label>
                <input type="text" name="receiver_name" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm focus:border-emerald-500" placeholder="Nama lengkap penerima" required>
                <p class="text-[10px] text-slate-400 mt-0.5">Terpisah dari nama profil akun Anda.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Telepon Penerima <span class="text-rose-500">*</span></label>
                <input type="tel" name="phone" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm focus:border-emerald-500" placeholder="08xxxxxxxxxx" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Lengkap <span class="text-rose-500">*</span></label>
                <textarea name="address" rows="2" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm focus:border-emerald-500" placeholder="Jalan, nomor rumah, RT/RW, kelurahan" required></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kota <span class="text-rose-500">*</span></label>
                    <select name="city" id="modal-city" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-emerald-500 bg-white" required>
                        <option value="">Pilih Kota</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kecamatan <span class="text-rose-500">*</span></label>
                    <select name="district" id="modal-district" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-emerald-500 bg-white" required>
                        <option value="">Pilih Kecamatan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kode Pos <span class="text-rose-500">*</span></label>
                    <input type="text" name="postal_code" id="modal-postal-code" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-emerald-500 bg-slate-50" readonly required placeholder="Kode Pos">
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_primary" id="is_primary" value="1" class="rounded text-emerald-600 focus:ring-emerald-500">
                <label for="is_primary" class="text-xs text-slate-700">Jadikan sebagai alamat utama</label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="toggleNewAddressModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white shadow">Simpan Alamat</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleNewAddressModal() {
        const modal = document.getElementById('newAddressModal');
        if (modal) {
            modal.classList.toggle('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const dataAlamat = [
            {
                kota: 'Kota Bandung',
                kecamatan: [
                    { nama: 'Andir', kode_pos: '40184' }, { nama: 'Antapani', kode_pos: '40291' }, { nama: 'Arcamanik', kode_pos: '40293' }, { nama: 'Astanaanyar', kode_pos: '40242' },
                    { nama: 'Babakan Ciparay', kode_pos: '40223' }, { nama: 'Bandung Kidul', kode_pos: '40266' }, { nama: 'Bandung Kulon', kode_pos: '40214' }, { nama: 'Bandung Wetan', kode_pos: '40116' },
                    { nama: 'Batununggal', kode_pos: '40275' }, { nama: 'Bojongloa Kaler', kode_pos: '40232' }, { nama: 'Bojongloa Kidul', kode_pos: '40233' }, { nama: 'Buahbatu', kode_pos: '40286' },
                    { nama: 'Cibeunying Kaler', kode_pos: '40191' }, { nama: 'Cibeunying Kidul', kode_pos: '40125' }, { nama: 'Cibiru', kode_pos: '40614' }, { nama: 'Cicendo', kode_pos: '40171' },
                    { nama: 'Cidadap', kode_pos: '40142' }, { nama: 'Cinambo', kode_pos: '40294' }, { nama: 'Coblong', kode_pos: '40132' }, { nama: 'Gedebage', kode_pos: '40294' },
                    { nama: 'Kiaracondong', kode_pos: '40284' }, { nama: 'Lengkong', kode_pos: '40261' }, { nama: 'Mandalajati', kode_pos: '40194' }, { nama: 'Panyileukan', kode_pos: '40614' },
                    { nama: 'Rancasari', kode_pos: '40286' }, { nama: 'Regol', kode_pos: '40252' }, { nama: 'Sukajadi', kode_pos: '40161' }, { nama: 'Sukasari', kode_pos: '40151' },
                    { nama: 'Sumur Bandung', kode_pos: '40111' }, { nama: 'Ujungberung', kode_pos: '40611' }
                ]
            },
            {
                kota: 'Kabupaten Bandung',
                kecamatan: [
                    { nama: 'Arjasari', kode_pos: '40379' }, { nama: 'Baleendah', kode_pos: '40375' }, { nama: 'Banjaran', kode_pos: '40377' }, { nama: 'Bojongsoang', kode_pos: '40288' },
                    { nama: 'Cangkuang', kode_pos: '40238' }, { nama: 'Cicalengka', kode_pos: '40395' }, { nama: 'Cikancung', kode_pos: '40396' }, { nama: 'Cilengkrang', kode_pos: '40615' },
                    { nama: 'Cileunyi', kode_pos: '40622' }, { nama: 'Cimaung', kode_pos: '40374' }, { nama: 'Cimenyan', kode_pos: '40197' }, { nama: 'Ciparay', kode_pos: '40381' },
                    { nama: 'Ciwidey', kode_pos: '40973' }, { nama: 'Dayeuhkolot', kode_pos: '40258' }, { nama: 'Katapang', kode_pos: '40921' }, { nama: 'Majalaya', kode_pos: '40382' },
                    { nama: 'Margaasih', kode_pos: '40215' }, { nama: 'Margahayu', kode_pos: '40226' }, { nama: 'Pangalengan', kode_pos: '40378' }, { nama: 'Paseh', kode_pos: '40383' },
                    { nama: 'Rancaekek', kode_pos: '40394' }, { nama: 'Soreang', kode_pos: '40911' }
                ]
            },
            {
                kota: 'Kabupaten Bandung Barat',
                kecamatan: [
                    { nama: 'Batujajar', kode_pos: '40561' }, { nama: 'Cihampelas', kode_pos: '40562' }, { nama: 'Cikalongwetan', kode_pos: '40556' }, { nama: 'Cililin', kode_pos: '40562' },
                    { nama: 'Cipatat', kode_pos: '40554' }, { nama: 'Cipeundeuy', kode_pos: '40558' }, { nama: 'Cipongkor', kode_pos: '40564' }, { nama: 'Cisarua', kode_pos: '40551' },
                    { nama: 'Gununghalu', kode_pos: '40565' }, { nama: 'Lembang', kode_pos: '40391' }, { nama: 'Ngamprah', kode_pos: '40552' }, { nama: 'Padalarang', kode_pos: '40553' },
                    { nama: 'Parongpong', kode_pos: '40559' }, { nama: 'Sindangkerta', kode_pos: '40563' }
                ]
            },
            {
                kota: 'Kota Cimahi',
                kecamatan: [
                    { nama: 'Cimahi Selatan', kode_pos: '40531' }, { nama: 'Cimahi Tengah', kode_pos: '40521' }, { nama: 'Cimahi Utara', kode_pos: '40511' }
                ]
            },
            {
                kota: 'Kota Depok',
                kecamatan: [
                    { nama: 'Beji', kode_pos: '16421' }, { nama: 'Bojongsari', kode_pos: '16516' }, { nama: 'Cilodong', kode_pos: '16413' }, { nama: 'Cimanggis', kode_pos: '16451' },
                    { nama: 'Cinere', kode_pos: '16514' }, { nama: 'Cipayung', kode_pos: '16437' }, { nama: 'Limo', kode_pos: '16515' }, { nama: 'Pancoran Mas', kode_pos: '16431' },
                    { nama: 'Sawangan', kode_pos: '16511' }, { nama: 'Sukmajaya', kode_pos: '16411' }, { nama: 'Tapos', kode_pos: '16457' }
                ]
            },
            {
                kota: 'Kota Bogor',
                kecamatan: [
                    { nama: 'Bogor Barat', kode_pos: '16111' }, { nama: 'Bogor Selatan', kode_pos: '16131' }, { nama: 'Bogor Tengah', kode_pos: '16121' }, { nama: 'Bogor Timur', kode_pos: '16141' },
                    { nama: 'Bogor Utara', kode_pos: '16151' }, { nama: 'Tanah Sareal', kode_pos: '16161' }
                ]
            },
            {
                kota: 'Majalengka',
                kecamatan: [
                    { nama: 'Kadipaten', kode_pos: '45452' }, { nama: 'Jatiwangi', kode_pos: '45454' }, { nama: 'Majalengka', kode_pos: '45411' }, { nama: 'Bantarujeg', kode_pos: '45464' }
                ]
            }
        ];

        function populateKotaSelect(citySelectEl, districtSelectEl, postalInputEl, selectedCity, selectedDistrict) {
            if (!citySelectEl) return;
            citySelectEl.innerHTML = '<option value="">Pilih Kota</option>';
            dataAlamat.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.kota;
                opt.textContent = item.kota;
                if (selectedCity && selectedCity.toLowerCase() === item.kota.toLowerCase()) {
                    opt.selected = true;
                }
                citySelectEl.appendChild(opt);
            });

            function updateKecamatan() {
                if (!districtSelectEl) return;
                const kotaName = citySelectEl.value;
                const kotaObj = dataAlamat.find(k => k.kota === kotaName);
                districtSelectEl.innerHTML = '<option value="">Pilih Kecamatan</option>';

                if (kotaObj) {
                    kotaObj.kecamatan.forEach(kec => {
                        const opt = document.createElement('option');
                        opt.value = kec.nama;
                        opt.textContent = kec.nama;
                        opt.dataset.kodepos = kec.kode_pos;
                        if (selectedDistrict && selectedDistrict.toLowerCase() === kec.nama.toLowerCase()) {
                            opt.selected = true;
                        }
                        districtSelectEl.appendChild(opt);
                    });
                }
                updateKodePos();
            }

            function updateKodePos() {
                if (!districtSelectEl || !postalInputEl) return;
                const selectedOpt = districtSelectEl.options[districtSelectEl.selectedIndex];
                if (selectedOpt && selectedOpt.dataset.kodepos) {
                    postalInputEl.value = selectedOpt.dataset.kodepos;
                }
            }

            citySelectEl.addEventListener('change', () => {
                selectedDistrict = '';
                updateKecamatan();
            });

            districtSelectEl.addEventListener('change', updateKodePos);

            updateKecamatan();
        }

        // Inisialisasi dropdown alamat checkout utama
        const citySelect = document.getElementById('city-checkout');
        const districtSelect = document.getElementById('district-checkout');
        const postalInput = document.getElementById('postal_code-checkout');
        const currentCity = "{{ (isset($addresses) && $addresses->first()) ? $addresses->first()->city : (auth()->user()->city ?: 'Kota Bandung') }}";
        const currentDistrict = "{{ (isset($addresses) && $addresses->first()) ? $addresses->first()->district : (auth()->user()->district ?: '') }}";

        populateKotaSelect(citySelect, districtSelect, postalInput, currentCity, currentDistrict);

        // Inisialisasi dropdown modal alamat baru
        const modalCity = document.getElementById('modal-city');
        const modalDistrict = document.getElementById('modal-district');
        const modalPostal = document.getElementById('modal-postal-code');
        populateKotaSelect(modalCity, modalDistrict, modalPostal, '', '');

        // Toggle Edit Form Alamat
        window.toggleEditAddress = function() {
            const container = document.getElementById('manual-address-details');
            const btnText = document.getElementById('btn-toggle-edit-text');
            if (container) {
                container.classList.toggle('hidden');
                if (!container.classList.contains('hidden')) {
                    if (btnText) btnText.textContent = 'Tutup Edit Alamat';
                    receiverNameInput?.focus();
                } else {
                    if (btnText) btnText.textContent = 'Edit Alamat Pesanan Ini';
                }
            }
        };

        // Handle Pergantian Radio Pilihan Alamat
        const addressRadios = document.querySelectorAll('.address-picker');
        const receiverNameInput = document.getElementById('receiver-name-input');
        const receiverPhoneInput = document.getElementById('receiver-phone-input');
        const receiverAddressInput = document.getElementById('receiver-address-input');

        addressRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.address-radio-card').forEach(card => {
                    card.classList.remove('border-emerald-500', 'bg-emerald-50/40');
                    card.classList.add('border-slate-200', 'bg-white');
                });
                const parentCard = this.closest('.address-radio-card');
                if (parentCard) {
                    parentCard.classList.remove('border-slate-200', 'bg-white');
                    parentCard.classList.add('border-emerald-500', 'bg-emerald-50/40');
                }

                if (receiverNameInput && this.dataset.nama) receiverNameInput.value = this.dataset.nama;
                if (receiverPhoneInput && this.dataset.telepon) receiverPhoneInput.value = this.dataset.telepon;
                if (receiverAddressInput && this.dataset.alamat) receiverAddressInput.value = this.dataset.alamat;

                if (this.dataset.kota) {
                    populateKotaSelect(citySelect, districtSelect, postalInput, this.dataset.kota, this.dataset.kecamatan);
                }
                if (postalInput && this.dataset.kodepos) {
                    postalInput.value = this.dataset.kodepos;
                }
            });
        });

        // Handle Ongkir & Perhitungan Total di Kolom Ringkasan
        const subtotal = {{ $totalSubtotal }};
        const courierRadios = document.querySelectorAll('.courier-radio');
        const shippingTotalEl = document.getElementById('shippingTotal');
        const grandTotalEl = document.getElementById('grandTotal');

        function updateTotals() {
            let shippingPrice = 0;
            courierRadios.forEach(radio => {
                const card = radio.closest('.courier-card');
                if (radio.checked) {
                    shippingPrice = parseFloat(radio.dataset.price) || 0;
                    if (card) {
                        card.classList.remove('border-slate-200', 'bg-white');
                        card.classList.add('border-emerald-500', 'bg-emerald-50/30');
                    }
                } else if (card) {
                    card.classList.remove('border-emerald-500', 'bg-emerald-50/30');
                    card.classList.add('border-slate-200', 'bg-white');
                }
            });

            const grandTotal = subtotal + shippingPrice;
            if (shippingTotalEl) {
                shippingTotalEl.textContent = 'Rp ' + shippingPrice.toLocaleString('id-ID');
            }
            if (grandTotalEl) {
                grandTotalEl.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
            }
        }

        courierRadios.forEach(r => r.addEventListener('change', updateTotals));
        updateTotals();

        // Pratinjau Bukti Pembayaran
        const proofInput = document.getElementById('bukti_pembayaran_input');
        const proofPreviewBox = document.getElementById('proof_preview_box');
        const proofPreviewImg = document.getElementById('proof_preview_img');

        if (proofInput && proofPreviewBox && proofPreviewImg) {
            proofInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file maksimal 2MB!');
                        this.value = '';
                        proofPreviewBox.classList.add('hidden');
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        proofPreviewImg.src = e.target.result;
                        proofPreviewBox.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    proofPreviewBox.classList.add('hidden');
                }
            });
        }

        // Form Submit Validation
        const checkoutForm = document.getElementById('checkout-form');
        const submitOrderBtn = document.getElementById('submitOrderBtn');

        if (checkoutForm) {
            checkoutForm.addEventListener('submit', function(e) {
                const revealManualAddress = () => {
                    const c = document.getElementById('manual-address-details');
                    if (c && c.classList.contains('hidden')) {
                        c.classList.remove('hidden');
                        const btnText = document.getElementById('btn-toggle-edit-text');
                        if (btnText) btnText.textContent = 'Tutup Edit Alamat';
                    }
                };

                if (!receiverNameInput || !receiverNameInput.value.trim()) {
                    e.preventDefault();
                    revealManualAddress();
                    alert('Mohon isi Nama Penerima di alamat pengiriman.');
                    receiverNameInput.focus();
                    return false;
                }
                if (!receiverPhoneInput || !receiverPhoneInput.value.trim()) {
                    e.preventDefault();
                    revealManualAddress();
                    alert('Mohon isi Nomor Telepon Penerima.');
                    receiverPhoneInput.focus();
                    return false;
                }
                if (!receiverAddressInput || !receiverAddressInput.value.trim()) {
                    e.preventDefault();
                    revealManualAddress();
                    alert('Mohon isi Alamat Lengkap pengiriman.');
                    receiverAddressInput.focus();
                    return false;
                }
                if (!citySelect || !citySelect.value) {
                    e.preventDefault();
                    revealManualAddress();
                    alert('Mohon pilih Kota pengiriman.');
                    citySelect.focus();
                    return false;
                }
                if (!proofInput || !proofInput.files || proofInput.files.length === 0) {
                    e.preventDefault();
                    alert('Bukti Pembayaran wajib diunggah! Silakan pilih foto bukti transfer Anda.');
                    proofInput.focus();
                    proofInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }

                if (submitOrderBtn) {
                    submitOrderBtn.disabled = true;
                    submitOrderBtn.classList.add('opacity-75', 'cursor-not-allowed');
                    submitOrderBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        Memproses Pesanan...
                    `;
                }
            });
        }
    });
</script>
@endsection
