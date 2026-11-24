@php 
    $routeName = request()->route()?->getName(); 
    $isTransparent = in_array($routeName, ['home', 'buyer.dashboard', 'landing', 'produk.rental', 'produk.index']);
    $expiredRentals = collect();
    if(auth()->check() && auth()->user()->role === 'buyer') {
        $expiredRentals = \App\Models\Pembeli\OrderDetail_pembeli::whereHas('order', function($q) {
            $q->where('user_id', auth()->id())
              ->whereNotIn('status', ['selesai', 'dibatalkan', 'menunggu'])
              ->whereDoesntHave('returns', function($rq) {
                  $rq->whereIn('status', ['pending', 'checking', 'waiting_refund', 'completed']);
              });
        })
        ->where('type', 'rent')
        ->get()
        ->filter(function($detail) {
            if($detail->start_date && $detail->duration) {
                // Tambah durasi ke start date, set end of day, lalu cek apakah sekarang > end date
                $endDate = \Carbon\Carbon::parse($detail->start_date)->addDays($detail->duration)->endOfDay();
                return now()->greaterThan($endDate);
            }
            return false;
        });
    }
@endphp

@if($expiredRentals->count() > 0)
<div id="rentalAlertBanner" class="fixed top-0 left-0 right-0 bg-red-600 text-white text-center text-xs md:text-sm py-2.5 px-4 shadow-md z-[60] flex flex-col md:flex-row justify-center items-center gap-2">
    <span>⚠️ <strong>Peringatan:</strong> Ada {{ $expiredRentals->count() }} barang sewaan yang durasinya telah habis.</span>
    @if($expiredRentals->count() === 1)
        <a href="{{ route('orders.detail', ['id' => $expiredRentals->first()->order_id, 'detail_id' => $expiredRentals->first()->id]) }}" class="underline font-bold hover:text-red-200 bg-white/20 px-3 py-1 rounded-full text-xs">Kembalikan Sekarang</a>
    @else
        <a href="{{ route('profile', ['tab' => 'rentals']) }}" class="underline font-bold hover:text-red-200 bg-white/20 px-3 py-1 rounded-full text-xs">Kembalikan Sekarang</a>
    @endif
</div>
@endif

