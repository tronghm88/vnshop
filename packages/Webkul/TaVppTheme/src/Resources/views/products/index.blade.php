{{-- Product Listing Page --}}
<x-ta-vpp-theme::layouts>
    <x-slot:title>
        Danh sách sản phẩm
    </x-slot>

    <main class="page">
        <div class="container">
            {{-- Breadcrumb --}}
            <x-ta-vpp-theme::breadcrumb />

            {{-- Section Header with Sort --}}
            <div class="section-header">
                <h2>Danh sách sản phẩm</h2>
                <button class="btn">Sắp xếp: Phù hợp nhất</button>
            </div>

            {{-- Two Column Layout: Filters + Products --}}
            <div class="catalog-layout">
                {{-- Filters Sidebar --}}
                <aside>
                    {{-- Category Filter --}}
                    <div class="filter-card">
                        <h4>Danh mục</h4>
                        <div class="filter-list">
                            <label><input type="checkbox" /> Bút & chì</label>
                            <label><input type="checkbox" /> Bút dạ</label>
                            <label><input type="checkbox" /> Vở & sổ</label>
                            <label><input type="checkbox" /> Phụ kiện</label>
                        </div>
                    </div>

                    {{-- Price Range Filter --}}
                    <div class="filter-card">
                        <h4>Khoảng giá</h4>
                        <div class="filter-list">
                            <label><input type="checkbox" /> Dưới 20.000đ</label>
                            <label><input type="checkbox" /> 20.000đ - 50.000đ</label>
                            <label><input type="checkbox" /> 50.000đ - 100.000đ</label>
                            <label><input type="checkbox" /> Trên 100.000đ</label>
                        </div>
                    </div>

                    {{-- Rating Filter --}}
                    <div class="filter-card">
                        <h4>Đánh giá</h4>
                        <div class="filter-list">
                            <label><input type="checkbox" /> 4 sao trở lên</label>
                            <label><input type="checkbox" /> 3 sao trở lên</label>
                            <label><input type="checkbox" /> 2 sao trở lên</label>
                        </div>
                    </div>
                </aside>

                {{-- Products Grid Section --}}
                <section>
                    <div class="product-grid">
                        {{-- Sample Product Cards - Replace with dynamic content later --}}
                        @for ($i = 1; $i <= 6; $i++)
                        <article class="product-card">
                            <div class="product-image">
                                <img src="{{ asset('themes/ta-vpp-theme/build/images/sample-product.jpg') }}" alt="Sample Product {{ $i }}">
                            </div>
                            <div class="product-title">Sản phẩm mẫu {{ $i }} - Chất lượng cao</div>
                            <div class="product-price-row">
                                <div class="price">{{ number_format(12000 * $i, 0, ',', '.') }}đ</div>
                                @if ($i % 2 == 0)
                                    <div class="discount-badge">-22%</div>
                                @endif
                            </div>
                            <div class="product-rating-row">
                                <div class="stars">
                                    @for ($s = 1; $s <= 5; $s++)
                                        @if ($s <= 4)
                                            <i class="fa-solid fa-star"></i>
                                        @elseif ($s == 5 && $i % 3 == 0)
                                            <i class="fa-solid fa-star"></i>
                                        @elseif ($s == 5)
                                            <i class="fa-solid fa-star-half-stroke"></i>
                                        @else
                                            <i class="fa-regular fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <div class="sold-count">Đã bán {{ rand(10, 500) }}</div>
                            </div>
                            <a class="btn-add-cart" href="#">Xem chi tiết</a>
                        </article>
                        @endfor
                    </div>

                    {{-- Pagination --}}
                    <div class="pagination">
                        <button class="btn">1</button>
                        <button class="btn">2</button>
                        <button class="btn">3</button>
                        <button class="btn">Tiếp</button>
                    </div>
                </section>
            </div>
        </div>
    </main>
</x-ta-vpp-theme::layouts>
