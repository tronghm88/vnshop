@props([
    'title' => '',
    'products' => [],
    'showViewMore' => true,
    'viewMoreUrl' => '#'
])

<section class="section container">
    @if ($title)
        <div class="section-header">
            <h2>{{ $title }}</h2>
        </div>
    @endif

    <div class="carousel-container">
        <button class="carousel-btn prev" onclick="scrollCarousel(this, -1)">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        
        <div class="carousel">
            @foreach ($products as $product)
                <x-ta-vpp-theme::products.card :product="$product" />
            @endforeach
        </div>

        <button class="carousel-btn next" onclick="scrollCarousel(this, 1)">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    </div>

    @if ($showViewMore)
        <div class="view-more-container">
            <a href="{{ $viewMoreUrl }}" class="btn-view-more">Xem Thêm</a>
        </div>
    @endif
</section>
