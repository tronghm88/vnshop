@inject('productRepository', 'Webkul\Product\Repositories\ProductRepository')
@inject('attributeRepository', 'Webkul\Attribute\Repositories\AttributeRepository')

@php
    $requestParams = request()->query();
    
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
        'category_id' => $category->id,
    ]);

    $products = $productRepository->getAll($params);
    
    if (empty($filterableAttributes = $category->filterableAttributes)) {
        $filterableAttributes = $attributeRepository->getFilterableAttributes();
    }
@endphp

<x-ta-vpp-theme::layouts>
    <x-slot:title>
        {{ trim($category->meta_title) != "" ? $category->meta_title : $category->name }}
    </x-slot>

    <main class="page">
        <div class="container">
            {{-- Breadcrumb --}}
            

            {{-- Section Header with Sort --}}
            <div class="section-header">
                <h2>{{ $category->name }}</h2>
                <div class="sort-wrapper">
                    <label>@lang('ta-vpp-theme::app.categories.filters.sort'):</label>
                    <select onchange="window.location.href=this.value" class="sort-select">
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'created_at-desc']) }}" {{ request('sort') == 'created_at-desc' ? 'selected' : '' }}>{{ trans('ta-vpp-theme::app.products.sort-by.latest') }}</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price-asc']) }}" {{ request('sort') == 'price-asc' ? 'selected' : '' }}>{{ trans('ta-vpp-theme::app.products.sort-by.price-asc') }}</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price-desc']) }}" {{ request('sort') == 'price-desc' ? 'selected' : '' }}>{{ trans('ta-vpp-theme::app.products.sort-by.price-desc') }}</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'name-asc']) }}" {{ request('sort') == 'name-asc' ? 'selected' : '' }}>{{ trans('ta-vpp-theme::app.products.sort-by.name-asc') }}</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'name-desc']) }}" {{ request('sort') == 'name-desc' ? 'selected' : '' }}>{{ trans('ta-vpp-theme::app.products.sort-by.name-desc') }}</option>
                    </select>
                </div>
            </div>

            {{-- Two Column Layout: Filters + Products --}}
            <div class="catalog-layout">
                {{-- Filters Sidebar --}}
                <aside>
                    <form id="filter-form" action="{{ url()->current() }}" method="GET">
                        {{-- Preserve other params --}}
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif
                        @if(request('limit'))
                            <input type="hidden" name="limit" value="{{ request('limit') }}">
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
                                                    onchange="this.form.submit()"
                                                /> 
                                                {{ $label }}
                                            </label>
                                        @endforeach
                                    @else
                                        {{-- Other Attributes --}}
                                        @foreach ($attribute->options as $option)
                                            <label>
                                                <input type="checkbox" name="{{ $attribute->code }}[]" value="{{ $option->id }}" 
                                                    {{ in_array($option->id, request($attribute->code, [])) ? 'checked' : '' }}
                                                    onchange="this.form.submit()"
                                                /> 
                                                {{ $option->label ?? $option->admin_name }}
                                            </label>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        
                        @if(request()->except(['page', 'sort', 'limit']))
                            <div class="mt-4">
                                <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-danger">{{ trans('ta-vpp-theme::app.categories.filters.clear-all') }}</a>
                            </div>
                        @endif
                    </form>
                </aside>

                {{-- Products Grid Section --}}
                <section>
                    <div class="product-grid">
                        @forelse ($products as $product)
                            <x-ta-vpp-theme::products.card :product="$product" />
                        @empty
                            <div class="empty-products" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                                <img src="{{ bagisto_asset('images/thank-you.png') }}" alt="Empty" style="margin: 0 auto 20px; max-width: 200px;">
                                <p>@lang('shop::app.categories.view.empty')</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    {{ $products->appends(request()->query())->links() }}
                </section>
            </div>
        </div>
    </main>
</x-ta-vpp-theme::layouts>
