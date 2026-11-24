@extends('layouts.app_pembeli')

@section('content')
<div class="pt-8 pb-16 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Chat Penjual</h1>
            <p class="text-sm text-slate-500">Percakapan dibuat dari produk yang kamu pilih.</p>
        </div>

        <div class="grid gap-6 xl:grid-cols-[340px_1fr]">
            <aside class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200 h-fit">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Percakapan</p>
                        <p class="text-xs text-slate-500">{{ $conversations->count() }} thread</p>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse($conversations as $item)
                        @php
                            $last = $item->latestMessage;
                            $active = isset($conversation) && $conversation?->id === $item->id;
                            $sellerName = $item->seller?->store?->nama_toko ?? $item->seller?->name ?? 'Penjual';
                        @endphp
                        <a href="{{ route('chat.show', $item) }}" class="block rounded-3xl border p-4 transition {{ $active ? 'border-green-400 bg-green-50' : 'border-slate-200 bg-slate-50 hover:border-green-300' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="font-semibold text-slate-900 truncate">{{ $sellerName }}</p>
                                        @if(($item->unread_count ?? 0) > 0)
                                            <span class="bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">
                                                {{ $item->unread_count }} baru
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 truncate">{{ $item->product?->name ?? 'Produk' }}</p>
                                    <p class="mt-2 text-xs truncate {{ ($item->unread_count ?? 0) > 0 ? 'font-bold text-slate-800' : 'text-slate-500' }}">
                                        {{ $last ? (($last->sender_id === \Illuminate\Support\Facades\Auth::id() ? 'Kamu: ' : '') . $last->message) : 'Belum ada pesan' }}
                                    </p>
                                </div>
                                <span class="text-xs text-slate-400 whitespace-nowrap">{{ $last?->created_at?->format('H:i') }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-3xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-500">
                            Belum ada chat. Buka produk lalu klik Hubungi Penjual.
                        </div>
                    @endforelse
                </div>
            </aside>

            <section class="flex flex-col h-[calc(100vh-220px)] overflow-hidden rounded-3xl bg-white shadow-sm border border-slate-200">
                @if($conversation)
                    @php
                        $sellerName = $conversation->seller?->store?->nama_toko ?? $conversation->seller?->name ?? 'Penjual';
                    @endphp
                    <div class="border-b px-6 py-5 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-slate-900">{{ $sellerName }}</p>
                                @if(isset($conversation->seller) && method_exists($conversation->seller, 'isOnline') && $conversation->seller->isOnline())
                                    <span class="inline-flex items-center gap-1 text-[11px] text-green-600 font-medium bg-green-50 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Online
                                    </span>
                                @else
                                    <span class="text-[11px] text-slate-400">
                                        Offline {{ (isset($conversation->seller) && $conversation->seller->last_seen) ? '• ' . $conversation->seller->last_seen : '' }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500">{{ $conversation->product?->name ?? 'Produk' }}</p>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto px-6 py-6 space-y-4 bg-slate-50">
                        @forelse($messages as $msg)
                            <div class="flex {{ $msg->sender_id === \Illuminate\Support\Facades\Auth::id() ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-xl rounded-3xl p-4 text-sm leading-relaxed shadow-sm {{ $msg->sender_id === \Illuminate\Support\Facades\Auth::id() ? 'bg-green-600 text-white rounded-br-none' : 'bg-white border border-slate-200 rounded-bl-none text-slate-800' }}">
                                    @if($loop->first && $conversation->product)
                                        <div class="mb-3 rounded-2xl bg-white p-3 shadow-sm border border-slate-200/80 text-slate-800">
                                            <div class="flex items-center gap-3">
                                                <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-xl border border-slate-100 bg-slate-100">
                                                    <img src="{{ $conversation->product->image_url }}" alt="{{ $conversation->product->name }}" class="h-full w-full object-cover">
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <span class="inline-block rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 mb-1">
                                                        {{ $conversation->product->is_rental ? 'Sewa' : 'Beli' }}
                                                    </span>
                                                    <h4 class="truncate text-sm font-bold text-slate-900 leading-tight">{{ $conversation->product->name }}</h4>
                                                    <p class="text-xs font-semibold text-green-600 mt-1">
                                                        @if($conversation->product->is_rental)
                                                            Rp {{ number_format($conversation->product->rent_price ?? 0, 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">/hari</span>
                                                        @else
                                                            Rp {{ number_format($conversation->product->buy_price ?? $conversation->product->price ?? 0, 0, ',', '.') }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <a href="{{ route('produk.detail', $conversation->product_id) }}" target="_blank" class="flex-shrink-0 rounded-xl bg-slate-100 hover:bg-slate-200 px-3 py-2 text-xs font-medium text-slate-700 transition">
                                                    Lihat
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                    <p>{{ $msg->message }}</p>
                                    <div class="mt-3 text-[11px] {{ $msg->sender_id === \Illuminate\Support\Facades\Auth::id() ? 'text-green-100' : 'text-slate-400' }} text-right">{{ $msg->created_at->format('H:i') }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="mx-auto max-w-xl rounded-3xl border border-dashed border-slate-200 bg-white p-10 text-center">
                                <p class="font-semibold text-slate-900">Mulai percakapan</p>
                                <p class="mt-2 text-sm text-slate-500">Tanyakan stok, ukuran, atau detail produk.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="border-t px-6 py-5 bg-white">
                        <form action="{{ route('chat.conversation.send', $conversation) }}" method="POST" class="flex gap-3">
                            @csrf
                            <input type="text" name="message" placeholder="Tulis pesan..." class="flex-1 rounded-2xl border border-slate-200 px-4 py-3 focus:border-green-500 focus:outline-none" required>
                            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-green-600 px-6 py-3 text-sm font-semibold text-white hover:bg-green-700">Kirim</button>
                        </form>
                    </div>
                @else
                    <div class="flex-1 grid place-items-center bg-slate-50 p-8 text-center">
                        <div>
                            <p class="text-lg font-semibold text-slate-900">Pilih percakapan</p>
                            <p class="mt-2 text-sm text-slate-500">Atau mulai chat dari halaman produk.</p>
                        </div>
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection
