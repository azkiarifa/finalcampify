<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campify Seller - Marketplace Outdoor</title>

    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary-emerald: #10B981;
            --dark-emerald: #059669;
            --soft-emerald: #ecfdf5;
            --bg-light: #f8fafc;
            --text-main: #1e293b;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-main);
            overflow-x: hidden;
        }

        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1040;
            overflow-y: auto;
            background-color: #ffffff;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.04);
        }

        .main-content {
            margin-left: 280px;
            padding: 40px;
            min-height: 100vh;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1), padding 0.3s ease;
        }

        .card-modern {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .btn-emerald {
            background: linear-gradient(135deg, var(--primary-emerald) 0%, var(--dark-emerald) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 10px 24px;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);
            transition: all 0.2s ease;
        }

        .btn-emerald:hover {
            transform: scale(1.02);
            color: white;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(229, 231, 235, 0.6);
            position: sticky;
            top: 0;
            z-index: 999;
            margin-left: 280px;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .alert-modern {
            border: none;
            border-radius: 15px;
            padding: 1rem 1.5rem;
        }

        /* Desktop: Collapsed State */
        @media (min-width: 993px) {
            body.sidebar-collapsed .sidebar {
                transform: translateX(-100%);
            }
            body.sidebar-collapsed .main-content {
                margin-left: 0;
            }
            body.sidebar-collapsed .glass-nav {
                margin-left: 0;
            }
        }

        /* Mobile & Tablet: Drawer State */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            body.sidebar-open .sidebar {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            .glass-nav {
                margin-left: 0;
                padding: 12px 20px !important;
            }
        }

        /* Backdrop overlay for mobile */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(2px);
            z-index: 1030;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        body.sidebar-open .sidebar-backdrop {
            display: block;
            opacity: 1;
        }

        #sidebarToggleBtn {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        #sidebarToggleBtn:hover {
            background-color: var(--soft-emerald) !important;
            color: var(--primary-emerald) !important;
            border-color: var(--primary-emerald) !important;
            transform: scale(1.05);
        }

        /* Global Pagination & SVG Safety */
        svg.w-5, svg.h-5, .pagination svg {
            width: 1rem !important;
            height: 1rem !important;
            max-width: 1rem !important;
            max-height: 1rem !important;
            display: inline-block;
            vertical-align: middle;
        }

        .pagination-container nav {
            width: 100%;
        }
        .pagination-container p {
            margin-bottom: 0 !important;
        }
        .pagination {
            margin: 0;
            gap: 6px;
            display: flex;
            align-items: center;
        }
        .page-item {
            display: inline-flex;
        }
        .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            color: #475569;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px !important;
            font-size: 0.875rem;
            font-weight: 600;
            line-height: 1;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .page-link:hover {
            background-color: #f8fafc;
            color: #059669;
            border-color: #cbd5e1;
        }
        .page-item.active .page-link {
            background-color: #10B981 !important;
            border-color: #10B981 !important;
            color: #ffffff !important;
            font-weight: 700;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.25);
        }
        .page-item.disabled .page-link {
            color: #94a3b8 !important;
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
            cursor: not-allowed;
        }
    </style>
</head>

