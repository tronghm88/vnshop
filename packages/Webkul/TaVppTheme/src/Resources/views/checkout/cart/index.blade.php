@php
    $cart = cart()->getCart();
    $items = $cart ? $cart->items : collect();
    
    // Fetch applied cart rules
    $cartRuleRepository = app('Webkul\CartRule\Repositories\CartRuleRepository');
    $appliedRuleIds = $cart && $cart->applied_cart_rule_ids ? explode(',', $cart->applied_cart_rule_ids) : [];
    
    $appliedRules = [];
    
    if (!empty($appliedRuleIds)) {
        $appliedRules = $cartRuleRepository->findWhereIn('id', $appliedRuleIds);
    }
@endphp

<x-ta-vpp-theme::layouts>
    <x-slot:title>
        {{ trans('ta-vpp-theme::app.checkout.cart.index.cart') }}
    </x-slot>

    

    <div class="container">
        <x-ta-vpp-theme::breadcrumb name="cart" />
        
        @if ($items->count() > 0)
            <div class="cart-layout">
                <!-- Cart Items Section -->
                <div class="cart-items-section">
                    <div class="cart-header-row">
                        <label class="checkbox-container">
                            <input type="checkbox" id="selectAll" checked />
                            <span class="checkmark"></span>
                        </label>
                        <span></span>
                        <span class="checkbox-label">@lang('shop::app.checkout.cart.index.select-all') ({{ round($cart->items_qty, 0) }} {{ trans('ta-vpp-theme::app.checkout.cart.index.items') }})</span>
                        <span class="cart-header-quantity">@lang('ta-vpp-theme::app.checkout.cart.index.quantity')</span>
                        <span class="cart-header-total">{{ trans('ta-vpp-theme::app.checkout.cart.index.total') }}</span>
                        <span></span>
                    </div>

                        @foreach ($items as $item)
                            @php
                                $product = $item->product;
                                $images = $product->cart_base_image;

                                // Price logic
                                $price = $item->base_price;
                                $regularPrice = $product->cart_regular_price;
                                
                                $discountPercentage = 0;
                                if ($regularPrice > $price) {
                                    $discountPercentage = round((($regularPrice - $price) / $regularPrice) * 100);
                                }
                            @endphp
                            
                            <div class="cart-item-card" data-id="{{ $item->id }}">
                                <label class="checkbox-container">
                                    <input type="checkbox" class="item-checkbox" checked />
                                    <span class="checkmark"></span>
                                </label>
                                
                                <div class="cart-item-image">
                                    <img src="{{ $images['medium_image_url'] }}" alt="{{ $item->name }}" />
                                </div>

                                <div class="cart-item-details">
                                    <h3 class="cart-item-title">
                                        <a href="{{ route('shop.product_or_category.index', $product->url_key) }}">{{ $item->name }}</a>
                                    </h3>
                                    
                                    @if (isset($item->additional['attributes']))
                                        <div class="item-options">
                                            @foreach ($item->additional['attributes'] as $attribute)
                                                <b>{{ $attribute['attribute_name'] }}: </b> {{ $attribute['option_label'] }}<br>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="cart-item-price-row">
                                        <span class="price">{{ core()->currency($item->base_price) }}</span>
                                        
                                        @if ($discountPercentage > 0)
                                            <span class="discount-badge">-{{ $discountPercentage }}%</span>
                                            <strike class="original-price">{{ core()->currency($regularPrice) }}</strike>
                                        @endif
                                    </div>
                                </div>

                                <div class="cart-item-quantity">
                                    <div class="quantity-control">
                                        <button type="button" class="qty-down">−</button>
                                        <input type="number" name="qty[{{ $item->id }}]" value="{{ $item->quantity }}" min="1" max="99" readonly />
                                        <button type="button" class="qty-up">+</button>
                                    </div>
                                </div>

                                <div class="cart-item-total">
                                    <span class="price">{{ core()->currency($item->base_total) }}</span>
                                </div>

                                <button class="cart-item-delete" aria-label="{{ trans('ta-vpp-theme::app.checkout.cart.index.remove') }}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                    @endforeach
                </div>

                <!-- Cart Summary Sidebar -->
                <div class="cart-sidebar">
                    <!-- Promotions -->
                    <div class="cart-promo-card">
                        <div class="promo-header">
                            <i class="fa-solid fa-gift"></i>
                            <span>{{ trans('ta-vpp-theme::app.checkout.cart.index.promotions') }}</span>
                            <a href="#" class="promo-view-more">
                                {{ trans('ta-vpp-theme::app.checkout.cart.index.view-more') }} <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        </div>
                        <div class="promo-content">
                            <form id="coupon-form" method="POST">
                                @csrf
                                <div class="promo-input-group">
                                    <input type="text" name="code" placeholder="{{ trans('ta-vpp-theme::app.checkout.cart.index.coupon-code') }}" value="{{ $cart->coupon_code }}" {{ $cart->coupon_code ? 'readonly' : '' }}>
                                    @if ($cart->coupon_code)
                                        <button type="button" class="btn btn-outline" id="remove-coupon">{{ trans('ta-vpp-theme::app.checkout.cart.index.remove-coupon') }}</button>
                                    @else
                                        <button type="submit" class="btn btn-primary">{{ trans('ta-vpp-theme::app.checkout.cart.index.apply-coupon') }}</button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="cart-summary-card">
                        <div class="summary-row">
                            <span>{{ trans('ta-vpp-theme::app.checkout.cart.summary.sub-total') }}</span>
                            <span class="summary-value">{{ core()->currency($cart->base_sub_total) }}</span>
                        </div>
                        
                        @if ($cart->base_discount_amount > 0)
                            <div class="summary-row">
                                <span>{{ trans('ta-vpp-theme::app.checkout.cart.summary.discount-amount') }}</span>
                                <span class="summary-value">-{{ core()->currency($cart->base_discount_amount) }}</span>
                            </div>
                        @endif

                        @if (count($appliedRules) > 0)
                            <div class="summary-row" style="display: block; margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ddd;">
                                <div style="font-weight: 600; margin-bottom: 5px; color: #e65100;">{{ trans('ta-vpp-theme::app.checkout.cart.index.applied-offers') }}</div>
                                @foreach ($appliedRules as $rule)
                                    <div style="font-size: 13px; color: #2e7d32; display: flex; align-items: center; margin-bottom: 4px;">
                                        <span style="margin-right: 5px;">✓</span>
                                        {{ $rule->name }}
                                        @if ($rule->free_shipping)
                                            <span style="margin-left: 5px; font-size: 11px; background: #e8f5e9; color: #2e7d32; padding: 2px 6px; border-radius: 4px;">{{ trans('ta-vpp-theme::app.checkout.cart.index.free-shipping') }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if ($cart->base_tax_total > 0)
                            <div class="summary-row">
                                <span>{{ trans('ta-vpp-theme::app.checkout.cart.summary.tax') }}</span>
                                <span class="summary-value">{{ core()->currency($cart->base_tax_total) }}</span>
                            </div>
                        @endif

                        <div class="summary-row summary-total">
                            <span>{{ trans('ta-vpp-theme::app.checkout.cart.summary.grand-total') }}</span>
                            <span class="summary-total-value">{{ core()->currency($cart->base_grand_total) }}</span>
                        </div>
                        <a href="{{ route('shop.checkout.onepage.index') }}" class="btn btn-primary btn-checkout">
                            {{ trans('ta-vpp-theme::app.checkout.cart.summary.proceed-to-checkout') }}
                        </a>
                        <p class="summary-note">{{ trans('ta-vpp-theme::app.checkout.cart.index.summary-note') }}</p>
                    </div>
                </div>
            </div>
        @else
            <div class="cart-empty">
                <p>{{ trans('ta-vpp-theme::app.checkout.cart.index.empty-cart') }}</p>
                <a href="{{ route('shop.home.index') }}" class="btn btn-primary">{{ trans('ta-vpp-theme::app.checkout.cart.index.continue-shopping') }}</a>
            </div>
        @endif
    </div>
</x-ta-vpp-theme::layouts>
