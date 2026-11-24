@extends('layouts.app_pembeli')

@section('content')
<div class="pt-8 pb-20 bg-slate-50 min-h-screen">
    <div class="max-w-2xl mx-auto px-4">
        
        <div class="mb-6">
            <a href="{{ route('orders.detail', $pesanan->id) }}" class="text-sm text-slate-500 hover:text-slate-800 flex items-center gap-2">
                ← Kembali ke Detail Pesanan
            </a>
        </div>

        <!-- Progress Stepper (Khusus Sewa) -->
        @if($detail->type === 'rent')
        <div class="bg-white rounded-[24px] shadow-sm border border-slate-100 p-6 mb-6">
            <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-slate-400">
                <span>Status Pengembalian</span>
                <span class="text-emerald-600">Sewa</span>
            </div>
            
            <div class="mt-4 flex items-center justify-between relative">
                <!-- Line Behind Stepper -->
                <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-[2px] bg-slate-100 z-0"></div>
                
                @php
                    $status = $return ? $return->status : 'start';
                    $step = 1;
                    if ($status === 'pending') $step = 2;
                    if ($status === 'approved') $step = 3;
                    if ($status === 'shipping') $step = 4;
                    if (in_array($status, ['denda_pending', 'denda_submitted'])) $step = 5;
                    if ($status === 'completed') $step = 6;
                @endphp

                <!-- Step 1: Ajukan -->
                <div class="z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 1 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'bg-slate-100 text-slate-400' }}">1</div>
                    <span class="text-[9px] font-bold text-slate-500 mt-2">Pengajuan</span>
                </div>
                
                <!-- Step 2: Persetujuan -->
                <div class="z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 2 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'bg-slate-100 text-slate-400' }}">2</div>
                    <span class="text-[9px] font-bold text-slate-500 mt-2">Persetujuan</span>
                </div>

                <!-- Step 3: Resi/Kirim -->
                <div class="z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 3 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'bg-slate-100 text-slate-400' }}">3</div>
                    <span class="text-[9px] font-bold text-slate-500 mt-2">Kirim</span>
                </div>

                <!-- Step 4: Pengecekan -->
                <div class="z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 4 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'bg-slate-100 text-slate-400' }}">4</div>
                    <span class="text-[9px] font-bold text-slate-500 mt-2">Pengecekan</span>
                </div>

                <!-- Step 5: Denda (Jika ada) -->
                <div class="z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 5 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'bg-slate-100 text-slate-400' }}">5</div>
                    <span class="text-[9px] font-bold text-slate-500 mt-2">Denda</span>
                </div>

                <!-- Step 6: Selesai -->
                <div class="z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 6 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'bg-slate-100 text-slate-400' }}">6</div>
                    <span class="text-[9px] font-bold text-slate-500 mt-2">Selesai</span>
                </div>
            </div>
        </div>
        @endif

        <!-- Progress Stepper (Khusus Beli / Purchase) -->
        @if($detail->type === 'buy')
        <div class="bg-white rounded-[24px] shadow-sm border border-slate-100 p-6 mb-6">
            <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-slate-400">
                <span>Status Retur</span>
                <span class="text-rose-600 font-extrabold">Beli</span>
            </div>
            
            <div class="mt-4 flex items-center justify-between relative">
                <!-- Line Behind Stepper -->
                <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-[2px] bg-slate-100 z-0"></div>
                
                @php
                    $status = $return ? $return->status : 'start';
                    $step = 1;
                    if ($status === 'pending') $step = 2;
                    if (in_array($status, ['approved', 'refund_pending', 'replacement_shipping', 'shipping'])) $step = 3;
                    if ($status === 'completed') $step = 4;
                    if ($status === 'rejected') $step = 0;

                    $step4Title = 'Selesai';
                    if ($return) {
                        if ($return->resolution_type === 'refund') {
                            $step4Title = 'Dana Dikembalikan';
                        } elseif ($return->resolution_type === 'replacement') {
                            $step4Title = 'Barang Diterima';
                        }
                    }
                @endphp

                <!-- Step 1: Pengajuan -->
                <div class="z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 1 ? 'bg-rose-600 text-white shadow-md shadow-rose-200' : 'bg-slate-100 text-slate-400' }}">1</div>
                    <span class="text-[9px] font-bold text-slate-500 mt-2">Pengajuan</span>
                </div>
                
                <!-- Step 2: Tinjauan Seller -->
                <div class="z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 2 ? 'bg-rose-600 text-white shadow-md shadow-rose-200' : 'bg-slate-100 text-slate-400' }}">2</div>
                    <span class="text-[9px] font-bold text-slate-500 mt-2">Tinjauan Seller</span>
                </div>

                <!-- Step 3: Resolusi -->
                <div class="z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 3 ? 'bg-rose-600 text-white shadow-md shadow-rose-200' : 'bg-slate-100 text-slate-400' }}">3</div>
                    <span class="text-[9px] font-bold text-slate-500 mt-2">Resolusi</span>
                </div>

                <!-- Step 4: Selesai -->
                <div class="z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 4 ? 'bg-rose-600 text-white shadow-md shadow-rose-200' : 'bg-slate-100 text-slate-400' }}">4</div>
                    <span class="text-[9px] font-bold text-slate-500 mt-2 text-center">{{ $step4Title }}</span>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-[32px] shadow-xl p-8 border border-slate-100">
            <h1 class="text-2xl font-bold text-slate-900 mb-6">Pengembalian Produk</h1>

            <!-- Info Produk -->
            <div class="flex items-center gap-4 mb-8 p-4 bg-slate-50 rounded-2xl">
                @php
                    $imgPath = $detail->product?->image ?? $detail->product?->gambar;
                    if ($imgPath && !str_starts_with($imgPath, 'assets/images/') && !str_starts_with($imgPath, 'storage/') && !str_starts_with($imgPath, 'http')) {
                        if (file_exists(public_path('assets/images/' . $imgPath))) {
                            $imgPath = 'assets/images/' . $imgPath;
                        } else {
                            $imgPath = 'storage/' . $imgPath;
                        }
                    }
                @endphp
                @if($imgPath && (file_exists(public_path($imgPath)) || str_contains($imgPath, 'http')))
                    <img src="{{ asset($imgPath) }}" class="w-16 h-16 object-cover rounded-xl shadow-sm">
                @else
                    <div class="w-16 h-16 bg-slate-200 rounded-xl flex items-center justify-center">🏕️</div>
                @endif
                <div>
                    <h3 class="font-bold text-slate-800">{{ $detail->product->name ?? $detail->product->nama_produk }}</h3>
                    <p class="text-xs text-slate-500">
                        {{ $detail->type === 'rent' ? 'Durasi Sewa: ' . ($detail->duration ?? 3) . ' Hari' : 'Jumlah Beli: ' . ($detail->qty ?? 1) . ' Item' }}
                    </p>
                </div>
            </div>

            <!-- Pesan Sukses / Error Flash -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 text-emerald-800 rounded-2xl text-sm border border-emerald-100">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 text-rose-800 rounded-2xl text-sm border border-rose-100">
                    {{ session('error') }}
                </div>
            @endif

            <!-- ------------------ STAGE SECTIONS ------------------ -->
            @if($detail->type === 'rent')
                <!-- ================== ALUR SEWA (RENTAL - UNTOUCHED & ORIGINAL) ================== -->
                @if(!$return)
                    <form action="{{ route('orders.return.store', $detail->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Toko (Tujuan Pengembalian)</label>
                            <div class="p-4 bg-emerald-50 text-emerald-800 rounded-2xl text-sm border border-emerald-100">
                                <p class="font-bold text-base">{{ $detail->product->store->nama_toko ?? 'Toko Campify' }}</p>
                                <p class="mt-1 text-slate-600">{{ $detail->product->store->alamat ?? 'Alamat Toko tidak terintegrasi' }}</p>
                                <p class="mt-2 text-xs opacity-75 font-semibold">Toko ini yang bertanggung jawab untuk menerima barang pengembalian.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-[24px] border border-slate-100">
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Biaya Sewa</label>
                                <p class="text-sm font-bold text-slate-800">Rp {{ number_format($detail->harga) }}</p>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black text-emerald-500 uppercase tracking-widest">Dana Jaminan (25% Tersegel)</label>
                                <p class="text-sm font-bold text-emerald-600">Rp {{ number_format($detail->product->buy_price * 0.25) }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Metode Pengembalian</label>
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-2xl cursor-pointer hover:border-emerald-500 transition-colors">
                                    <input type="radio" name="metode_return" value="antar" checked class="text-emerald-600 focus:ring-emerald-500" onchange="toggleKurir(false)">
                                    <div>
                                        <span class="text-sm font-bold block text-slate-800">Antar Langsung</span>
                                        <span class="text-[10px] text-slate-400 block">Selesaikan di tempat seller</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-2xl cursor-pointer hover:border-emerald-500 transition-colors">
                                    <input type="radio" name="metode_return" value="kurir" class="text-emerald-600 focus:ring-emerald-500" onchange="toggleKurir(true)">
                                    <div>
                                        <span class="text-sm font-bold block text-slate-800">Kirim via Kurir</span>
                                        <span class="text-[10px] text-slate-400 block">Kirim paket ke alamat toko</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Kurir Details -->
                        <div id="kurir_details_container" class="hidden space-y-4 bg-slate-50 p-6 rounded-[24px] border border-slate-100">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Input Resi Pengiriman Balik</label>
                                <input type="text" name="resi_return" id="resi_return_input" class="w-full rounded-2xl border-slate-200 p-4 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Masukkan nomor resi ekspedisi (misal: JNE, J&T, SiCepat)">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Foto / Bukti Kondisi Barang Saat Ini</label>
                                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-emerald-400 transition cursor-pointer relative bg-white">
                                    <input type="file" name="foto_kondisi" id="foto_kondisi_input" accept="image/*" class="w-full text-sm text-slate-600" />
                                    <p class="mt-2 text-[10px] text-slate-500">Wajib diisi sebagai bukti dokumentasi barang dikirim balik dalam kondisi aman.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Input Informasi Rekening Bank Pembeli (Untuk Refund Jaminan) -->
                        <div class="bg-slate-50 p-6 rounded-[24px] border border-slate-100 space-y-4">
                            <h4 class="text-sm font-bold text-slate-800">Rekening Pengembalian Dana Jaminan</h4>
                            <p class="text-xs text-slate-500">Uang jaminan sewa Anda akan ditransfer balik oleh Admin ke rekening ini setelah pengembalian barang selesai.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Nama Bank</label>
                                    <input type="text" name="buyer_refund_bank_name" class="w-full text-xs rounded-xl border-slate-200 p-3 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Contoh: BCA, Mandiri, BRI" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Nomor Rekening</label>
                                    <input type="text" name="buyer_refund_bank_account" class="w-full text-xs rounded-xl border-slate-200 p-3 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Nomor rekening tanpa spasi" required>
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Nama Pemilik Rekening</label>
                                    <input type="text" name="buyer_refund_bank_name_owner" class="w-full text-xs rounded-xl border-slate-200 p-3 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Nama lengkap sesuai buku tabungan" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl font-bold shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-1">
                            Ajukan Permintaan Pengembalian
                        </button>
                    </form>
                    <script>
                        function toggleKurir(show) {
                            const container = document.getElementById('kurir_details_container');
                            const resi = document.getElementById('resi_return_input');
                            const foto = document.getElementById('foto_kondisi_input');
                            container.classList.toggle('hidden', !show);
                            resi.required = show;
                            foto.required = show;
                        }
                    </script>

                <!-- ------------------ STAGE 4: SHIPPING (MENUNGGU BARANG DITERIMA SELLER) ------------------ -->
                @elseif($return && $return->status === 'shipping')
                    <div class="text-center py-10">
                        <div class="w-20 h-20 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Paket Sedang Dikirim</h3>
                        <p class="text-slate-500 max-w-md mx-auto text-sm">
                            Nomor resi <strong>{{ $return->resi_return }}</strong> telah diunggah. Menunggu Penjual menerima paket dan memeriksa kondisi fisik barang secara langsung.
                        </p>

                        @if($return->proof_returned_image)
                        <div class="mt-6">
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2 text-left">Foto Kondisi Barang yang Diunggah</p>
                            <img src="{{ asset($return->proof_returned_image) }}" class="w-full max-h-60 object-cover rounded-2xl shadow-sm border border-slate-200">
                        </div>
                        @endif
                    </div>

                <!-- ------------------ STAGE 5: DENDA PENDING (PEMBELI HARUS BAYAR SISA KEKURANGAN) ------------------ -->
                @elseif($return && $return->status === 'denda_pending')
                    <div class="space-y-6">
                        <div class="p-6 bg-amber-50 border border-amber-100 rounded-3xl">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-1">Kekurangan Pembayaran Denda (Defisit)</p>
                                    <p class="text-3xl font-black text-rose-600">Rp {{ number_format($return->deficit) }}</p>
                                </div>
                                <span class="px-3 py-1 bg-amber-200 text-amber-800 rounded-full text-[10px] font-bold uppercase">Harus Ditransfer</span>
                            </div>

                            <div class="space-y-4 pt-4 border-t border-amber-200/50 text-slate-700 text-sm">
                                <p class="font-bold text-slate-800">Mengapa saya harus membayar kekurangan ini?</p>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Denda sewa Anda melebihi jumlah deposit jaminan. Deposit jaminan sebesar <strong>Rp {{ number_format($return->deposit_amount) }}</strong> telah terpotong habis, dan Anda wajib melunasi sisa kekurangannya.
                                </p>
                                
                                <div class="bg-white p-4 rounded-2xl border border-amber-200 space-y-2 text-xs">
                                    <div class="flex justify-between">
                                        <span>Denda Terlambat:</span>
                                        <span class="font-bold text-slate-800">Rp {{ number_format($return->late_fee) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Denda Kerusakan (Seller):</span>
                                        <span class="font-bold text-slate-800">Rp {{ number_format($return->damage_fee) }}</span>
                                    </div>
                                    <div class="flex justify-between border-t pt-2 font-bold">
                                        <span>Total Denda:</span>
                                        <span>Rp {{ number_format($return->total_fines) }}</span>
                                    </div>
                                    <div class="flex justify-between text-emerald-600">
                                        <span>Dipotong Deposit Jaminan:</span>
                                        <span>- Rp {{ number_format($return->deposit_amount) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3 pt-4 border-t border-amber-200/50 mt-4">
                                <p class="text-xs font-bold text-slate-700">Silakan transfer kekurangan ke Rekening Bank Toko:</p>
                                <div class="bg-white p-4 rounded-2xl border border-slate-200">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-[10px] text-slate-500 uppercase font-bold">Bank</p>
                                            <p class="text-sm font-bold text-slate-800">{{ $detail->product->store->bank_name ?? 'BCA' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] text-slate-500 uppercase font-bold">No. Rekening</p>
                                            <p class="text-sm font-bold text-slate-800">{{ $detail->product->store->bank_account_number ?? '-' }}</p>
                                        </div>
                                        <div class="col-span-2">
                                            <p class="text-[10px] text-slate-500 uppercase font-bold">Atas Nama</p>
                                            <p class="text-sm font-bold text-slate-800">{{ $detail->product->store->bank_account_name ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('orders.return.upload-bukti', $return->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Upload Bukti Transfer Pelunasan Denda</label>
                                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-emerald-400 transition bg-slate-50">
                                    <input type="file" name="bukti_denda" accept="image/*" class="w-full text-sm text-slate-600" required />
                                    <p class="mt-2 text-[10px] text-slate-500">Format: JPG/PNG/WEBP (maks 2MB).</p>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl font-bold shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-1">
                                Kirim Bukti Pembayaran
                            </button>
                        </form>
                    </div>

                <!-- ------------------ STAGE 6: DENDA SUBMITTED (MENUNGGU VERIFIKASI) ------------------ -->
                @elseif($return && $return->status === 'denda_submitted')
                    <div class="text-center py-10">
                        <div class="w-20 h-20 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3 3L22 4"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Memverifikasi Bukti Denda</h3>
                        <p class="text-slate-500 max-w-md mx-auto text-sm">
                            Bukti pembayaran sisa denda Anda telah dikirim ke Penjual. Harap tunggu Penjual melakukan pencocokan mutasi rekening mereka.
                        </p>

                        @if($return->bukti_denda)
                        <div class="mt-6">
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2 text-left">Bukti Transfer yang Anda Unggah</p>
                            <img src="{{ asset($return->bukti_denda) }}" class="w-full max-h-60 object-cover rounded-2xl shadow-sm border border-slate-200">
                        </div>
                        @endif
                    </div>

                <!-- ------------------ STAGE 7: COMPLETED (SELESAI TRANSAKSI) ------------------ -->
                @elseif($return && $return->status === 'completed')
                    <div class="text-center py-10">
                        <div class="w-20 h-20 bg-emerald-500 text-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-200">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 mb-2">Pengembalian Selesai!</h3>
                        <p class="text-slate-500 mb-8">Terima kasih telah mengembalikan alat camping dengan tertib. Transaksi sewa Anda kini dinyatakan selesai!</p>
                        
                        <div class="bg-slate-50 p-6 rounded-3xl text-left border border-slate-100">
                            <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest mb-4">Ringkasan Pengembalian</p>
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500">Metode</span>
                                    <span class="font-bold text-slate-800">{{ $return->resi_return === 'DIANTAR_LANGSUNG' ? 'Antar Langsung (Offline)' : 'Kurir' }}</span>
                                </div>
                                @if($return->resi_return !== 'DIANTAR_LANGSUNG')
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500">No. Resi</span>
                                    <span class="font-bold text-slate-800">{{ $return->resi_return }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500">Kondisi Fisik</span>
                                    <span class="font-bold text-emerald-600 uppercase">{{ $return->kondisi_barang }}</span>
                                </div>
                                
                                <div class="flex justify-between text-sm pt-3 border-t border-slate-200">
                                    <span class="text-slate-500">Dana Jaminan Awal</span>
                                    <span class="font-bold text-slate-900">Rp {{ number_format($return->deposit_amount) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500 text-red-500">Total Denda Sewa (-)</span>
                                    <span class="font-bold text-red-600">- Rp {{ number_format($return->total_fines) }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-4 mt-2 border-t-2 border-slate-200 border-dashed">
                                    <span class="text-md font-black text-slate-800 uppercase tracking-widest">Dana Refund Anda</span>
                                    <span class="text-2xl font-black text-emerald-600">Rp {{ number_format($return->to_buyer) }}</span>
                                </div>
                            </div>

                            <!-- Info Pencairan Keuangan Oleh Admin -->
                            <div class="mt-6 p-4 bg-emerald-50 rounded-2xl border border-emerald-100 flex gap-3 items-start">
                                <div class="shrink-0 text-emerald-600 mt-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <div class="space-y-1">
                                    <h5 class="text-xs font-bold text-emerald-950">Status Pencairan Dana Jaminan</h5>
                                    @if($return->refund_disbursed_at)
                                        <p class="text-[11px] text-emerald-800 leading-relaxed">
                                            Admin telah mentransfer dana refund sebesar <strong>Rp {{ number_format($return->to_buyer) }}</strong> ke rekening <strong>{{ $return->buyer_refund_bank_name }} ({{ $return->buyer_refund_bank_account }})</strong> Anda pada <strong>{{ $return->refund_disbursed_at->format('d M Y H:i') }}</strong>.
                                        </p>
                                    @else
                                        <p class="text-[11px] text-slate-600 leading-relaxed">
                                            Pengembalian selesai. Admin sedang memproses transfer balik dana refund sebesar <strong>Rp {{ number_format($return->to_buyer) }}</strong> ke rekening <strong>{{ $return->buyer_refund_bank_name }} ({{ $return->buyer_refund_bank_account }})</strong> Anda.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            @else
                <!-- ================== ALUR JUAL BELI (PEMBELIAN PRODUK) ================== -->
                @if(!$return)
                    <!-- STAGE 1: FORM PENGAJUAN PENGEMBALIAN (PILIHAN AWAL REFUND VS RETUR BARANG) -->
                    <form action="{{ route('orders.return.store', $detail->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="buyReturnForm">
                        @csrf

                        <!-- PILIHAN DI AWAL FORM: REFUND DANA VS RETUR BARANG (TUKAR) -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <label class="block text-sm font-bold text-slate-900">
                                        Pilih Solusi Pengembalian <span class="text-rose-500">*</span>
                                    </label>
                                    <p class="text-xs text-slate-500 mt-0.5">Tentukan bagaimana Anda ingin menyelesaikan komplain ini</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Opsi 1: Refund Dana -->
                                <label class="group relative flex flex-col p-5 rounded-2xl border-2 transition-all duration-200 cursor-pointer shadow-sm hover:shadow-md border-emerald-600 bg-emerald-50/40 ring-4 ring-emerald-500/10" id="card-resolution-refund">
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg shrink-0 group-hover:scale-105 transition-transform">
                                                💸
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <input type="radio" name="resolution_type" value="refund" checked class="text-emerald-600 focus:ring-emerald-500" onchange="toggleResolutionChoice('refund')">
                                                    <span class="text-sm font-bold text-slate-900">Refund Dana</span>
                                                </div>
                                                <span class="text-[11px] font-semibold text-emerald-700">Uang Kembali 100%</span>
                                            </div>
                                        </div>
                                        <span id="check-refund-indicator" class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-sm">
                                            ✓
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-600 leading-relaxed mb-4">
                                        Uang/dana transaksi dikembalikan penuh ke rekening atau e-wallet Anda tanpa kirim unit baru.
                                    </p>
                                    <div class="mt-auto pt-3 border-t border-slate-200/60 space-y-1.5 text-[11px]">
                                        <div class="flex items-center gap-1.5 text-slate-600">
                                            <span class="text-emerald-600 font-bold">✓</span>
                                            <span>Transfer langsung ke rekening/e-wallet</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-slate-600">
                                            <span class="text-emerald-600 font-bold">✓</span>
                                            <span>Status akhir: <strong>Dana Dikembalikan</strong></span>
                                        </div>
                                    </div>
                                </label>

                                <!-- Opsi 2: Retur Barang (Tukar) -->
                                <label class="group relative flex flex-col p-5 rounded-2xl border-2 transition-all duration-200 cursor-pointer shadow-sm hover:shadow-md border-slate-200 bg-white hover:border-slate-300" id="card-resolution-replacement">
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-lg shrink-0 group-hover:scale-105 transition-transform">
                                                🔄
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <input type="radio" name="resolution_type" value="replacement" class="text-emerald-600 focus:ring-emerald-500" onchange="toggleResolutionChoice('replacement')">
                                                    <span class="text-sm font-bold text-slate-900">Retur Barang (Tukar)</span>
                                                </div>
                                                <span class="text-[11px] font-semibold text-slate-500">Tukar Unit Baru</span>
                                            </div>
                                        </div>
                                        <span id="check-replacement-indicator" class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-sm hidden">
                                            ✓
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-600 leading-relaxed mb-4">
                                        Kembalikan barang rusak/cacat ke toko untuk ditukar dengan produk pengganti yang baru & berfungsi baik.
                                    </p>
                                    <div class="mt-auto pt-3 border-t border-slate-200/60 space-y-1.5 text-[11px]">
                                        <div class="flex items-center gap-1.5 text-slate-600">
                                            <span class="text-emerald-600 font-bold">✓</span>
                                            <span>Bebas ongkir (100% ditanggung Penjual)</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-slate-600">
                                            <span class="text-emerald-600 font-bold">✓</span>
                                            <span>Status akhir: <strong>Barang Pengganti Diterima</strong></span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Info Toko & Estimasi -->
                        <div class="p-4 bg-slate-50 text-slate-800 rounded-2xl text-xs border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-sm shrink-0 mt-0.5">
                                    🏪
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Toko Tujuan</span>
                                    <p class="font-bold text-sm text-slate-900">{{ $detail->product->store->nama_toko ?? 'Toko Campify' }}</p>
                                    <p class="text-slate-500 mt-0.5">{{ $detail->product->store->alamat ?? 'Alamat Toko Campify' }}</p>
                                </div>
                            </div>
                            <div class="sm:text-right shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200">
                                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Nilai Transaksi</span>
                                <p class="text-base font-black text-emerald-700">Rp {{ number_format($detail->harga * $detail->qty) }}</p>
                            </div>
                        </div>

                        <!-- Alasan Komplain -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Alasan Pengajuan / Kendala Barang <span class="text-rose-500">*</span></label>
                            <textarea name="alasan_return" rows="3" class="w-full rounded-2xl border-slate-200 p-3.5 focus:ring-emerald-500 focus:border-emerald-500 text-sm bg-white" placeholder="Jelaskan kendala Anda secara detail (misal: barang cacat fisik, robek, komponen tidak lengkap, dll)" required></textarea>
                        </div>

                        <!-- Foto Bukti -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Foto Bukti Kondisi Barang <span class="text-rose-500">*</span></label>
                            <div class="border-2 border-dashed border-slate-200 rounded-2xl p-5 text-center hover:border-emerald-500 transition bg-slate-50/60 cursor-pointer relative">
                                <input type="file" name="foto_kondisi" accept="image/*" class="w-full text-xs text-slate-600 cursor-pointer" required />
                                <p class="mt-2 text-[11px] text-slate-500">Unggah foto jelas bagian barang yang rusak / tidak sesuai (JPG, PNG, WebP, maks. 2MB).</p>
                            </div>
                        </div>

                        <!-- FORM SPESIFIK OPSI: REKENING / E-WALLET (Wajib jika Refund Dana, Cadangan jika Retur Barang) -->
                        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/80 space-y-4" id="section-refund-account">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm">💳</span>
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800" id="account-section-title">Rekening / E-Wallet Tujuan (Refund Dana)</h4>
                                </div>
                                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800" id="account-section-badge">Wajib Diisi</span>
                            </div>
                            <p class="text-xs text-slate-500" id="account-section-desc">
                                Dana pengembalian 100% akan ditransfer langsung oleh Penjual ke rekening/e-wallet ini.
                            </p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Nama Bank / E-Wallet <span class="text-rose-500">*</span></label>
                                    <input type="text" name="buyer_refund_bank_name" id="input_refund_bank" class="w-full text-xs rounded-xl border-slate-200 p-3 focus:ring-emerald-500 focus:border-emerald-500 bg-white" placeholder="Contoh: BCA / Mandiri / GoPay / OVO" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Nomor Rekening / No. HP E-Wallet <span class="text-rose-500">*</span></label>
                                    <input type="text" name="buyer_refund_bank_account" id="input_refund_acc" class="w-full text-xs rounded-xl border-slate-200 p-3 focus:ring-emerald-500 focus:border-emerald-500 bg-white" placeholder="Nomor rekening / No HP tanpa spasi" required>
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Nama Pemilik Akun / Rekening <span class="text-rose-500">*</span></label>
                                    <input type="text" name="buyer_refund_bank_name_owner" id="input_refund_owner" class="w-full text-xs rounded-xl border-slate-200 p-3 focus:ring-emerald-500 focus:border-emerald-500 bg-white" placeholder="Nama lengkap sesuai buku tabungan atau akun e-wallet" required>
                                </div>
                            </div>
                        </div>

                        <!-- FORM SPESIFIK OPSI: PENGIRIMAN PENGEMBALIAN BARANG (Hanya muncul jika Retur Barang) -->
                        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/80 space-y-4 hidden" id="section-replacement-shipping">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm">🚚</span>
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900">Kurir Pengembalian Barang ke Toko</h4>
                                </div>
                                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Bebas Ongkir (Seller Tanggung)</span>
                            </div>
                            <p class="text-xs text-slate-500">
                                Pilih kurir yang akan menjemput atau menerima paket pengembalian Anda. Biaya kirim balik sepenuhnya ditanggung Penjual.
                            </p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Pilihan Ekspedisi Kurir</label>
                                    <select name="kurir_return" id="courier_return_select" class="w-full text-xs rounded-xl border-slate-200 p-3 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                                        <option value="J&T Express">J&T Express (Pickup / Drop-off)</option>
                                        <option value="JNE Express">JNE Express (Reguler)</option>
                                        <option value="SiCepat Ekspres">SiCepat Ekspres (BEST)</option>
                                        <option value="AnterAja">AnterAja</option>
                                        <option value="POS Indonesia">POS Indonesia</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Resi / Booking Pickup</label>
                                    <div class="flex gap-2">
                                        <input type="text" name="resi_return" id="resi_return_input" class="w-full text-xs rounded-xl border-slate-200 p-3 focus:ring-emerald-500 focus:border-emerald-500 bg-white" placeholder="Contoh: JP293108234 (Bisa diisi nanti / generate)">
                                        <button type="button" onclick="openBuyerPickupModal()" class="shrink-0 px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-sm cursor-pointer" title="Jadwalkan Kurir Menjemput Paket">
                                            🚚 Pickup
                                        </button>
                                    </div>
                                    <input type="hidden" name="booking_code_return" id="booking_code_return_input">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl font-bold text-sm shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-0.5 cursor-pointer">
                            Submit Pengajuan Pengembalian
                        </button>
                    </form>

                @elseif($return && $return->status === 'pending')
                    <!-- STAGE 2: MENINJAU KOMPLAIN -->
                    <div class="text-center py-8 space-y-4">
                        <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto shadow-sm">
                            <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800">Menunggu Tinjauan Penjual</h3>
                        <p class="text-slate-500 max-w-md mx-auto text-xs leading-relaxed">
                            Permintaan Anda telah diajukan. Penjual sedang memeriksa rincian masalah dan bukti foto untuk mengonfirmasi kelayakan solusi.
                        </p>
                        
                        <div class="mt-6 bg-slate-50 p-6 rounded-2xl text-left border border-slate-200/70 space-y-3">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Jenis Solusi Diajukan:</span>
                                <span class="px-3 py-1 rounded-full text-xs font-black uppercase {{ $return->resolution_type === 'refund' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ $return->resolution_type === 'refund' ? 'Refund Dana (Uang Kembali)' : 'Retur Barang (Tukar Unit Baru)' }}
                                </span>
                            </div>

                            <div class="grid grid-cols-3 gap-2 text-xs">
                                <span class="text-slate-400">Alasan Masalah:</span>
                                <span class="col-span-2 font-bold text-slate-800">"{{ $return->renter_notes }}"</span>
                                
                                <span class="text-slate-400">Rekening Tujuan:</span>
                                <span class="col-span-2 font-bold text-slate-800">
                                    {{ $return->buyer_refund_bank_name ?: '-' }} - {{ $return->buyer_refund_bank_account ?: '-' }} a/n {{ $return->buyer_refund_bank_name_owner ?: '-' }}
                                </span>

                                @if($return->resolution_type === 'replacement')
                                <span class="text-slate-400">Kurir Pengembalian:</span>
                                <span class="col-span-2 font-bold text-emerald-700">
                                    {{ $return->kurir_return ?? 'J&T Express' }} {{ $return->resi_return ? '(' . $return->resi_return . ')' : '(Belum kirim)' }}
                                </span>
                                @endif
                            </div>

                            @if($return->proof_returned_image)
                            <div class="pt-3 border-t border-slate-200">
                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Bukti Fisik Barang Diunggah:</p>
                                <a href="{{ asset($return->proof_returned_image) }}" target="_blank" class="inline-block rounded-xl overflow-hidden border border-slate-200 shadow-sm max-w-xs">
                                    <img src="{{ asset($return->proof_returned_image) }}" class="w-full max-h-40 object-cover">
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                @elseif($return && $return->status === 'refund_pending')
                    <!-- STAGE: REFUND DANA SEDANG DIPROSES PENJUAL -->
                    <div class="text-center py-8 space-y-4">
                        <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto shadow-sm">
                            <svg class="w-8 h-8 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3 3L22 4"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Pengembalian Dana Sedang Diproses</h3>
                        <p class="text-slate-500 max-w-md mx-auto text-xs leading-relaxed">
                            Penjual telah menyetujui resolusi <strong>Refund Dana</strong>. Saat ini transfer balik dana 100% sedang diproses ke rekening/e-wallet Anda.
                        </p>

                        <div class="bg-rose-50/60 p-5 rounded-2xl border border-rose-200 text-left max-w-md mx-auto space-y-3">
                            <div class="flex justify-between items-center text-xs pb-2 border-b border-rose-200">
                                <span class="text-slate-500">Total Nominal Refund:</span>
                                <span class="text-base font-black text-rose-600">Rp {{ number_format($return->escrow_total) }}</span>
                            </div>
                            <div class="text-xs space-y-1">
                                <span class="text-slate-400">Rekening Tujuan:</span>
                                <p class="font-bold text-slate-800">{{ $return->buyer_refund_bank_name }} - {{ $return->buyer_refund_bank_account }}</p>
                                <p class="text-slate-600">Atas Nama: <strong>{{ $return->buyer_refund_bank_name_owner }}</strong></p>
                            </div>
                        </div>
                    </div>

                @elseif($return && $return->status === 'approved')
                    <!-- STAGE: RETUR DISETUJUI - PEMBELI KIRIM BARANG FISIK KE TOKO -->
                    <div class="space-y-6">
                        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-emerald-700 font-bold text-sm">✓ Pengajuan Retur Disetujui Penjual</span>
                            </div>
                            <p class="text-xs text-emerald-800 leading-relaxed">
                                Penjual menyetujui penukaran barang pengganti. Silakan kirimkan barang fisik ke alamat toko penjual di bawah ini. Ongkir sepenuhnya ditanggung penjual.
                            </p>
                        </div>

                        <!-- Alamat Toko -->
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs">
                            <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Alamat Pengiriman Balik ke Toko</span>
                            <p class="font-bold text-sm text-slate-900 mt-1">{{ $detail->product->store->nama_toko ?? 'Toko Campify' }}</p>
                            <p class="text-slate-600 mt-0.5">{{ $detail->product->store->alamat ?? 'Alamat Toko Campify' }}</p>
                        </div>

                        <!-- Form Input Resi Pengembalian -->
                        <form action="{{ route('orders.return.submit-shipping', $return->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Ekspedisi Kurir</label>
                                    <select name="kurir_return" id="shipping_courier_select" class="w-full text-xs rounded-xl border-slate-200 p-3 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                                        <option value="J&T Express">J&T Express</option>
                                        <option value="JNE Express">JNE Express</option>
                                        <option value="SiCepat Ekspres">SiCepat Ekspres</option>
                                        <option value="AnterAja">AnterAja</option>
                                        <option value="POS Indonesia">POS Indonesia</option>
                                    </select>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-xs font-bold text-slate-700">Nomor Resi Pengembalian <span class="text-rose-500">*</span></label>
                                        <button type="button" onclick="openBuyerPickupModal()" class="text-[10px] font-bold text-emerald-700 hover:underline">
                                            🚚 Booking Pickup Kurir
                                        </button>
                                    </div>
                                    <input type="text" name="resi_return" id="approved_resi_input" class="w-full text-xs rounded-xl border-slate-200 p-3 focus:ring-emerald-500 focus:border-emerald-500 bg-white" placeholder="Contoh: JP293810234 atau gunakan Booking Pickup" required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Foto / Bukti Paket Saat Dikemas <span class="text-rose-500">*</span></label>
                                <input type="file" name="foto_kondisi" accept="image/*" class="w-full text-xs text-slate-500 file:py-2 file:px-3 file:rounded-xl file:border-0 file:bg-emerald-100 file:text-emerald-800 cursor-pointer" required>
                                <p class="text-[10px] text-slate-400 mt-1">Foto bukti paket siap dikirimkan kembali.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                                <textarea name="renter_notes" rows="2" class="w-full rounded-xl border-slate-200 p-3 text-xs" placeholder="Contoh: Paket sudah dibungkus bubble wrap dan siap dijemput kurir."></textarea>
                            </div>

                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3.5 rounded-2xl font-bold text-sm shadow-md shadow-emerald-100 transition-all">
                                Konfirmasi Pengiriman Paket Balik
                            </button>
                        </form>
                    </div>

                @elseif($return && $return->status === 'shipping')
                    <!-- STAGE: BARANG BUYER SEDANG DALAM PERJALANAN KE TOKO -->
                    <div class="space-y-6">
                        <div class="text-center py-6">
                            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                                <svg class="w-8 h-8 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-1">Paket Pengembalian Sedang Menuju Toko</h3>
                            <p class="text-slate-500 text-xs max-w-md mx-auto">
                                Menunggu penjual menerima paket pengembalian Anda. Penjual akan memeriksa isi barang dan segera mengirimkan barang pengganti.
                            </p>
                        </div>

                        <!-- Card Resi 1: Pengembalian (Buyer -> Seller) -->
                        <div class="p-5 bg-white rounded-2xl border-2 border-emerald-500/80 shadow-sm space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
                                <div>
                                    <span class="text-[10px] font-black uppercase text-emerald-600 tracking-wider">Resi Pengembalian (Buyer ke Seller)</span>
                                    <h4 class="text-base font-black text-slate-900 mt-0.5">{{ $return->kurir_return ?? 'J&T Express' }}</h4>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 self-start sm:self-auto">
                                    Sedang Dikirim
                                </span>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold">Nomor Resi:</span>
                                    <p class="text-sm font-mono font-black text-slate-800">{{ $return->resi_return }}</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Ongkir: <strong class="text-emerald-700">Rp 0 (Ditanggung Penjual)</strong></p>
                                </div>
                                <button type="button" onclick="openBuyerTrackingModal('{{ $return->resi_return }}', '{{ $return->kurir_return }}')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-sm cursor-pointer self-start sm:self-auto">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    Lacak Pengiriman (API Kurir)
                                </button>
                            </div>
                        </div>
                    </div>

                @elseif($return && $return->status === 'replacement_shipping')
                    <!-- STAGE: BARANG PENGGANTI DIKIRIM (TAMPILKAN DUA RESI DI SISI BUYER) -->
                    <div class="space-y-6">
                        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-black text-emerald-900">Barang Pengganti Telah Dikirim!</h3>
                                <p class="text-xs text-emerald-800 mt-0.5">Penjual telah mengirimkan unit baru ke alamat Anda. Periksa pelacakan kedua resi di bawah ini.</p>
                            </div>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                        </div>

                        <!-- DUA RESI: RESI PENGEMBALIAN & RESI PENGGANTI -->
                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-400">Rincian Dua Pengiriman Retur</h4>

                            <!-- RESI 1: PENGEMBALIAN (BUYER -> SELLER) -->
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                                <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span class="text-xs font-bold text-slate-800">1. Resi Pengembalian (Buyer ke Seller)</span>
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Diterima Toko</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                    <div>
                                        <p class="text-slate-500">{{ $return->kurir_return ?? 'J&T Express' }} &bull; <span class="font-mono font-bold text-slate-900">{{ $return->resi_return }}</span></p>
                                        <p class="text-[10px] text-slate-400">Beban Ongkir: <strong class="text-slate-700">Ditanggung Penjual</strong></p>
                                    </div>
                                    @if($return->resi_return)
                                    <button type="button" onclick="openBuyerTrackingModal('{{ $return->resi_return }}', '{{ $return->kurir_return }}')" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 underline self-start sm:self-auto">
                                        Lacak Resi Pengembalian →
                                    </button>
                                    @endif
                                </div>
                            </div>

                            <!-- RESI 2: BARANG PENGGANTI (SELLER -> BUYER) -->
                            <div class="p-5 bg-white rounded-2xl border-2 border-emerald-500 shadow-md space-y-3">
                                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                                        <span class="text-xs font-black text-slate-900">2. Resi Barang Pengganti (Seller ke Buyer)</span>
                                    </div>
                                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800">Dalam Perjalanan Kurir</span>
                                </div>
                                
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase">Ekspedisi:</span>
                                        <h5 class="text-sm font-black text-slate-900">{{ $return->kurir_pengganti ?? 'JNE Express' }}</h5>
                                        <p class="text-xs font-mono font-black text-emerald-700 mt-0.5">{{ $return->resi_pengganti }}</p>
                                        <p class="text-[11px] text-slate-500 mt-1">Ongkir Pengganti: <strong class="text-emerald-700">Rp 0 (Ditanggung Penjual)</strong></p>
                                    </div>
                                    <button type="button" onclick="openBuyerTrackingModal('{{ $return->resi_pengganti }}', '{{ $return->kurir_pengganti }}')" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-sm self-start sm:self-auto cursor-pointer">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                        Lacak Barang Pengganti (API Kurir)
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- TOMBOL KONFIRMASI PENERIMAAN BARANG PENGGANTI -->
                        <form action="{{ route('orders.return.confirm-replacement', $return->id) }}" method="POST" class="pt-2">
                            @csrf
                            <button type="submit" onclick="return confirm('Apakah Anda telah menerima barang pengganti dalam kondisi baik?')" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl font-black text-sm shadow-lg shadow-emerald-200 transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Konfirmasi Barang Pengganti Diterima
                            </button>
                            <p class="text-center text-[11px] text-slate-400 mt-2">Klik tombol di atas jika barang pengganti telah sampai dan sesuai.</p>
                        </form>
                    </div>

                @elseif($return && $return->status === 'completed')
                    <!-- STAGE: SELESAI TRANSAKSI RETUR (STATUS AKHIR: DANA DIKEMBALIKAN ATAU BARANG PENGGANTI DITERIMA) -->
                    <div class="text-center py-8 space-y-4">
                        <div class="w-16 h-16 bg-emerald-500 text-white rounded-full flex items-center justify-center mx-auto mb-2 shadow-lg shadow-emerald-200">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        
                        <h3 class="text-2xl font-black text-slate-900">
                            {{ $return->resolution_type === 'refund' ? 'Dana Berhasil Dikembalikan!' : 'Barang Pengganti Diterima!' }}
                        </h3>
                        
                        <p class="text-slate-500 text-xs max-w-md mx-auto leading-relaxed">
                            @if($return->resolution_type === 'refund')
                                Permintaan pengembalian dana 100% sebesar <strong>Rp {{ number_format($return->escrow_total) }}</strong> telah sukses diselesaikan ke rekening Anda.
                            @else
                                Anda telah mengonfirmasi penerimaan barang pengganti dengan baik. Transaksi retur barang telah resmi selesai!
                            @endif
                        </p>

                        <!-- Ringkasan Arsip -->
                        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 text-left text-xs max-w-md mx-auto space-y-2 mt-4">
                            <div class="flex justify-between pb-2 border-b border-slate-200">
                                <span class="text-slate-500">Status Akhir:</span>
                                <span class="font-bold {{ $return->resolution_type === 'refund' ? 'text-rose-600' : 'text-emerald-700' }} uppercase">
                                    {{ $return->resolution_type === 'refund' ? 'Dana Dikembalikan' : 'Barang Pengganti Diterima' }}
                                </span>
                            </div>
                            @if($return->resolution_type === 'refund')
                            <div class="flex justify-between">
                                <span class="text-slate-500">Rekening Tujuan:</span>
                                <span class="font-bold text-slate-800">{{ $return->buyer_refund_bank_name }} ({{ $return->buyer_refund_bank_account }})</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Penerima Dana:</span>
                                <span class="font-bold text-slate-800">{{ $return->buyer_refund_bank_name_owner }}</span>
                            </div>
                            @else
                            <div class="flex justify-between">
                                <span class="text-slate-500">Resi Pengembalian:</span>
                                <span class="font-mono font-bold text-slate-800">{{ $return->resi_return }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Resi Pengganti:</span>
                                <span class="font-mono font-bold text-emerald-700">{{ $return->resi_pengganti }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                @elseif($return && $return->status === 'rejected')
                    <!-- STAGE: KOMPLAIN DITOLAK SELLER -->
                    <div class="text-center py-8 space-y-4">
                        <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Komplain Ditolak oleh Penjual</h3>
                        <p class="text-slate-500 text-xs max-w-md mx-auto">
                            Pengajuan retur barang dinilai tidak valid atau tidak memenuhi syarat garansi oleh penjual.
                        </p>
                        @if($return->owner_notes)
                        <div class="bg-rose-50 p-4 rounded-xl text-left border border-rose-200 text-xs text-rose-800 max-w-md mx-auto">
                            <strong>Alasan Penolakan Toko:</strong> "{{ $return->owner_notes }}"
                        </div>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- ================= MODAL LIVE TRACKING (API KURIR) ================= -->
<div id="buyerTrackingModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">🚚</span>
                <h3 class="font-bold text-sm text-slate-900">Pelacakan Live (API Kurir)</h3>
            </div>
            <button type="button" onclick="closeBuyerTrackingModal()" class="text-slate-400 hover:text-slate-600 text-base font-bold cursor-pointer">✕</button>
        </div>

        <div id="buyerTrackingModalBody" class="space-y-4">
            <div class="text-center py-6 text-slate-400 text-xs">
                <div class="w-6 h-6 border-2 border-emerald-600 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
                Menghubungi API Ekspedisi Kurir...
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL BOOKING PICKUP (API KURIR) ================= -->
<div id="buyerPickupModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">📦</span>
                <h3 class="font-bold text-sm text-slate-900">Booking Pickup Kurir (Jemput Paket)</h3>
            </div>
            <button type="button" onclick="closeBuyerPickupModal()" class="text-slate-400 hover:text-slate-600 text-base font-bold cursor-pointer">✕</button>
        </div>

        <div class="space-y-3 text-xs">
            <p class="text-slate-500">Kurir ekspedisi akan datang langsung ke alamat Anda untuk menjemput paket pengembalian ini.</p>
            
            <div>
                <label class="block font-bold text-slate-700 mb-1">Pilih Ekspedisi</label>
                <select id="pickup_courier_code" class="w-full rounded-xl border-slate-200 p-2.5 text-xs bg-slate-50">
                    <option value="JNT">J&T Express</option>
                    <option value="JNE">JNE Express</option>
                    <option value="SICEPAT">SiCepat Ekspres</option>
                    <option value="ANTERAJA">AnterAja</option>
                    <option value="POS">POS Indonesia</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Jadwal Penjemputan</label>
                <select id="pickup_time_slot" class="w-full rounded-xl border-slate-200 p-2.5 text-xs bg-slate-50">
                    <option value="Hari Ini (14:00 - 17:00 WIB)">Hari Ini (14:00 - 17:00 WIB)</option>
                    <option value="Besok Pagi (09:00 - 12:00 WIB)">Besok Pagi (09:00 - 12:00 WIB)</option>
                    <option value="Besok Siang (13:00 - 16:00 WIB)">Besok Siang (13:00 - 16:00 WIB)</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Catatan Penjemputan</label>
                <input type="text" id="pickup_notes" class="w-full rounded-xl border-slate-200 p-2.5 text-xs" placeholder="Contoh: Titip di pos satpam / rumah pagar hijau">
            </div>

            <div id="pickup_result_box" class="hidden p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-1.5 text-emerald-900">
                <p class="font-bold text-xs" id="pickup_res_title">✓ Booking Berhasil Terjadwal!</p>
                <p class="text-[11px]" id="pickup_res_driver"></p>
                <p class="text-[11px]" id="pickup_res_booking"></p>
            </div>

            <button type="button" onclick="submitBookingPickup()" id="btn-submit-pickup" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-bold text-xs shadow-sm transition">
                Konfirmasi Jadwal Penjemputan (API Kurir)
            </button>
        </div>
    </div>
</div>

<script>
function toggleResolutionChoice(type) {
    const cardRefund = document.getElementById('card-resolution-refund');
    const cardReplacement = document.getElementById('card-resolution-replacement');
    const sectionShipping = document.getElementById('section-replacement-shipping');
    const accountTitle = document.getElementById('account-section-title');
    const accountBadge = document.getElementById('account-section-badge');
    const accountDesc = document.getElementById('account-section-desc');

    const bankInput = document.getElementById('input_refund_bank');
    const accInput = document.getElementById('input_refund_acc');
    const ownerInput = document.getElementById('input_refund_owner');

    const checkRefund = document.getElementById('check-refund-indicator');
    const checkReplacement = document.getElementById('check-replacement-indicator');

    if (type === 'refund') {
        if (cardRefund) {
            cardRefund.className = 'group relative flex flex-col p-5 rounded-2xl border-2 transition-all duration-200 cursor-pointer shadow-sm hover:shadow-md border-emerald-600 bg-emerald-50/40 ring-4 ring-emerald-500/10';
        }
        if (cardReplacement) {
            cardReplacement.className = 'group relative flex flex-col p-5 rounded-2xl border-2 transition-all duration-200 cursor-pointer shadow-sm hover:shadow-md border-slate-200 bg-white hover:border-slate-300';
        }
        if (checkRefund) checkRefund.classList.remove('hidden');
        if (checkReplacement) checkReplacement.classList.add('hidden');

        if (sectionShipping) {
            sectionShipping.classList.add('hidden');
        }
        if (accountTitle) accountTitle.innerText = 'Rekening / E-Wallet Tujuan (Refund Dana)';
        if (accountBadge) {
            accountBadge.innerText = 'Wajib Diisi';
            accountBadge.className = 'text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800';
        }
        if (accountDesc) accountDesc.innerText = 'Dana pengembalian 100% akan ditransfer langsung oleh Penjual ke rekening/e-wallet ini.';
        
        if (bankInput) bankInput.required = true;
        if (accInput) accInput.required = true;
        if (ownerInput) ownerInput.required = true;
    } else {
        if (cardReplacement) {
            cardReplacement.className = 'group relative flex flex-col p-5 rounded-2xl border-2 transition-all duration-200 cursor-pointer shadow-sm hover:shadow-md border-emerald-600 bg-emerald-50/40 ring-4 ring-emerald-500/10';
        }
        if (cardRefund) {
            cardRefund.className = 'group relative flex flex-col p-5 rounded-2xl border-2 transition-all duration-200 cursor-pointer shadow-sm hover:shadow-md border-slate-200 bg-white hover:border-slate-300';
        }
        if (checkReplacement) checkReplacement.classList.remove('hidden');
        if (checkRefund) checkRefund.classList.add('hidden');

        if (sectionShipping) {
            sectionShipping.classList.remove('hidden');
        }
        if (accountTitle) accountTitle.innerText = 'Rekening / E-Wallet Cadangan (Opsional)';
        if (accountBadge) {
            accountBadge.innerText = 'Opsional / Cadangan';
            accountBadge.className = 'text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700';
        }
        if (accountDesc) accountDesc.innerText = 'Diperlukan untuk berjaga-jaga jika stok barang pengganti di toko habis, dana akan otomatis dialihkan ke rekening ini.';
        
        if (bankInput) bankInput.required = false;
        if (accInput) accInput.required = false;
        if (ownerInput) ownerInput.required = false;
    }
}

// Modal Live Tracking
async function openBuyerTrackingModal(waybill, courier) {
    const modal = document.getElementById('buyerTrackingModal');
    const body = document.getElementById('buyerTrackingModalBody');
    if (!modal || !body) return;

    modal.classList.remove('hidden');
    body.innerHTML = `
        <div class="text-center py-6 text-slate-400 text-xs">
            <div class="w-6 h-6 border-2 border-emerald-600 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
            Menghubungi API Ekspedisi Kurir...
        </div>
    `;

    try {
        const res = await fetch('/courier-api/track/' + encodeURIComponent(waybill) + '?courier=' + encodeURIComponent(courier || ''));
        const json = await res.json();
        if (json.success && json.data) {
            const d = json.data;
            let list = '';
            d.history.forEach(item => {
                list += `
                    <li class="relative pl-4 pb-3 border-l-2 border-emerald-500 last:border-l-0 text-xs">
                        <span class="absolute -left-[5px] top-1 w-2 h-2 rounded-full bg-emerald-600"></span>
                        <div class="font-bold text-slate-800">${item.status} - <span class="font-normal text-slate-500">${item.location}</span></div>
                        <div class="text-[11px] text-slate-600 mt-0.5">${item.description}</div>
                        <div class="text-[10px] text-slate-400 mt-0.5">${item.time}</div>
                    </li>
                `;
            });

            body.innerHTML = `
                <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-xs space-y-1">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Ekspedisi:</span>
                        <span class="font-bold text-slate-800">${d.courier}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">No. Resi:</span>
                        <span class="font-mono font-bold text-emerald-700">${d.waybill}</span>
                    </div>
                    <div class="flex justify-between pt-1 border-t border-slate-200">
                        <span class="text-slate-400">Status Terkini:</span>
                        <span class="font-bold text-emerald-700">${d.status_label}</span>
                    </div>
                </div>
                <h5 class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Riwayat Checkpoint Perjalanan:</h5>
                <ul class="space-y-0.5 pl-1 max-h-56 overflow-y-auto">
                    ${list}
                </ul>
            `;
        } else {
            body.innerHTML = `<div class="p-3 bg-amber-50 text-amber-800 rounded-xl text-xs">Informasi pelacakan resi belum tersedia di server ekspedisi.</div>`;
        }
    } catch(e) {
        body.innerHTML = `<div class="p-3 bg-rose-50 text-rose-800 rounded-xl text-xs">Gagal menghubungkan server kurir.</div>`;
    }
}

function closeBuyerTrackingModal() {
    const modal = document.getElementById('buyerTrackingModal');
    if (modal) modal.classList.add('hidden');
}

// Modal Booking Pickup
function openBuyerPickupModal() {
    const modal = document.getElementById('buyerPickupModal');
    if (modal) modal.classList.remove('hidden');
}

function closeBuyerPickupModal() {
    const modal = document.getElementById('buyerPickupModal');
    if (modal) modal.classList.add('hidden');
}

async function submitBookingPickup() {
    const courier = document.getElementById('pickup_courier_code').value;
    const timeSlot = document.getElementById('pickup_time_slot').value;
    const notes = document.getElementById('pickup_notes').value;
    const btn = document.getElementById('btn-submit-pickup');
    const resBox = document.getElementById('pickup_result_box');

    btn.disabled = true;
    btn.innerText = 'Menghubungi API Kurir...';

    try {
        const res = await fetch('{{ route('courier.book-pickup') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                courier: courier,
                time_slot: timeSlot,
                notes: notes,
                address: '{{ auth()->user()->alamat ?? 'Alamat Terdaftar' }}',
                sender_name: '{{ auth()->user()->receiver_name ?: auth()->user()->name }}',
                sender_phone: '{{ auth()->user()->phone ?? '08xxxxxxxx' }}'
            })
        });
        const data = await res.json();
        if (data.success && data.data) {
            const d = data.data;
            const generatedWaybill = 'JP' + Math.floor(1000000000 + Math.random() * 9000000000);

            // Auto-fill inputs in parent form
            const resiInput1 = document.getElementById('resi_return_input');
            const resiInput2 = document.getElementById('approved_resi_input');
            const bkgInput = document.getElementById('booking_code_return_input');

            if (resiInput1) resiInput1.value = generatedWaybill;
            if (resiInput2) resiInput2.value = generatedWaybill;
            if (bkgInput) bkgInput.value = d.booking_code;

            resBox.classList.remove('hidden');
            document.getElementById('pickup_res_title').innerText = '✓ Penjemputan Terjadwal (' + d.courier + ')';
            document.getElementById('pickup_res_driver').innerText = 'Driver Ditugaskan: ' + d.driver.name + ' (' + d.driver.phone + ')';
            document.getElementById('pickup_res_booking').innerText = 'Kode Booking: ' + d.booking_code + ' | Resi: ' + generatedWaybill;

            btn.innerText = '✓ Berhasil Terjadwal';
            setTimeout(() => {
                closeBuyerPickupModal();
            }, 2500);
        } else {
            alert('Gagal membuat jadwal pickup kurir.');
            btn.disabled = false;
            btn.innerText = 'Coba Lagi';
        }
    } catch(e) {
        alert('Terjadi kesalahan jaringan saat booking kurir.');
        btn.disabled = false;
        btn.innerText = 'Coba Lagi';
    }
}
</script>
        </div>
    </div>
</div>

@endsection