<body>

    {{-- SIDEBAR --}}
    @include('SellerView.layouts.sidebar_seller')

    {{-- SIDEBAR BACKDROP (MOBILE) --}}
    <div id="sidebarBackdrop" class="sidebar-backdrop"></div>

    {{-- TOP NAVBAR --}}
    <nav class="navbar glass-nav p-3 px-4 px-lg-5 mb-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button type="button" id="sidebarToggleBtn" class="btn btn-light rounded-circle shadow-sm border p-0 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;" title="Buka / Tutup Sidebar">
                    <i class="bi bi-list fs-4 text-dark"></i>
                </button>
                <a href="{{ route('seller.dashboard') }}" class="text-decoration-none d-flex align-items-center gap-2">
                    <span class="m-0 fw-bold fs-4" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; letter-spacing: -0.5px;">CAMPIFY.</span>
                    <span class="badge bg-emerald-soft text-emerald px-2 py-0.5 rounded-pill fw-semibold text-uppercase d-none d-sm-inline-block" style="font-size: 0.65rem;">Seller</span>
                </a>
            </div>

            <div class="nav-actions d-flex align-items-center gap-3">
                {{-- NOTIFICATION BELL DROPDOWN --}}
                <div class="dropdown">
                    <button class="btn btn-link text-muted p-1 position-relative fs-5 text-decoration-none shadow-none border-0" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi Pesanan">
                        <i class="bi bi-bell fs-4"></i>
                        @if(($sellerNewOrdersCount ?? 0) > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size: 0.65rem;">
                                {{ ($sellerNewOrdersCount ?? 0) > 99 ? '99+' : ($sellerNewOrdersCount ?? 0) }}
                            </span>
                        @endif
                    </button>
                    
                    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 mt-3" aria-labelledby="notificationDropdown" style="width: 360px; max-width: 90vw; z-index: 1050;">
                        <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center rounded-top-4">
                            <div>
                                <h6 class="fw-bold m-0 text-dark">Notifikasi Pesanan</h6>
                                <small class="text-muted">{{ ($sellerNewOrdersCount ?? 0) > 0 ? ($sellerNewOrdersCount ?? 0) . ' pesanan baru perlu diproses' : 'Semua pesanan sudah diproses' }}</small>
                            </div>
                            @if(($sellerNewOrdersCount ?? 0) > 0)
                                <span class="badge bg-danger rounded-pill px-2 py-1 small">Baru</span>
                            @endif
                        </div>

                        <div class="notification-list p-2" style="max-height: 380px; overflow-y: auto;">
                            @if(isset($sellerNewOrders) && $sellerNewOrders->count() > 0)
                                @foreach($sellerNewOrders as $newOrder)
                                    @php
                                        $buyerName = $newOrder->order?->buyer?->name ?? $newOrder->order?->receiver_name ?? 'Pelanggan';
                                        $firstProduct = $newOrder->items->first()?->product?->nama_produk ?? $newOrder->items->first()?->product?->name ?? 'Produk Outdoor';
                                        $itemCount = $newOrder->items->count();
                                    @endphp
                                    <a href="{{ route('seller.orders.show', $newOrder->id) }}" class="dropdown-item p-3 rounded-3 mb-1 text-wrap d-flex align-items-start gap-3 hover-light">
                                        <div class="avatar-icon bg-emerald-subtle text-emerald rounded-circle p-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                            <i class="bi bi-bag-check-fill fs-5"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="fw-bold text-dark small text-truncate" style="max-width: 140px;">{{ $buyerName }}</span>
                                                <small class="text-muted" style="font-size: 0.7rem;">{{ $newOrder->created_at?->diffForHumans() }}</small>
                                            </div>
                                            <p class="m-0 text-muted small" style="line-height: 1.3;">
                                                {{ Str::limit($firstProduct, 30) }} {{ $itemCount > 1 ? '(+' . ($itemCount - 1) . ' item lain)' : '' }}
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <span class="fw-bold text-emerald small">Rp {{ number_format($newOrder->subtotal ?: ($newOrder->order?->total_amount ?? 0), 0, ',', '.') }}</span>
                                                <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.65rem;">Perlu Diproses</span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                <div class="p-4 text-center text-muted">
                                    <div class="fs-1 opacity-25 mb-2">🎉</div>
                                    <p class="small fw-semibold m-0">Tidak ada pesanan baru</p>
                                    <small class="text-muted">Pesanan baru akan muncul di sini saat pembeli melakukan checkout.</small>
                                </div>
                            @endif
                        </div>

                        <div class="p-2 border-top bg-light rounded-bottom-4 d-flex justify-content-between gap-1">
                            <a href="/seller/orders" class="btn btn-sm btn-link text-emerald fw-bold text-decoration-none w-50 text-center py-2">
                                <i class="bi bi-receipt me-1"></i>Pesanan Baru
                            </a>
                            <a href="/seller/rentals" class="btn btn-sm btn-link text-muted fw-bold text-decoration-none w-50 text-center py-2">
                                <i class="bi bi-calendar-range me-1"></i>Penyewaan
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="vr mx-2 opacity-10"></div>
                
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm border-0 rounded-pill px-3">
                        <i class="bi bi-box-arrow-right me-1"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- CONTENT --}}
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-modern alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-modern alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Open/Close Toggle Logic
            const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
            const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');

            function toggleSidebar() {
                if (window.innerWidth >= 993) {
                    document.body.classList.toggle('sidebar-collapsed');
                    const isCollapsed = document.body.classList.contains('sidebar-collapsed');
                    localStorage.setItem('seller_sidebar_collapsed', isCollapsed ? '1' : '0');
                } else {
                    document.body.classList.toggle('sidebar-open');
                }
            }

            function closeSidebarMobile() {
                document.body.classList.remove('sidebar-open');
            }

            if (sidebarToggleBtn) {
                sidebarToggleBtn.addEventListener('click', toggleSidebar);
            }
            if (sidebarCloseBtn) {
                sidebarCloseBtn.addEventListener('click', closeSidebarMobile);
            }
            if (sidebarBackdrop) {
                sidebarBackdrop.addEventListener('click', closeSidebarMobile);
            }

            // Restore user's desktop collapsed preference on load
            if (window.innerWidth >= 993 && localStorage.getItem('seller_sidebar_collapsed') === '1') {
                document.body.classList.add('sidebar-collapsed');
            }

            window.addEventListener('resize', function() {
                if (window.innerWidth >= 993) {
                    document.body.classList.remove('sidebar-open');
                }
            });
        });
    </script>
</body>
</html>
