<x-ta-vpp-theme::layouts>
    <x-slot:title>
        @lang('shop::app.customers.account.reviews.title')
    </x-slot>

    <div class="container">
        {{-- Breadcrumb --}}
        <nav class="breadcrumb">
            <a href="{{ route('shop.home.index') }}">Trang chủ</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Tài khoản</span>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Đánh giá của tôi</span>
        </nav>

        <div class="account-layout">
            {{-- Sidebar --}}
            <aside class="account-sidebar">
                <div class="account-menu">
                    <nav class="account-nav">
                        <a href="{{ route('shop.customers.account.profile.index') }}" class="account-nav-item">
                            <i class="fa-solid fa-user"></i>
                            <span>Thông tin tài khoản</span>
                        </a>
                        <a href="{{ route('shop.customers.account.orders.index') }}" class="account-nav-item">
                            <i class="fa-solid fa-bag-shopping"></i>
                            <span>Đơn hàng của tôi</span>
                        </a>
                        <a href="{{ route('shop.customers.account.addresses.index') }}" class="account-nav-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>Địa chỉ</span>
                        </a>
                        <a href="{{ route('shop.customers.account.reviews.index') }}" class="account-nav-item active">
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

            {{-- Content --}}
            <div class="account-content">
                <div class="account-content-header">
                    <h1>@lang('shop::app.customers.account.reviews.title')</h1>
                    <p>Xem lại lịch sử đánh giá sản phẩm của bạn</p>
                </div>

                @if (! $reviews->isEmpty())
                    <div class="reviews-list" style="display: flex; flex-direction: column; gap: 16px;">
                        @foreach($reviews as $review)
                            <div class="review-card" style="display: flex; gap: 20px; border: 1px solid var(--border); border-radius: 12px; padding: 20px; background: #fff; align-items: flex-start;">
                                <div class="review-image" style="width: 100px; height: 100px; flex-shrink: 0; border-radius: 8px; overflow: hidden; border: 1px solid var(--border); background: #f9f9f9;">
                                    <a href="{{ route('shop.product_or_category.index', $review->product->url_key) }}">
                                        <img 
                                            src="{{ $review->product->base_image_url ?? bagisto_asset('images/small-product-placeholder.webp') }}" 
                                            alt="{{ $review->product->name }}"
                                            style="width: 100%; height: 100%; object-fit: cover;"
                                        >
                                    </a>
                                </div>
                                
                                <div class="review-details" style="flex: 1; min-width: 0;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                                        <h3 style="margin: 0; font-size: 16px; font-weight: 600; line-height: 1.4;">
                                            <a href="{{ route('shop.product_or_category.index', $review->product->url_key) }}" style="color: var(--text); text-decoration: none; transition: color 0.2s;">
                                                {{ $review->title }}
                                            </a>
                                        </h3>
                                        <div class="stars" style="color: #f6a623; white-space: nowrap; font-size: 14px;">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if($review->rating >= $i)
                                                    <i class="fa-solid fa-star"></i>
                                                @else
                                                    <i class="fa-regular fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>

                                    <div style="font-size: 13px; color: var(--muted); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                                        <i class="fa-regular fa-calendar"></i>
                                        {{ $review->created_at->format('d/m/Y') }}
                                    </div>

                                    <p style="margin: 0; color: var(--text); line-height: 1.6; font-size: 14px;">
                                        {{ $review->comment }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Pagination --}}
                    {{ $reviews->appends(request()->query())->links() }}
                @else
                     <div class="empty-state">
                         <div class="empty-icon"><i class="fa-solid fa-star"></i></div>
                         <h3>@lang('shop::app.customers.account.reviews.empty-review')</h3>
                         <p>Bạn chưa có đánh giá nào cho sản phẩm.</p>
                         <a href="{{ route('shop.home.index') }}" class="btn btn-primary" style="margin-top: 16px; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-cart-shopping"></i>
                            Mua sắm ngay
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .review-card a:hover {
            color: var(--primary) !important;
        }
        @media (max-width: 576px) {
            .review-card {
                flex-direction: column;
                gap: 12px !important;
            }
            .review-image {
                width: 60px !important;
                height: 60px !important;
            }
        }
    </style>
</x-ta-vpp-theme::layouts>
