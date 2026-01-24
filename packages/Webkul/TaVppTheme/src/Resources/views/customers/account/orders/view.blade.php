{{-- Order data is provided by the controller, customer info from auth --}}
@php
    $customer = auth()->guard('customer')->user();
    
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

<x-ta-vpp-theme::layouts>
    <x-slot:title>
        Chi tiết đơn hàng #{{ $order->increment_id }}
    </x-slot>

    <div class="container">
        {{-- Breadcrumb --}}
        <nav class="breadcrumb">
            <a href="{{ route('shop.home.index') }}">Trang chủ</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('shop.customers.account.profile.index') }}">Tài khoản</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('shop.customers.account.orders.index') }}">Đơn hàng của tôi</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Đơn hàng #{{ $order->increment_id }}</span>
        </nav>

        {{-- Account Layout --}}
        <div class="account-layout">
            {{-- Sidebar Navigation --}}
            <aside class="account-sidebar">
                <div class="account-menu">
                    <div class="account-user-info">
                        <div class="user-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="user-details">
                            <h3>{{ $customer->first_name }} {{ $customer->last_name }}</h3>
                            <p>{{ $customer->email }}</p>
                        </div>
                    </div>

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
                {{-- Order Header --}}
                <div class="order-detail-header">
                    <div class="order-title-section">
                        <a href="{{ route('shop.customers.account.orders.index') }}" class="back-btn">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1>Đơn hàng #{{ $order->increment_id }}</h1>
                            <p class="order-date">
                                <i class="fa-regular fa-calendar"></i>
                                Đặt hàng lúc {{ $order->created_at->format('H:i - d/m/Y') }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="order-actions">
                        @if($order->canCancel())
                            <form id="cancelOrderForm" method="POST" action="{{ route('shop.customers.account.orders.cancel', $order->id) }}" style="display: inline;">
                                @csrf
                            </form>
                            <button type="button" class="btn btn-outline" onclick="confirmCancelOrder()">
                                <i class="fa-solid fa-xmark"></i>
                                Hủy đơn hàng
                            </button>
                        @endif
                        
                        @if($order->canReorder() && core()->getConfigData('sales.order_settings.reorder.shop'))
                            <a href="{{ route('shop.customers.account.orders.reorder', $order->id) }}" class="btn btn-primary">
                                <i class="fa-solid fa-rotate-right"></i>
                                Mua lại
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Order Status Timeline --}}
                <div class="order-status-card">
                    <div class="order-status-badge {{ $statusClass }}">
                        {{ $statusLabel }}
                    </div>
                    
                    <div class="order-timeline">
                        <div class="timeline-item {{ $order->created_at ? 'completed' : '' }}">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-title">Đơn hàng đã đặt</div>
                                @if($order->created_at)
                                    <div class="timeline-time">{{ $order->created_at->format('H:i - d/m/Y') }}</div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="timeline-item {{ in_array($order->status, ['processing', 'completed']) ? 'completed' : '' }}">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-title">Đang xử lý</div>
                                @if($order->status === 'processing' || $order->status === 'completed')
                                    <div class="timeline-time">{{ $order->updated_at->format('H:i - d/m/Y') }}</div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="timeline-item {{ $order->status === 'completed' ? 'completed' : '' }}">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-title">Hoàn thành</div>
                                @if($order->status === 'completed')
                                    <div class="timeline-time">{{ $order->updated_at->format('H:i - d/m/Y') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="order-items-card">
                    <h2>Sản phẩm đã đặt</h2>
                    
                    <div class="order-items-list">
                        @foreach($order->items as $item)
                            @php
                                $product = $item->product;
                                $productBaseImage = product_image()->getProductBaseImage($product);
                                $image = $productBaseImage ? $productBaseImage['medium_image_url'] : null;
                            @endphp
                            
                            <div class="order-item">
                                <div class="order-item-image">
                                    @if($image)
                                        <img src="{{ $image }}" alt="{{ $item->name }}">
                                    @else
                                        <div class="no-image">
                                            <i class="fa-solid fa-image"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="order-item-details">
                                    <h3>{{ $item->name }}</h3>
                                    
                                    @if(isset($item->additional['attributes']))
                                        <div class="item-options">
                                            @foreach($item->additional['attributes'] as $attribute)
                                                <span><strong>{{ $attribute['attribute_name'] }}:</strong> {{ $attribute['option_label'] }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    <div class="item-sku">SKU: {{ $item->sku }}</div>
                                </div>
                                
                                <div class="order-item-price">
                                    <div class="item-unit-price">
                                        {{ core()->currency($item->price, $order->order_currency_code) }}
                                    </div>
                                    <div class="item-quantity">x{{ $item->qty_ordered }}</div>
                                </div>
                                
                                <div class="order-item-total">
                                    {{ core()->currency($item->total, $order->order_currency_code) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Order Summary & Address --}}
                <div class="order-detail-grid">
                    {{-- Address Information --}}
                    <div class="order-address-card">
                        <h3>Thông tin giao hàng</h3>
                        
                        @if($order->shipping_address)
                            <div class="address-section">
                                <div class="address-label">
                                    <i class="fa-solid fa-location-dot"></i>
                                    Địa chỉ giao hàng
                                </div>
                                <div class="address-content">
                                    <strong>{{ $order->shipping_address->name }}</strong>
                                    <p>{{ $order->shipping_address->address }}</p>
                                    <p>{{ $order->shipping_address->city }}, {{ $order->shipping_address->state }}</p>
                                    <p>{{ $order->shipping_address->postcode }}</p>
                                    <p>
                                        <i class="fa-solid fa-phone"></i>
                                        {{ $order->shipping_address->phone }}
                                    </p>
                                </div>
                            </div>
                        @endif
                        
                        @if($order->billing_address)
                            <div class="address-section">
                                <div class="address-label">
                                    <i class="fa-solid fa-file-invoice"></i>
                                    Địa chỉ thanh toán
                                </div>
                                <div class="address-content">
                                    <strong>{{ $order->billing_address->name }}</strong>
                                    <p>{{ $order->billing_address->address }}</p>
                                    <p>{{ $order->billing_address->city }}, {{ $order->billing_address->state }}</p>
                                    <p>{{ $order->billing_address->postcode }}</p>
                                    <p>
                                        <i class="fa-solid fa-phone"></i>
                                        {{ $order->billing_address->phone }}
                                    </p>
                                </div>
                            </div>
                        @endif
                        
                        <div class="address-section">
                            <div class="address-label">
                                <i class="fa-solid fa-truck"></i>
                                Phương thức vận chuyển
                            </div>
                            <div class="address-content">
                                {{ $order->shipping_title }}
                            </div>
                        </div>
                        
                        <div class="address-section">
                            <div class="address-label">
                                <i class="fa-solid fa-credit-card"></i>
                                Phương thức thanh toán
                            </div>
                            <div class="address-content">
                                {{ $order->payment_title }}
                            </div>
                        </div>
                    </div>

                    {{-- Order Summary --}}
                    <div class="order-summary-card">
                        <h3>Tóm tắt đơn hàng</h3>
                        
                        <div class="summary-rows">
                            <div class="summary-row">
                                <span>Tạm tính</span>
                                <span>{{ core()->currency($order->sub_total, $order->order_currency_code) }}</span>
                            </div>
                            
                            @if($order->shipping_amount > 0)
                                <div class="summary-row">
                                    <span>Phí vận chuyển</span>
                                    <span>{{ core()->currency($order->shipping_amount, $order->order_currency_code) }}</span>
                                </div>
                            @endif
                            
                            @if($order->discount_amount > 0)
                                <div class="summary-row discount">
                                    <span>Giảm giá</span>
                                    <span>-{{ core()->currency($order->discount_amount, $order->order_currency_code) }}</span>
                                </div>
                            @endif
                            
                            @if($order->tax_amount > 0)
                                <div class="summary-row">
                                    <span>Thuế</span>
                                    <span>{{ core()->currency($order->tax_amount, $order->order_currency_code) }}</span>
                                </div>
                            @endif
                            
                            <div class="summary-divider"></div>
                            
                            <div class="summary-row total">
                                <span>Tổng cộng</span>
                                <span>{{ core()->currency($order->grand_total, $order->order_currency_code) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmCancelOrder() {
            if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?')) {
                document.getElementById('cancelOrderForm').submit();
            }
        }
    </script>
    @endpush
</x-ta-vpp-theme::layouts>
