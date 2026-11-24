@extends('layouts.app_pembeli')

@section('content')
<div class="py-12 max-w-6xl mx-auto px-4 bg-gray-50 min-h-screen">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight uppercase">Keranjang Belanja</h1>
            <p class="text-sm text-slate-500">Pilih perlengkapan yang ingin Anda bawa berpetualang hari ini.</p>
        </div>
        @if(!$cart->isEmpty())
        <div class="flex items-center gap-3 bg-white px-6 py-4 rounded-3xl shadow-sm border border-gray-200 self-end md:self-auto">
            <input type="checkbox" id="select-all" class="w-6 h-6 rounded border-gray-300 text-green-600 focus:ring-green-500 cursor-pointer">
            <label for="select-all" class="text-sm font-bold text-slate-700 cursor-pointer uppercase tracking-widest">Pilih Semua</label>
        </div>
        @endif
    </div>

    @if($cart->isEmpty())
        <div class="text-center py-24 bg-white rounded-3xl shadow-sm border border-gray-100">
            <div class="mx-auto h-24 w-24 bg-green-50 rounded-full flex items-center justify-center text-5xl mb-6">🛒</div>
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Keranjang Anda Kosong</h2>
            <p class="text-slate-500 mb-8 max-w-xs mx-auto">Sepertinya Anda belum memilih perlengkapan camping. Yuk, mulai jelajahi produk kami!</p>
            <a href="{{ route('produk.index') }}" class="inline-flex items-center gap-3 bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-2xl font-bold text-sm uppercase tracking-widest transition-all">
                Mulai Belanja
            </a>
        </div>
    @else

    <form action="{{ route('checkout.index') }}" method="GET" id="cart-form">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

            <!-- ITEMS LIST -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($cart as $item)
                    @php
                        $price = $item->type === 'buy'
                            ? ($item->product->buy_price ?? 0)
                            : ($item->product->rent_price ?? 0) * $item->duration;
                        $itemTotal = $price * $item->qty;
                    @endphp

                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 transition-all hover:shadow-md flex items-center gap-4 group relative overflow-hidden">
                        <!-- Selection Area -->
                        <div class="flex-shrink-0 flex items-center justify-center pr-1">
                            <input type="checkbox" name="selected_items[]" value="{{ $item->id }}" 
                                   class="cart-checkbox w-6 h-6 rounded border-gray-300 text-green-600 focus:ring-green-500 cursor-pointer"
                                   data-price="{{ $itemTotal }}" checked>
                        </div>

                        <!-- Product Image -->
                        <div class="relative h-24 w-24 flex-shrink-0 overflow-hidden rounded-xl shadow-sm border border-gray-100 bg-gray-50">
                            <img src="{{ $item->product->image_url }}" 
                                 alt="{{ $item->product->name }}"
                                 class="h-full w-full object-cover">
                            <div class="absolute bottom-1 left-1/2 -translate-x-1/2 bg-slate-900/90 text-white text-[8px] font-bold px-2 py-0.5 rounded-full uppercase tracking-tight shadow">
                                {{ $item->type === 'buy' ? 'Beli' : 'Sewa' }}
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col h-full justify-between gap-3">
                                <div>
                                    <div class="flex justify-between items-start gap-2">
                                        <h3 class="text-base font-bold text-slate-900 leading-snug truncate">{{ $item->product->name }}</h3>
                                        <button type="button" onclick="removeItem({{ $item->id }})" class="h-8 w-8 flex-shrink-0 flex items-center justify-center rounded-lg bg-red-50 text-red-400 hover:bg-red-500 hover:text-white transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">
                                        @if($item->type === 'rent')
                                            {{ $item->duration }} HARI • MULAI {{ \Carbon\Carbon::parse($item->start_date)->format('d M Y') }}
                                        @else
                                            PEMBELIAN PERALATAN
                                        @endif
                                    </p>
                                </div>

                                <div class="flex flex-wrap items-end justify-between gap-3">
                                    <!-- Controls -->
                                    <div class="flex flex-col gap-1.5">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Qty</span>
                                            <span class="text-[10px] font-medium {{ ($item->product?->stock ?? 0) > 0 ? 'text-emerald-600' : 'text-rose-500' }}">(Stok: {{ $item->product?->stock ?? 0 }})</span>
                                        </div>
                                        <div class="flex items-center bg-gray-100 rounded-lg p-0.5 border border-gray-200 w-fit">
                                            @if($item->type === 'buy')
                                                <button type="button" onclick="updateQty({{ $item->id }}, {{ $item->qty - 1 }})" class="h-8 w-8 flex items-center justify-center rounded bg-white hover:bg-green-600 hover:text-white transition-all text-slate-600 font-bold text-sm">-</button>
                                                <span class="w-10 text-center font-bold text-xs text-slate-800">{{ $item->qty }}</span>
                                                <button type="button" onclick="updateQty({{ $item->id }}, {{ $item->qty + 1 }})" class="h-8 w-8 flex items-center justify-center rounded bg-white hover:bg-green-600 hover:text-white transition-all text-slate-600 font-bold text-sm">+</button>
                                            @else
                                                <button type="button" onclick="updateDuration({{ $item->id }}, {{ $item->duration - 1 }})" class="h-8 w-8 flex items-center justify-center rounded bg-white hover:bg-green-600 hover:text-white transition-all text-slate-600 font-bold text-sm">-</button>
                                                <div class="flex items-center px-2">
                                                    <span class="font-bold text-xs text-slate-800">{{ $item->duration }}</span>
                                                    <span class="text-[9px] font-bold text-slate-400 uppercase ml-1">Hari</span>
                                                </div>
                                                <button type="button" onclick="updateDuration({{ $item->id }}, {{ $item->duration + 1 }})" class="h-8 w-8 flex items-center justify-center rounded bg-white hover:bg-green-600 hover:text-white transition-all text-slate-600 font-bold text-sm">+</button>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="text-right">
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Subtotal</p>
                                        <p class="text-lg font-bold text-green-600 leading-tight">Rp {{ number_format($itemTotal, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- SUMMARY SIDEBAR (Compact & tidak terlalu panjang) -->
            <div class="h-fit lg:sticky lg:top-24 lg:col-span-1">
                <div class="bg-slate-900 p-5 rounded-2xl shadow-sm text-white border border-slate-800">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-3">
                        <h2 class="text-sm font-bold text-white tracking-wide">Ringkasan Pesanan</h2>
                        <span class="text-xs text-gray-400"><span id="selected-count" class="font-bold text-white">0</span> item</span>
                    </div>

                    <div class="flex items-baseline justify-between mb-4">
                        <span class="text-xs font-medium text-gray-400">Estimasi Total</span>
                        <span id="display-total" class="text-xl font-bold text-emerald-400 tabular-nums">Rp 0</span>
                    </div>

                    <button type="submit" id="checkout-btn" disabled class="w-full bg-emerald-600 disabled:bg-slate-800 disabled:text-slate-600 hover:bg-emerald-500 text-white py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all flex items-center justify-center shadow cursor-pointer">
                        Checkout Terpilih
                    </button>
                </div>
            </div>
        </div>
    </form>
    @endif
</div>

<!-- Hidden form for update/remove actions -->
<form id="action-form" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="_method" id="action-method" value="POST">
    <input type="hidden" name="quantity" id="action-qty">
    <input type="hidden" name="duration" id="action-duration">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.cart-checkbox');
        const displayTotal = document.getElementById('display-total');
        const selectedCount = document.getElementById('selected-count');
        const checkoutBtn = document.getElementById('checkout-btn');

        function updateSummary() {
            let total = 0;
            let count = 0;
            checkboxes.forEach(cb => {
                if(cb.checked) {
                    total += parseFloat(cb.dataset.price);
                    count++;
                }
            });
            displayTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
            selectedCount.textContent = count;
            checkoutBtn.disabled = count === 0;
            
            if(selectAll) {
                selectAll.checked = count === checkboxes.length && checkboxes.length > 0;
            }
        }

        if(selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateSummary();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSummary);
        });

        updateSummary();
    });

    function updateQty(id, val) {
        if(val < 1) return;
        const form = document.getElementById('action-form');
        form.action = `/cart/update/${id}`;
        document.getElementById('action-method').value = 'POST';
        document.getElementById('action-qty').value = val;
        form.submit();
    }

    function updateDuration(id, val) {
        if(val < 1) return;
        const form = document.getElementById('action-form');
        form.action = `/cart/update/${id}`;
        document.getElementById('action-method').value = 'POST';
        document.getElementById('action-duration').value = val;
        form.submit();
    }

    function removeItem(id) {
        if(!confirm('Hapus produk dari keranjang?')) return;
        const form = document.getElementById('action-form');
        form.action = `/cart/remove/${id}`;
        document.getElementById('action-method').value = 'DELETE';
        form.submit();
    }
</script>
@endsection