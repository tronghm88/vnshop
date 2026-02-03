@inject('productRepository', 'Webkul\Product\Repositories\ProductRepository')
@inject('attributeRepository', 'Webkul\Attribute\Repositories\AttributeRepository')

@php
    $requestParams = request()->query();
    $query = request()->input('name') ?? request()->input('query');
    
    // Convert array params to comma separated for Repository if needed
    $parsedParams = [];
    foreach ($requestParams as $key => $value) {
        if (is_array($value)) {
            $parsedParams[$key] = implode(',', $value);
        } else {
            $parsedParams[$key] = $value;
        }
    }

    $params = array_merge($parsedParams, [
        'channel_id' => core()->getCurrentChannel()->id,
        'status' => 1,
        'visible_individually' => 1,
        'name' => $query,
    ]);

    // Initial SSR load
    $products = $productRepository->getAll($params);
    $filterableAttributes = $attributeRepository->getFilterableAttributes();
    
    $title = $query ? trans('shop::app.search.title', ['query' => $query]) : trans('ta-vpp-theme::app.search.results');
@endphp

<x-ta-vpp-theme::layouts>
    <x-slot:title>
        {{ $title }}
    </x-slot>
    <div class="container">
        {{-- Breadcrumb --}}
        <x-ta-vpp-theme::breadcrumb />

        {{-- Section Header with Sort --}}
        <div class="section-header">
            <h2>{{ $title }}</h2>
            
            <div class="sort-wrapper">
                <label>@lang('ta-vpp-theme::app.categories.filters.sort'):</label>
                <select id="sort-select" class="sort-select">
                    <option value="created_at-desc" {{ request('sort') == 'created_at-desc' ? 'selected' : '' }}>{{ trans('ta-vpp-theme::app.products.sort-by.latest') }}</option>
                    <option value="price-asc" {{ request('sort') == 'price-asc' ? 'selected' : '' }}>{{ trans('ta-vpp-theme::app.products.sort-by.price-asc') }}</option>
                    <option value="price-desc" {{ request('sort') == 'price-desc' ? 'selected' : '' }}>{{ trans('ta-vpp-theme::app.products.sort-by.price-desc') }}</option>
                    <option value="name-asc" {{ request('sort') == 'name-asc' ? 'selected' : '' }}>{{ trans('ta-vpp-theme::app.products.sort-by.name-asc') }}</option>
                    <option value="name-desc" {{ request('sort') == 'name-desc' ? 'selected' : '' }}>{{ trans('ta-vpp-theme::app.products.sort-by.name-desc') }}</option>
                </select>
            </div>
        </div>

        {{-- Two Column Layout: Filters + Products --}}
        <div class="catalog-layout">
            {{-- Filters Sidebar --}}
            <aside>
                <form id="filter-form">
                    {{-- Preserve search query if exists --}}
                    @if($query)
                        <input type="hidden" name="name" value="{{ $query }}">
                    @endif
                    
                    @foreach ($filterableAttributes as $attribute)
                        <div class="filter-card">
                            <h4>{{ $attribute->admin_name }}</h4>
                            <div class="filter-list">
                                @if ($attribute->type == 'price')
                                    {{-- Price Filter --}}
                                    @php
                                        $priceRanges = [
                                            '0,20000'          => trans('ta-vpp-theme::app.categories.filters.price-under', ['price' => '20.000đ']),
                                            '20000,50000'      => trans('ta-vpp-theme::app.categories.filters.price-range', ['from' => '20.000đ', 'to' => '50.000đ']),
                                            '50000,100000'     => trans('ta-vpp-theme::app.categories.filters.price-range', ['from' => '50.000đ', 'to' => '100.000đ']),
                                            '100000,100000000' => trans('ta-vpp-theme::app.categories.filters.price-above', ['price' => '100.000đ']),
                                        ];
                                        $currentPrice = request('price');
                                    @endphp
                                    @foreach ($priceRanges as $range => $label)
                                        <label>
                                            <input type="radio" name="price" value="{{ $range }}" 
                                                {{ $currentPrice == $range ? 'checked' : '' }}
                                                class="filter-input"
                                            /> 
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                @else
                                    {{-- Other Attributes --}}
                                    @foreach ($attribute->options as $option)
                                    @php
                                        $selectedOptions = request($attribute->code) ? explode(',', request($attribute->code)) : [];
                                    @endphp
                                        <label>
                                            <input type="checkbox" name="{{ $attribute->code }}[]" value="{{ $option->id }}" 
                                                {{ in_array($option->id, $selectedOptions) ? 'checked' : '' }}
                                                class="filter-input"
                                            /> 
                                            {{ $option->label ?? $option->admin_name }}
                                        </label>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="mt-4" id="clear-filters-btn" style="display: {{ request()->except(['query', 'page', 'sort', 'limit', 'name']) ? 'block' : 'none' }}">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFilters()">{{ trans('ta-vpp-theme::app.categories.filters.clear-all') }}</button>
                    </div>
                </form>
            </aside>

            {{-- Products Grid Section --}}
            <section>
                <div class="product-grid" id="product-grid">
                    @forelse ($products as $product)
                        <x-ta-vpp-theme::products.card :product="$product" />
                    @empty
                        <div class="empty-products" id="empty-state" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                            <img src="{{ bagisto_asset('images/notfound.svg', 'ta-vpp-theme') }}" alt="Empty" style="margin: 0 auto 20px; max-width: 200px;">
                            <p>@lang('ta-vpp-theme::app.search.no-results')</p>
                        </div>
                    @endforelse
                </div>

                {{-- Load More / Pagination --}}
                <div class="load-more-wrapper" style="text-align: center; margin-top: 30px;">
                    <button id="load-more-btn" class="btn btn-primary" style="display: {{ $products->hasMorePages() ? 'inline-block' : 'none' }}">
                            @lang('shop::app.search.load_more', ['default' => 'Load More'])
                    </button>
                    <div id="loading-spinner" style="display: none; color: #666;">
                        <i class="fa-solid fa-spinner fa-spin fa-2x"></i>
                    </div>
                </div>
            </section>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('filter-form');
            const sortSelect = document.getElementById('sort-select');
            const productGrid = document.getElementById('product-grid');
            const loadMoreBtn = document.getElementById('load-more-btn');
            const loadingSpinner = document.getElementById('loading-spinner');
            const emptyState = document.getElementById('empty-state');
            const clearFiltersBtn = document.getElementById('clear-filters-btn');
            
            // State
            let currentPage = 1;
            let nextPageUrl = '{{ $products->nextPageUrl() }}';
            let isLoading = false;
            
            const apiUrl = '{{ url("api/products") }}';
            const addToCartUrl = '{{ route("shop.api.checkout.cart.store") }}';
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Event Listeners
            // Handle Filter Changes
            const filterInputs = document.querySelectorAll('.filter-input');
            filterInputs.forEach(input => {
                input.addEventListener('change', () => {
                    resetAndFetch();
                });
            });

            // Handle Sort Change
            if(sortSelect) {
                sortSelect.addEventListener('change', () => {
                    resetAndFetch();
                });
            }

            // Handle Load More
            if(loadMoreBtn) {
                loadMoreBtn.addEventListener('click', () => {
                    if (nextPageUrl) {
                        fetchProducts(nextPageUrl, true);
                    }
                });
            }

            window.clearFilters = function() {
                filterForm.reset();
                // Specifically allow checking/unchecking inputs
                const inputs = filterForm.querySelectorAll('input:checked');
                inputs.forEach(input => input.checked = false);
                resetAndFetch();
            }

            function resetAndFetch() {
                currentPage = 1;
                // Construct URL for first page
                const url = new URL(apiUrl, window.location.origin);
                appendParamsToUrl(url);
                url.searchParams.set('page', 1);
                
                fetchProducts(url.toString(), false);
            }

            function appendParamsToUrl(urlData) {
                const formData = new FormData(filterForm);
                const sortValue = sortSelect ? sortSelect.value : '';
                
                // Add sort
                if (sortValue) urlData.searchParams.set('sort', sortValue);

                // Add form data
                // Need to handle arrays like color[]=1&color[]=2 -> color=1,2
                const params = {};
                for (const [key, value] of formData.entries()) {
                    let cleanKey = key.replace('[]', '');
                    if (params[cleanKey]) {
                        params[cleanKey] += ',' + value;
                    } else {
                        params[cleanKey] = value;
                    }
                }
                
                for (const [key, value] of Object.entries(params)) {
                    urlData.searchParams.set(key, value);
                }
                
                // Update Browser URL (History)
                const browserUrl = new URL(window.location.href);
                browserUrl.search = urlData.search;
                window.history.pushState({}, '', browserUrl);
                
                // Show/Hide Clear Button
                let hasFilters = false;
                for(const key of Object.keys(params)) {
                    if(key !== 'name' && key !== 'sort' && key !== 'limit' && key !== 'page') {
                       hasFilters = true;
                       break;
                    }
                }
                if (clearFiltersBtn) clearFiltersBtn.style.display = hasFilters ? 'block' : 'none';
            }

            function fetchProducts(url, append) {
                if (isLoading) return;
                isLoading = true;
                
                if(loadMoreBtn) loadMoreBtn.style.display = 'none';
                if(loadingSpinner) loadingSpinner.style.display = 'block';

                fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (!append) {
                        productGrid.innerHTML = '';
                    }

                    if (data.data && data.data.length > 0) {
                        if (document.getElementById('empty-state')) document.getElementById('empty-state').remove();
                        
                        data.data.forEach(product => {
                            const productHtml = renderProductCard(product);
                            productGrid.insertAdjacentHTML('beforeend', productHtml);
                        });
                    } else {
                        if (!append) {
                             productGrid.innerHTML = `
                                <div class="empty-products" id="empty-state" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                                    <img src="{{ bagisto_asset('images/notfound.svg', 'ta-vpp-theme') }}" alt="Empty" style="margin: 0 auto 20px; max-width: 200px;">
                                    <p>@lang('ta-vpp-theme::app.search.no-results')</p>
                                </div>
                             `;
                        }
                    }

                    // Update Pagination logic
                    if (data.links && data.links.next) {
                        nextPageUrl = data.links.next;
                        if(loadMoreBtn) loadMoreBtn.style.display = 'inline-block';
                    } else {
                        nextPageUrl = null;
                        if(loadMoreBtn) loadMoreBtn.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error fetching products:', error);
                })
                .finally(() => {
                    isLoading = false;
                    if(loadingSpinner) loadingSpinner.style.display = 'none';
                });
            }

            function renderProductCard(product) {
                 // Calculate Discount and Price Display
                 let priceUrl = '{{ url("/") }}/' + product.url_key;
                 let priceDisplay = '';
                 
                 // Logic to determine price display
                 // The API structure for prices:
                 // "min_price": "$53.99"
                 // "prices": { "regular": { "price": "53.9900", "formatted_price": "$53.99" } }
                 
                 // We will simply use min_price for now as it seems to be the main price
                 
                 priceDisplay = `<div class="price">${product.min_price}</div>`;

                 // Generate Stars
                 let rating = parseFloat(product.ratings ? product.ratings.average : 0);
                 let starsHtml = '';
                 for (let i = 1; i <= 5; i++) {
                     if (i <= rating) {
                         starsHtml += '<i class="fa-solid fa-star"></i> ';
                     } else {
                         starsHtml += '<i class="fa-regular fa-star"></i> ';
                     }
                 }

                 // Random sold count for demo functionality
                 let soldCount = Math.floor(Math.random() * (1000 - 10) + 10);

                 // Image handling
                 let imageUrl = '';
                 if (product.base_image && product.base_image.medium_image_url) {
                     imageUrl = product.base_image.medium_image_url;
                 } else if (product.images && product.images.length > 0) {
                     imageUrl = product.images[0].medium_image_url;
                 }

                 // Add to Cart / Out of Stock
                 let addToCartHtml = '';
                 if (product.is_saleable) {
                     addToCartHtml = `
                        <form action="${addToCartUrl}" method="POST" class="add-to-cart-form">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="product_id" value="${product.id}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn-add-cart">
                                <i class="fa-solid fa-cart-plus"></i> @lang('ta-vpp-theme::app.components.products.card.add-to-cart')
                            </button>
                        </form>
                     `;
                 } else {
                      addToCartHtml = `
                        <button class="btn-add-cart" disabled style="opacity: 0.5; cursor: not-allowed;">
                            @lang('ta-vpp-theme::app.products.view.out-of-stock')
                        </button>
                      `;
                 }

                 return `
                    <article class="product-card">
                        <a href="${priceUrl}" class="product-image">
                            <img 
                                src="${imageUrl}" 
                                alt="${product.name}"
                                loading="lazy"
                            >
                        </a>

                        <a href="${priceUrl}" class="product-title" title="${product.name}">
                            ${product.name}
                        </a>

                        <div class="product-price-row">
                            ${priceDisplay}
                        </div>

                        <div class="product-rating-row">
                            <div class="stars">
                                ${starsHtml}
                            </div>
                            <div class="sold-count">Đã bán ${soldCount}</div>
                        </div>

                        ${addToCartHtml}
                    </article>
                 `;
            }
        });
    </script>
    @endpush
</x-ta-vpp-theme::layouts>
