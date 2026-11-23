<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('layouts.navbar_pembeli', function ($view) {
            if (auth()->check()) {
                $userId = auth()->id();
                $cartCount = \App\Models\Pembeli\Keranjang_pembeli::where('user_id', $userId)->count();
                $wishlistCount = \App\Models\Pembeli\Wishlist_pembeli::where('user_id', $userId)->count();
                $chatUnreadCount = \App\Models\Message::whereHas('conversation', function ($q) use ($userId) {
                        $q->where('buyer_id', $userId);
                    })
                    ->where('sender_id', '!=', $userId)
                    ->whereNull('read_at')
                    ->count();

                $view->with([
                    'cartCount' => $cartCount,
                    'wishlistCount' => $wishlistCount,
                    'chatUnreadCount' => $chatUnreadCount,
                ]);
            }
        });

        \Illuminate\Support\Facades\View::composer('SellerView.layouts.sidebar_seller', function ($view) {
            if (auth()->check()) {
                $sellerId = auth()->id();
                $sellerChatUnreadTotal = \App\Models\Message::whereHas('conversation', function ($q) use ($sellerId) {
                        $q->where('seller_id', $sellerId);
                    })
                    ->where('sender_id', '!=', $sellerId)
                    ->whereNull('read_at')
                    ->count();

                $sellerPendingOrdersCount = \App\Models\SellerOrder::where(function ($query) use ($sellerId) {
                        $query->where('seller_id', $sellerId)
                            ->orWhereHas('store', fn ($store) => $store->where('user_id', $sellerId));
                    })
                    ->whereIn('status', ['pending', 'menunggu'])
                    ->whereDoesntHave('order', function ($oq) {
                        $oq->whereIn('status', ['selesai', 'delivered', 'completed', 'dibatalkan', 'cancelled', 'dikirim', 'shipped']);
                    })
                    ->count();

                $view->with([
                    'sellerChatUnreadTotal' => $sellerChatUnreadTotal,
                    'sellerPendingOrdersCount' => $sellerPendingOrdersCount,
                ]);
            }
        });

        \Illuminate\Support\Facades\View::composer(['SellerView.layouts.app_seller', 'SellerView.layouts.navbar_seller'], function ($view) {
            if (auth()->check()) {
                $sellerId = auth()->id();

                $newOrders = \App\Models\SellerOrder::with(['order.buyer', 'items.product'])
                    ->where(function ($query) use ($sellerId) {
                        $query->where('seller_id', $sellerId)
                            ->orWhereHas('store', fn ($store) => $store->where('user_id', $sellerId));
                    })
                    ->whereIn('status', ['pending', 'menunggu'])
                    ->whereDoesntHave('order', function ($oq) {
                        $oq->whereIn('status', ['selesai', 'delivered', 'completed', 'dibatalkan', 'cancelled', 'dikirim', 'shipped']);
                    })
                    ->latest()
                    ->take(5)
                    ->get();

                $newOrdersCount = \App\Models\SellerOrder::where(function ($query) use ($sellerId) {
                        $query->where('seller_id', $sellerId)
                            ->orWhereHas('store', fn ($store) => $store->where('user_id', $sellerId));
                    })
                    ->whereIn('status', ['pending', 'menunggu'])
                    ->whereDoesntHave('order', function ($oq) {
                        $oq->whereIn('status', ['selesai', 'delivered', 'completed', 'dibatalkan', 'cancelled', 'dikirim', 'shipped']);
                    })
                    ->count();

                $newRentalsCount = \App\Models\SellerModels\Rental_seller::whereHas('product', function ($query) use ($sellerId) {
                        $query->where('user_id', $sellerId)
                            ->orWhereHas('store', fn ($store) => $store->where('user_id', $sellerId));
                    })
                    ->where('status', 'pending')
                    ->whereDoesntHave('order', function ($oq) {
                        $oq->whereIn('status', ['selesai', 'delivered', 'completed', 'dibatalkan', 'cancelled']);
                    })
                    ->count();

                $totalNewOrders = $newOrdersCount + $newRentalsCount;

                $view->with([
                    'sellerNewOrders' => $newOrders,
                    'sellerNewOrdersCount' => $totalNewOrders,
                    'sellerPendingOrdersCount' => $newOrdersCount,
                    'sellerPendingRentalsCount' => $newRentalsCount,
                ]);
            }
        });
    }
}
