<x-ta-vpp-theme::layouts>
    <x-slot:title>
        @lang('shop::app.home.page-title')
    </x-slot>

    @php
        $productRepository = app('Webkul\Product\Repositories\ProductRepository');
        
        // Fetch New Products
        $newProducts = $productRepository->getAll([
            'limit' => 10,
            'sort'  => 'created_at',
            'order' => 'desc',
        ]);

        // Fetch Featured Products
        $featuredProducts = $productRepository->getAll([
            'featured' => 1,
            'limit'    => 10,
        ]);
    @endphp

    {{-- Hero Slider --}}
    <x-ta-vpp-theme::hero-slider />
    
    {{-- New Products Carousel --}}
    <x-ta-vpp-theme::products.carousel 
        title="Sản phẩm mới nhất" 
        :products="$newProducts" 
        viewMoreUrl="{{ route('shop.search.index') }}"
    />

    {{-- Featured Products Carousel --}}
    <x-ta-vpp-theme::products.carousel 
        title="Sản phẩm nổi bật" 
        :products="$featuredProducts" 
        viewMoreUrl="{{ route('shop.search.index', ['featured' => 1]) }}"
    />

</x-ta-vpp-theme::layouts>
