<!-- SEO Meta Content -->
@push('meta')
    <meta name="title" content="{{ $page->meta_title }}" />
    <meta name="description" content="{{ $page->meta_description }}" />
    <meta name="keywords" content="{{ $page->meta_keywords }}" />
@endPush

<!-- Page Layout -->
<x-ta-vpp-theme::layouts>
    <!-- Page Title -->
    <x-slot:title>
        {{ $page->meta_title }}
    </x-slot>

    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('shop.home.index') }}">{{ trans('ta-vpp-theme::app.checkout.onepage.index.home') }}</a> /
            <span>{{ $page->page_title }}</span>
        </div>
    </div>

    <!-- Page Content -->
    <div class="cms-page-wrapper bg-white dark:bg-gray-900">
        <!-- Main Content Area -->
        <div class="container px-[60px] max-lg:px-8 pb-8">
            <!-- Page HTML Content -->
            <div class="cms-content prose prose-lg max-w-none dark:prose-invert">
                {!! $page->html_content !!}
            </div>
        </div>
    </div>

    <!-- Loop over the theme customization -->
    @foreach ($customizations as $customization)
        @php ($data = $customization->options) @endphp

        <!-- Static content -->
        @switch ($customization->type)
            @case ($customization::PRODUCT_CAROUSEL)
                <!-- Product Carousel -->
                <x-ta-vpp-theme::products.carousel
                    :title="$data['title'] ?? ''"
                    :src="route('shop.api.products.index', $data['filters'] ?? [])"
                    :view-more-url="route('shop.search.index', $data['filters'] ?? [])"
                />

                @break
            @default
                @break
        @endswitch
    @endforeach
</x-ta-vpp-theme::layouts>