<nav id="mainNavbar" 
     data-transparent="{{ $isTransparent ? 'true' : 'false' }}"
     class="fixed {{ $expiredRentals->count() > 0 ? 'top-[68px] md:top-[40px]' : 'top-0' }} left-0 right-0 z-50 transition-all duration-300 text-white bg-transparent" 
     style="{{ $isTransparent 
        ? 'background: transparent; backdrop-filter: none; -webkit-backdrop-filter: none; border-bottom: 1px solid transparent; box-shadow: none;' 
        : 'background: rgba(14, 46, 36, 0.95); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 4px 20px rgba(0,0,0,0.15);' }}">
  <div class="max-w-7xl mx-auto px-4 py-3 flex flex-col gap-3">

    <!-- TOP BAR -->
    <div class="flex items-center justify-between">

      <!-- Logo -->
      <a href="{{ auth()->check() ? route('buyer.dashboard') : route('landing') }}" class="flex items-center gap-3">
        <div class="w-10 h-10 bg-forest-500 text-white flex items-center justify-center rounded-lg font-bold text-xl shadow-md">
          C
        </div>
        <span class="font-semibold text-lg text-white tracking-wide">Campify</span>
      </a>

      <!-- Search -->
      <div class="hidden md:flex flex-1 mx-6">
        <form action="{{ route('produk.search') }}" method="GET" class="w-full">
          <div class="w-full bg-white/20 rounded-full px-4 py-2 flex items-center border border-transparent focus-within:border-white/50 transition backdrop-blur-sm">
            <span class="mr-2 text-white">🔍</span>
            <input type="text"
              name="q"
              placeholder="Cari tenda, carrier, atau alat masak..."
              class="bg-transparent w-full focus:outline-none text-sm text-white placeholder-white/70">
          </div>
        </form>
      </div>

      <!-- Icons -->
      <div class="flex items-center gap-4 text-xl text-white">
        <a href="{{ route('chat.index') }}" title="Chat" class="hover:text-forest-300 transition">💬</a>
        <a href="{{ route('wishlist.index') }}" title="Wishlist" class="hover:text-forest-300 transition relative">
          🤍
          @if(auth()->check() && ($wishlistCount ?? 0) > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
              {{ $wishlistCount ?? 0 }}
            </span>
          @endif
        </a>
        <a href="{{ route('cart.index') }}" title="Keranjang" class="hover:text-forest-300 transition relative">
          🛒
          @if(auth()->check() && ($cartCount ?? 0) > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
              {{ $cartCount ?? 0 }}
            </span>
          @endif
        </a>

        <div class="flex items-center gap-2 border-l border-white/30 text-white pl-4">
          @auth
            <div class="relative">
              <button onclick="toggleDropdown()" class="flex items-center gap-2 hover:text-forest-300 transition text-white">
                <div class="w-8 h-8 bg-forest-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow">
                  {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
                <span class="text-xs">▼</span>
              </button>
              
              <!-- Dropdown Menu -->
              <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border hidden text-gray-800 z-50 py-1">
                <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-forest-50 font-medium">Profile</a>
                <a href="{{ route('profile', ['tab' => 'purchases']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-forest-50 font-medium">Pemesanan Produk</a>
                <a href="{{ route('profile', ['tab' => 'rentals']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-forest-50 font-medium">Pemesanan Sewa</a>
                
                <hr class="my-1 border-gray-200">
                <form action="{{ route('logout') }}" method="POST" class="block">
                  @csrf
                  <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium">Logout</button>
                </form>
              </div>
            </div>
          @else
            <a href="{{ route('login') }}" class="text-sm font-medium hover:text-forest-300 transition text-white">Login</a>
          @endauth
        </div>
      </div>
    </div>

    <!-- MENU -->
    <div class="flex items-center gap-6 text-sm font-semibold text-white border-t border-white/20 pt-3">
      <a href="{{ auth()->check() ? route('buyer.dashboard') : route('landing') }}" 
         class="transition-all {{ request()->routeIs('home', 'buyer.dashboard', 'landing') ? 'text-white font-extrabold border-b-2 border-emerald-400 pb-1' : 'text-white/80 hover:text-white' }}">Home</a>
      <a href="{{ route('produk.rental') }}" 
         class="transition-all {{ request()->routeIs('produk.rental*') ? 'text-white font-extrabold border-b-2 border-emerald-400 pb-1' : 'text-white/80 hover:text-white' }}">Sewa</a>
      <a href="{{ route('produk.index') }}" 
         class="transition-all {{ request()->routeIs('produk.index*') ? 'text-white font-extrabold border-b-2 border-emerald-400 pb-1' : 'text-white/80 hover:text-white' }}">Beli</a>
      <a href="{{ route('articles.index') }}" 
         class="transition-all {{ request()->routeIs('articles*') ? 'text-white font-extrabold border-b-2 border-emerald-400 pb-1' : 'text-white/80 hover:text-white' }}">Artikel</a>
      <a href="{{ request()->routeIs('landing') ? '#download-app' : route('landing') . '#download-app' }}" 
         class="text-emerald-300 hover:text-emerald-200 transition-all flex items-center gap-1.5 ml-auto text-xs sm:text-sm bg-white/10 hover:bg-white/20 px-3 py-1 rounded-full border border-emerald-400/30">
        <i data-lucide="smartphone" class="w-3.5 h-3.5"></i>
        <span>Download App</span>
      </a>
    </div>

  </div>
</nav>

<script>
function toggleDropdown() {
  const dropdown = document.getElementById('userDropdown');
  dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
  const dropdown = document.getElementById('userDropdown');
  const button = event.target.closest('button');
  
  if (!button || !button.onclick || typeof button.onclick !== 'function' || button.onclick.toString().indexOf('toggleDropdown') === -1) {
    if (dropdown && !dropdown.classList.contains('hidden')) {
      dropdown.classList.add('hidden');
    }
  }
});

function updateNavbarScroll() {
  const navbar = document.getElementById('mainNavbar');
  if (!navbar) return;
  const isTransparent = navbar.dataset.transparent === 'true';

  if (isTransparent) {
    if (window.scrollY > 20) {
      navbar.style.setProperty('background', 'rgba(14, 46, 36, 0.95)', 'important');
      navbar.style.setProperty('backdrop-filter', 'blur(14px)', 'important');
      navbar.style.setProperty('-webkit-backdrop-filter', 'blur(14px)', 'important');
      navbar.style.setProperty('border-bottom', '1px solid rgba(255, 255, 255, 0.12)', 'important');
      navbar.style.setProperty('box-shadow', '0 10px 25px -5px rgba(0, 0, 0, 0.35)', 'important');
      navbar.classList.remove('bg-transparent');
    } else {
      navbar.style.setProperty('background', 'transparent', 'important');
      navbar.style.setProperty('backdrop-filter', 'none', 'important');
      navbar.style.setProperty('-webkit-backdrop-filter', 'none', 'important');
      navbar.style.setProperty('border-bottom', '1px solid transparent', 'important');
      navbar.style.setProperty('box-shadow', 'none', 'important');
      navbar.classList.add('bg-transparent');
    }
  }
}

window.addEventListener('scroll', updateNavbarScroll);
document.addEventListener('DOMContentLoaded', updateNavbarScroll);
updateNavbarScroll();
</script>
