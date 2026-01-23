{{-- Header Component --}}
<header class="site-header">
    {{-- Header Main --}}
    <div class="header-main">
        <div class="container header-main-inner">
            <a class="logo" href="{{ route('shop.home.index') }}">
                @if ($logo = core()->getCurrentChannel()->logo_url)
                    <img src="{{ $logo }}" alt="{{ config('app.name') }}" style="max-height: 40px;">
                @else
                    VPP Shop
                @endif
            </a>

            <x-ta-vpp-theme::search-bar />

            <div class="header-actions">
                <a class="icon-link" href="{{ route('shop.checkout.cart.index') }}">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Giỏ hàng</span>
                    @php $cart = cart()->getCart(); @endphp
                    <span class="badge-count">{{ $cart ? round($cart->items_qty, 0) : 0 }}</span>
                </a>
                
                @guest('customer')
                    <a class="icon-link" href="{{ route('shop.customer.session.index') }}">
                        <i class="fa-solid fa-user"></i>
                        <span>Tài khoản</span>
                    </a>
                @endguest

                @auth('customer')
                    <div class="user-dropdown">
                        <a class="icon-link" href="javascript:void(0)">
                            <i class="fa-solid fa-user"></i>
                            <span>{{ auth()->guard('customer')->user()->first_name }} <i class="fa-solid fa-chevron-down" style="font-size: 10px; margin-left: 4px;"></i></span>
                        </a>
                        <div class="dropdown-menu">
                            <a href="{{ route('shop.customers.account.profile.index') }}"><i class="fa-solid fa-user-gear"></i> Hồ sơ của tôi</a>
                            <a href="{{ route('shop.customers.account.orders.index') }}"><i class="fa-solid fa-box"></i> Đơn hàng của tôi</a>
                            <hr>
                            <a href="#" onclick="event.preventDefault(); document.getElementById('customerLogout').submit();" class="logout">
                                <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                            </a>
                            <form id="customerLogout" action="{{ route('shop.customer.session.destroy') }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>
