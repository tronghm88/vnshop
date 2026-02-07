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
    </div>
</article>
