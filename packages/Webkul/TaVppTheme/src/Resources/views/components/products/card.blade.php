@props(['product'])

@php
    $productBaseImage = product_image()->getProductBaseImage($product);
    $priceHtml = $product->getTypeInstance()->getPriceHtml();
    $isSaleable = $product->getTypeInstance()->isSaleable();
    
    // Check for discount
    $price = $product->getTypeInstance()->getMinimalPrice();
    $regularPrice = $product->getTypeInstance()->getMaximumPrice();
    $discountPercentage = 0;
    if ($regularPrice > $price) {
        $discountPercentage = round((($regularPrice - $price) / $regularPrice) * 100);
    }
    
    // Generate product URL - use url_key if available, otherwise url_path
    $productUrl = $product->url_key? route('shop.product_or_category.index', $product->url_key) : '#';
@endphp

<article class="product-card">
    <a href="{{ $productUrl }}" class="product-image">
        <img 
            src="{{ $productBaseImage['medium_image_url'] }}" 
            alt="{{ $product->name }}"
            loading="lazy"
        >
    </a>

    <a href="{{ $productUrl }}" class="product-title" title="{{ $product->name }}">
        {{ $product->name }}
    </a>

    <div class="product-price-row">
        <div class="price">{!! core()->currency($price) !!}</div>
        
        @if ($discountPercentage > 0)
            <div class="discount-badge">-{{ $discountPercentage }}%</div>
            <strike>{!! core()->currency($regularPrice) !!}</strike>
        @endif
    </div>

    <div class="product-rating-row">
        <div class="stars">
            @php $avgRating = round($product->reviews->avg('rating') ?? 0); @endphp
            @for ($i = 1; $i <= 5; $i++)
                @if ($i <= $avgRating)
                    <i class="fa-solid fa-star"></i>
                @else
                    <i class="fa-regular fa-star"></i>
                @endif
            @endfor
        </div>
        <div class="sold-count">Đã bán {{ rand(10, 1000) }}</div>
    </div>

    {{-- Add to Cart Form --}}
    @if ($isSaleable)
        <form action="{{ route('shop.api.checkout.cart.store') }}" method="POST" class="add-to-cart-form">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="btn-add-cart">
                <i class="fa-solid fa-cart-plus"></i> @lang('ta-vpp-theme::app.components.products.card.add-to-cart')
            </button>
        </form>
    @else
        <button class="btn-add-cart" disabled style="opacity: 0.5; cursor: not-allowed;">
            @lang('ta-vpp-theme::app.products.view.out-of-stock')
        </button>
    @endif
</article>
