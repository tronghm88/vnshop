@props([
    'title' => '',
    'src' => null,
    'viewMoreUrl' => '#'
])

@php
    $products = [];

    if ($src) {
        $urlComponents = parse_url($src);
        
        $queryParams = [];
        if (isset($urlComponents['query'])) {
            parse_str($urlComponents['query'], $queryParams);
        }

        $products = app('Webkul\Product\Repositories\ProductRepository')->getAll($queryParams);
    }
@endphp

@if (count($products))
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

        @if ($viewMoreUrl)
            <div class="view-more-container">
                <a href="{{ $viewMoreUrl }}" class="btn-view-more">
                    {{ trans('ta-vpp-theme::app.components.products.carousel.view-all') }}
                </a>
            </div>
        @endif
    </section>
@endif
