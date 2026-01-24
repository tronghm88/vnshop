{{-- Orders data is provided by OrdersComposer --}}
<x-ta-vpp-theme::layouts>
    <x-slot:title>
        Đơn hàng của tôi
    </x-slot>

    <div class="container">
        {{-- Breadcrumb --}}
        <nav class="breadcrumb">
            <a href="{{ route('shop.home.index') }}">Trang chủ</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('shop.customers.account.profile.index') }}">Tài khoản</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Đơn hàng của tôi</span>
        </nav>

        {{-- Account Layout --}}
        <div class="account-layout">
            {{-- Sidebar Navigation --}}
            <aside class="account-sidebar">
                <div class="account-menu">
                    <nav class="account-nav">
                        <a href="{{ route('shop.customers.account.profile.index') }}" class="account-nav-item">
                            <i class="fa-solid fa-user"></i>
                            <span>Thông tin tài khoản</span>
                        </a>
                        <a href="{{ route('shop.customers.account.orders.index') }}" class="account-nav-item active">
                            <i class="fa-solid fa-bag-shopping"></i>
                            <span>Đơn hàng của tôi</span>
                        </a>
                        <a href="{{ route('shop.customers.account.addresses.index') }}" class="account-nav-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>Địa chỉ</span>
                        </a>
                        <a href="{{ route('shop.customers.account.reviews.index') }}" class="account-nav-item">
                            <i class="fa-solid fa-star"></i>
                            <span>Đánh giá của tôi</span>
                        </a>
                        <a href="{{ route('shop.customer.session.destroy') }}" class="account-nav-item">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Đăng xuất</span>
                        </a>
                    </nav>
                </div>
            </aside>

            {{-- Main Content --}}
            <div class="account-content">
                <div class="account-content-header">
                    <h1>Đơn hàng của tôi</h1>
                    <p>Quản lý và theo dõi đơn hàng của bạn</p>
                </div>

                @if($orders && $orders->count() > 0)
                    <div class="orders-list">
                        @foreach($orders as $order)
                            @php
                                $statusClass = match($order->status) {
                                    'pending' => 'status-pending',
                                    'processing' => 'status-processing',
                                    'completed' => 'status-completed',
                                    'canceled' => 'status-canceled',
                                    'closed' => 'status-closed',
                                    default => 'status-pending'
                                };
                                
                                $statusLabel = match($order->status) {
                                    'pending' => 'Chờ xử lý',
                                    'processing' => 'Đang xử lý',
                                    'completed' => 'Hoàn thành',
                                    'canceled' => 'Đã hủy',
                                    'closed' => 'Đã đóng',
                                    default => ucfirst($order->status)
                                };
                            @endphp

                            <div class="order-card">
                                <div class="order-card-header">
                                    <div class="order-info">
                                        <div class="order-id">
                                            <i class="fa-solid fa-receipt"></i>
                                            Đơn hàng #{{ $order->increment_id }}
                                        </div>
                                        <div class="order-date">
                                            <i class="fa-regular fa-calendar"></i>
                                            {{ $order->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                    <div class="order-status {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </div>
                                </div>

                                <div class="order-card-body">
                                    {{-- Order Items Preview --}}
                                    <div class="order-items-preview">
                                        @foreach($order->items->take(3) as $item)
                                            @php
                                                $product = $item->product;
                                                $productBaseImage = product_image()->getProductBaseImage($product);
                                                $image = $productBaseImage ? $productBaseImage['medium_image_url'] : null;
                                            @endphp
                                            <div class="order-item-preview">
                                                @if($image)
                                                    <img src="{{ $image }}" alt="{{ $item->name }}">
                                                @else
                                                    <div class="no-image">
                                                        <i class="fa-solid fa-image"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                        
                                        @if($order->items->count() > 3)
                                            <div class="order-item-more">
                                                +{{ $order->items->count() - 3 }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Order Summary --}}
                                    <div class="order-summary">
                                        <div class="order-summary-row">
                                            <span>Tổng sản phẩm:</span>
                                            <strong>{{ $order->items->count() }} sản phẩm</strong>
                                        </div>
                                        <div class="order-summary-row">
                                            <span>Tổng tiền:</span>
                                            <strong class="price">{{ core()->currency($order->grand_total) }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="order-card-footer">
                                    <a href="{{ route('shop.customers.account.orders.view', $order->id) }}" class="btn btn-outline">
                                        <i class="fa-solid fa-eye"></i>
                                        Xem chi tiết
                                    </a>
                                    
                                    @if($order->canReorder() && core()->getConfigData('sales.order_settings.reorder.shop'))
                                        <a href="{{ route('shop.customers.account.orders.reorder', $order->id) }}" class="btn btn-primary">
                                            <i class="fa-solid fa-rotate-right"></i>
                                            Mua lại
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fa-solid fa-bag-shopping"></i>
                        </div>
                        <h3>Chưa có đơn hàng nào</h3>
                        <p>Bạn chưa có đơn hàng nào. Hãy khám phá và mua sắm ngay!</p>
                        <a href="{{ route('shop.home.index') }}" class="btn btn-primary">
                            <i class="fa-solid fa-home"></i>
                            Về trang chủ
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-ta-vpp-theme::layouts>
