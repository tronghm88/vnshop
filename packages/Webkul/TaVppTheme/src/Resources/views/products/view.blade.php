{{-- Product Detail Page with Real Bagisto Data (Vanilla JS - No Vue) --}}
@inject ('reviewHelper', 'Webkul\Product\Helpers\Review')
@inject ('productViewHelper', 'Webkul\Product\Helpers\View')

@php
    $avgRatings = $reviewHelper->getAverageRating($product);
    $percentageRatings = $reviewHelper->getPercentageRating($product);
    $totalReviews = $reviewHelper->getTotalFeedback($product);
    $customAttributeValues = $productViewHelper->getAdditionalData($product);
    $attributeData = collect($customAttributeValues)->filter(fn ($item) => ! empty($item['value']));
    $productBaseImage = product_image()->getProductBaseImage($product);
    $galleryImages = product_image()->getGalleryImages($product);
    
    // Calculate discount
    $regularPrice = $product->getTypeInstance()->getMinimalPrice();
    $finalPrice = $product->getTypeInstance()->getFinalPrice();
    $discount = 0;
    if ($regularPrice > $finalPrice) {
        $discount = round((($regularPrice - $finalPrice) / $regularPrice) * 100);
    }
    
    $isInStock = $product->getTypeInstance()->haveSufficientQuantity(1);
    
    $productConfig = [];
    if (Webkul\Product\Helpers\ProductType::hasVariants($product->type)) {
        $productConfig = app('Webkul\Product\Helpers\ConfigurableOption')->getConfigurationConfig($product);
    }
@endphp

{{-- SEO Meta Content --}}
@push('meta')
    <meta name="description" content="{{ trim($product->meta_description) != '' ? $product->meta_description : \Illuminate\Support\Str::limit(strip_tags($product->description), 120, '') }}"/>
    <meta name="keywords" content="{{ $product->meta_keywords }}"/>
    
    @if (core()->getConfigData('catalog.rich_snippets.products.enable'))
        <script type="application/ld+json">
            {!! app('Webkul\Product\Helpers\SEO')->getProductJsonLd($product) !!}
        </script>
    @endif

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $product->name }}" />
    <meta name="twitter:description" content="{!! htmlspecialchars(trim(strip_tags($product->description))) !!}" />
    <meta name="twitter:image" content="{{ $productBaseImage['medium_image_url'] }}" />
    <meta property="og:type" content="og:product" />
    <meta property="og:title" content="{{ $product->name }}" />
    <meta property="og:image" content="{{ $productBaseImage['medium_image_url'] }}" />
    <meta property="og:description" content="{!! htmlspecialchars(trim(strip_tags($product->description))) !!}" />
    <meta property="og:url" content="{{ route('shop.product_or_category.index', $product->url_key) }}" />
@endPush

<x-ta-vpp-theme::layouts>
    <x-slot:title>
        {{ trim($product->meta_title) != '' ? $product->meta_title : $product->name }}
    </x-slot>

    {!! view_render_event('bagisto.shop.products.view.before', ['product' => $product]) !!}

    
    <div class="container">
        {{-- Breadcrumb --}}
        <x-ta-vpp-theme::breadcrumb name="product" :entity="$product" />

        {{-- Product Detail Section --}}
        <div class="product-detail">
            {{-- Product Gallery --}}
            <div class="gallery">
                <div class="gallery-main">
                    <img id="mainImage" src="{{ $galleryImages[0]['large_image_url'] ?? $productBaseImage['large_image_url'] }}" alt="{{ $product->name }}">
                </div>
                
                @if (count($galleryImages) > 1)
                    <div class="gallery-thumbs-container">
                        <button class="thumb-nav" onclick="scrollThumbs(-1)">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <div class="gallery-thumbs" id="galleryThumbs">
                            @foreach ($galleryImages as $index => $image)
                                <div class="thumb {{ $index === 0 ? 'active' : '' }}" data-image="{{ $image['large_image_url'] }}">
                                    <img src="{{ $image['small_image_url'] }}" alt="{{ $product->name }}">
                                </div>
                            @endforeach
                        </div>
                        <button class="thumb-nav" onclick="scrollThumbs(1)">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                @endif
            </div>

            {{-- Product Info Card --}}
            <div class="product-info-card">
                {{-- Form for Add to Cart --}}
                <form id="productForm" method="POST" action="{{ route('shop.api.checkout.cart.store') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="is_buy_now" id="is_buy_now" value="0">
                    
                    {{-- Title with Badge --}}
                    <div class="product-title-container">
                        @if ($isInStock)
                            <span class="badge badge-best-seller">{{ trans('ta-vpp-theme::app.products.view.in-stock') }} <i class="fa-solid fa-check"></i></span>
                        @endif
                        <h1>{{ $product->name }}</h1>
                    </div>

                    {{-- Product Meta Info --}}
                    <div class="product-meta-row">
                        @if ($product->brand)
                            <span>{{ trans('ta-vpp-theme::app.products.view.brand') }}: <strong>{{ $product->brand }}</strong></span>
                        @endif
                        <span>SKU: <strong>{{ $product->sku }}</strong></span>
                    </div>

                    {{-- Rating and Stats --}}
                    <div class="product-stats-row">
                        <div class="stars">
                            @if ($totalReviews > 0)
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($avgRatings))
                                        <i class="fa-solid fa-star"></i>
                                    @elseif ($i - 0.5 <= $avgRatings)
                                        <i class="fa-solid fa-star-half-stroke"></i>
                                    @else
                                        <i class="fa-regular fa-star"></i>
                                    @endif
                                @endfor
                                <span>{{ trans('ta-vpp-theme::app.products.view.reviews-count', ['total' => $totalReviews]) }}</span>
                            @else
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa-regular fa-star"></i>
                                @endfor
                                <span>{{ trans('ta-vpp-theme::app.products.view.no-reviews') }}</span>
                            @endif
                        </div>
                        
                        <div class="divider"></div>
                        
                        @if ($isInStock)
                            <div class="stock-status" style="color: #27ae60;">{{ trans('ta-vpp-theme::app.products.view.in-stock') }}</div>
                        @else
                            <div class="stock-status" style="color: #e74c3c;">{{ trans('ta-vpp-theme::app.products.view.out-of-stock') }}</div>
                        @endif
                    </div>

                    {{-- Price Section --}}
                    <div class="product-price-row">
                        <div class="price price-large">{{ core()->currency($finalPrice) }}</div>
                        
                        @if ($discount > 0)
                            <strike>{{ core()->currency($regularPrice) }}</strike>
                            <div class="discount-badge">-{{ $discount }}%</div>
                        @endif
                    </div>

                    {{-- Voucher Section - Data prepared in ProductVoucherComposer --}}
                    @if(isset($availableVouchers) && $availableVouchers->count() > 0)
                        <div class="voucher-section">
                            <div class="voucher-title">
                                {{ trans('ta-vpp-theme::app.products.view.related-offers') }} 
                                <a href="{{ route('shop.home.index') }}#promotions">
                                    {{ trans('ta-vpp-theme::app.checkout.cart.index.view-more') }} <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </div>
                            <div class="voucher-list">
                                @foreach($availableVouchers as $rule)
                                    @php
                                        $isShipping = $rule->free_shipping ?? false;
                                        if (!$isShipping) {
                                            $isShipping = stripos($rule->name, 'ship') !== false 
                                                       || stripos($rule->description ?? '', 'ship') !== false;
                                        }
                                        $couponCode = $rule->coupon_code ?? null;
                                        $description = $rule->description ?? '';
                                        $expirationDate = $rule->ends_till ? \Carbon\Carbon::parse($rule->ends_till)->format('d/m/Y') : null;
                                    @endphp
                                    <div class="voucher-item {{ $isShipping ? 'voucher-shipping' : 'voucher-discount' }}" 
                                            style="cursor: pointer;"
                                            data-voucher-name="{{ htmlspecialchars($rule->name) }}"
                                            data-voucher-description="{{ htmlspecialchars($description) }}"
                                            data-voucher-expiration="{{ $expirationDate ?? '' }}"
                                            data-voucher-coupon="{{ $couponCode ?? '' }}"
                                        >
                                        <i class="fa-solid fa-{{ $isShipping ? 'truck-fast' : 'ticket' }}"></i> 
                                        {{ Str::limit($rule->name, 30) }}
                                        <div class="voucher-popup">
                                            <div class="voucher-popup-header">
                                                <span>{{ trans('ta-vpp-theme::app.products.view.promotion-program') }}</span>
                                                <i class="fa-solid fa-circle-info"></i>
                                            </div>
                                            @if($expirationDate)
                                                <div class="voucher-popup-expiration">
                                                    {{ trans('ta-vpp-theme::app.products.view.expiry-date') }}
                                                </div>
                                                <div class="voucher-popup-date">
                                                    {{ $expirationDate }}
                                                </div>
                                            @endif
                                            @if($description)
                                                <div class="voucher-popup-description">
                                                    {!! nl2br(e($description)) !!}
                                                </div>
                                            @endif
                                            @if($couponCode)
                                                <div class="voucher-popup-coupon">
                                                    <span>{{ trans('ta-vpp-theme::app.checkout.cart.coupon-code') }}:</span>
                                                    <div class="coupon-code-wrapper">
                                                        <code class="coupon-code">{{ $couponCode }}</code>
                                                        <button type="button" class="copy-coupon-btn" onclick="copyVoucherCode('{{ $couponCode }}', this)">
                                                            <i class="fa-solid fa-copy"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Configurable Options (Variants) --}}
                    @if (Webkul\Product\Helpers\ProductType::hasVariants($product->type))
                        @php
                            $config = app('Webkul\Product\Helpers\ConfigurableOption')->getConfigurationConfig($product);
                        @endphp
                        
                        @if (isset($config['attributes']) && count($config['attributes']) > 0)
                            <div class="variant-section">
                                @foreach ($config['attributes'] as $attribute)
                                    <h4>{{ $attribute['label'] }}:</h4>
                                    <div class="variant-list" id="variant-{{ $attribute['id'] }}">
                                        @foreach ($attribute['options'] as $option)
                                            <div class="variant" 
                                                data-attribute-id="{{ $attribute['id'] }}" 
                                                data-option-id="{{ $option['id'] }}">
                                                {{ $option['label'] }}
                                            </div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="super_attribute[{{ $attribute['id'] }}]" id="attribute_{{ $attribute['id'] }}" value="">
                                @endforeach
                            </div>
                        @endif
                    @endif

                    {{-- Quantity Control --}}
                    @if ($isInStock)
                        <div class="quantity-section">
                            <label>{{ trans('ta-vpp-theme::app.checkout.cart.index.quantity') }}:</label>
                            <div class="quantity-control">
                                <button type="button" onclick="decreaseQuantity()">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <input type="text" name="quantity" id="quantity" value="1" readonly>
                                <button type="button" onclick="increaseQuantity()">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Shipping Estimation --}}
                       

                        {{-- Action Buttons --}}
                        <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 10px;">
                            <button 
                                type="button"
                                id="addToCartBtn"
                                class="btn btn-outline" 
                                style="flex: 1; padding: 12px; border-color: var(--primary); color: var(--primary);"
                                onclick="addToCart()"
                            >
                                <i class="fa-solid fa-cart-shopping"></i> 
                                <span id="addToCartText">{{ trans('ta-vpp-theme::app.products.view.add-to-cart') }}</span>
                            </button>
                            <button 
                                type="button"
                                id="buyNowBtn"
                                class="btn btn-primary" 
                                style="flex: 1; padding: 12px;"
                                onclick="buyNow()"
                            >
                                {{ trans('ta-vpp-theme::app.products.view.buy-now') }}
                            </button>
                        </div>
                    @else
                        <div style="padding: 20px; background: #fee; border-radius: 8px; text-align: center; color: #c33;">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ trans('shop::app.products.view.out-of-stock') }}
                        </div>
                    @endif

                    {{-- Additional Info --}}
                    <div class="info-grid" style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
                        <div class="info-item" style="background: none; padding: 5px 0;">
                            <i class="fa-solid fa-truck" style="color: var(--primary); width: 20px;"></i>
                            <strong style="margin-right: 5px;">{{ trans('ta-vpp-theme::app.products.view.fast-delivery') }}</strong>{{ trans('ta-vpp-theme::app.products.view.nationwide-delivery') }}
                        </div>
                        <div class="info-item" style="background: none; padding: 5px 0;">
                            <i class="fa-solid fa-rotate-left" style="color: var(--primary); width: 20px;"></i>
                            <strong style="margin-right: 5px;">{{ trans('ta-vpp-theme::app.products.view.easy-returns') }}</strong>{{ trans('ta-vpp-theme::app.products.view.seven-days-returns') }}
                        </div>
                        <div class="info-item" style="background: none; padding: 5px 0;">
                            <i class="fa-solid fa-shield-halved" style="color: var(--primary); width: 20px;"></i>
                            <strong style="margin-right: 5px;">{{ trans('ta-vpp-theme::app.products.view.secure-payment') }}</strong>{{ trans('ta-vpp-theme::app.products.view.secure-info') }}
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Lightbox Gallery --}}
        <div id="lightbox" class="lightbox">
            <span class="close-lightbox" onclick="closeLightbox()">&times;</span>
            <div class="lightbox-content-container">
                <img class="lightbox-content" id="lightboxImage">
                <a class="prev" onclick="moveLightbox(-1)">&#10094;</a>
                <a class="next" onclick="moveLightbox(1)">&#10095;</a>
            </div>
            <div class="lightbox-thumbnails-container">
                <div class="lightbox-thumbnails" id="lightboxThumbnails">
                    {{-- Thumbnails will be populated by JS --}}
                </div>
            </div>
        </div>

        <style>
            /* Lightbox CSS */
            .lightbox {
                display: none;
                position: fixed;
                z-index: 9999;
                padding-top: 30px;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: hidden;
                background-color: rgba(0, 0, 0, 0.95);
                backdrop-filter: blur(5px);
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            
            .lightbox-content-container {
                position: relative;
                width: 100%;
                height: 75vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .lightbox-content {
                margin: auto;
                display: block;
                width: auto;
                height: auto;
                max-width: 95%;
                max-height: 100%;
                object-fit: contain;
                animation-name: zoom;
                animation-duration: 0.3s;
            }
            @keyframes zoom {
                from {transform: scale(0)} 
                to {transform: scale(1)}
            }
            .close-lightbox {
                position: absolute;
                top: 15px;
                right: 30px;
                color: #f1f1f1;
                font-size: 40px;
                font-weight: bold;
                transition: 0.3s;
                cursor: pointer;
                z-index: 10000;
            }
            .close-lightbox:hover,
            .close-lightbox:focus {
                color: #bbb;
                text-decoration: none;
                cursor: pointer;
            }
            .prev, .next {
                cursor: pointer;
                position: absolute;
                top: 50%;
                width: auto;
                padding: 16px;
                margin-top: -22px;
                color: white;
                font-weight: bold;
                font-size: 30px;
                transition: 0.6s ease;
                border-radius: 0 3px 3px 0;
                user-select: none;
                -webkit-user-select: none;
                z-index: 10000;
                background-color: rgba(0,0,0,0.3);
                transform: translateY(-50%);
            }
            .next {
                right: 0;
                border-radius: 3px 0 0 3px;
            }
            .prev {
                left: 0;
                border-radius: 3px 0 0 3px;
            }
            .prev:hover, .next:hover {
                background-color: rgba(0, 0, 0, 0.8);
            }

            /* Lightbox Thumbnails */
            .lightbox-thumbnails-container {
                width: 100%;
                height: 15vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 10px;
                overflow-x: auto;
            }
            
            .lightbox-thumbnails {
                display: flex;
                gap: 10px;
                padding: 5px;
            }
            
            .lightbox-thumb {
                width: 60px;
                height: 60px;
                object-fit: cover;
                cursor: pointer;
                opacity: 0.6;
                border: 2px solid transparent;
                transition: 0.3s;
                border-radius: 4px;
            }
            
            .lightbox-thumb:hover {
                opacity: 1;
            }
            
            .lightbox-thumb.active {
                opacity: 1;
                border-color: var(--primary, #007bff);
            }

            .gallery-main img {
                cursor: zoom-in;
            }
            
            @media only screen and (max-width: 700px){
                .lightbox-content {
                    width: 100%;
                }
                .prev, .next {
                    padding: 10px;
                    font-size: 20px;
                }
                .lightbox-thumbnails-container {
                     height: 12vh;
                }
                .lightbox-thumb {
                    width: 50px;
                    height: 50px;
                }
            }
        </style>

        {{-- Rating Section --}}
        <section class="rating-section">
            <div class="rating-header">{{ trans('ta-vpp-theme::app.products.view.review') }}</div>
            
            <div class="rating-summary">
                {{-- Overall Rating --}}
                <div class="rating-overall">
                    <div class="score">{{ number_format($avgRatings, 1) }}<span>/5</span></div>
                    <div class="stars">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= floor($avgRatings))
                                <i class="fa-solid fa-star"></i>
                            @elseif ($i - 0.5 <= $avgRatings)
                                <i class="fa-solid fa-star-half-stroke"></i>
                            @else
                                <i class="fa-regular fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <div class="count">{{ trans('ta-vpp-theme::app.products.view.reviews-count', ['total' => $totalReviews]) }}</div>
                </div>
                
                {{-- Rating Bars --}}
                <div class="rating-bars">
                    @for ($star = 5; $star >= 1; $star--)
                        <div class="rating-bar-item">
                            <span>{{ trans('ta-vpp-theme::app.products.view.star', ['star' => $star]) }}</span>
                            <div class="bar-container">
                                <div class="bar-fill" style="width: {{ $percentageRatings[$star] ?? 0 }}%;"></div>
                            </div>
                            <span>{{ number_format($percentageRatings[$star] ?? 0, 0) }}%</span>
                        </div>
                    @endfor
                </div>

                {{-- Review Input Box --}}
                @guest('customer')
                    <div class="review-input-box">
                        <p style="text-align: center; padding: 20px;">
                            <a href="{{ route('shop.customer.session.index') }}" style="color: var(--primary);">{{ trans('ta-vpp-theme::app.components.layouts.header.mobile.sign-in') }}</a> {{ trans('ta-vpp-theme::app.products.view.login-to-review') }}
                        </p>
                    </div>
                @else
                    <div class="review-input-box" id="reviewForm">
                        <div class="rating-input">
                            <span>{{ trans('ta-vpp-theme::app.products.view.select-rating') }}</span>
                            <div class="input-stars" id="input-stars">
                                <i class="fa-solid fa-star" data-value="1"></i>
                                <i class="fa-solid fa-star" data-value="2"></i>
                                <i class="fa-solid fa-star" data-value="3"></i>
                                <i class="fa-solid fa-star" data-value="4"></i>
                                <i class="fa-solid fa-star" data-value="5"></i>
                            </div>
                        </div>
                        <textarea id="reviewComment" placeholder="{{ trans('ta-vpp-theme::app.products.view.write-review') }}"></textarea>
                        <button class="btn-submit-review" onclick="submitReview()">
                            <i class="fa-solid fa-paper-plane"></i> 
                            <span id="reviewSubmitText">{{ trans('ta-vpp-theme::app.products.view.send-review') }}</span>
                        </button>
                    </div>
                @endguest
            </div>
            
            {{-- Review List --}}
            <div class="review-list" id="reviewList">
                <div id="reviewLoader" style="text-align: center; padding: 40px;">
                    <i class="fa-solid fa-spinner fa-spin" style="font-size: 24px;"></i>
                </div>
            </div>
        </section>

        {{-- Description Section --}}
        @if ($product->description)
            <section class="description-section">
                <div class="section-header">
                    <h2>{{ trans('ta-vpp-theme::app.products.view.description') }}</h2>
                </div>
                <div class="description-content">
                    {!! $product->description !!}
                </div>
            </section>
        @endif

        {{-- Related Products --}}
        @if ($product->related_products()->count())
            <section class="section">
                <div class="section-header">
                    <h2>{{ trans('ta-vpp-theme::app.products.view.related-product-title') }}</h2>
                </div>
                <x-ta-vpp-theme::products.carousel 
                    title=""
                    :show-title="false"
                    :products="$product->related_products"
                />
            </section>
        @endif
    </div>

    {!! view_render_event('bagisto.shop.products.view.after', ['product' => $product]) !!}

    @push('scripts')
    <script>
        // ============================================
        // Global Data
        // ============================================
        let productConfig = @json($productConfig);
        let currentGalleryImages = @json($galleryImages);
        let currentLightboxIndex = 0;

        // ============================================
        // Gallery Functions
        // ============================================
        function scrollThumbs(direction) {
            const thumbsContainer = document.getElementById('galleryThumbs');
            if (thumbsContainer) {
                const scrollAmount = thumbsContainer.offsetWidth * 0.6;
                thumbsContainer.scrollBy({
                    left: direction * scrollAmount,
                    behavior: 'smooth'
                });
            }
        }

        function attachThumbListeners() {
            const thumbs = document.querySelectorAll('.thumb');
            const mainImg = document.getElementById('mainImage');
            
            thumbs.forEach(thumb => {
                thumb.addEventListener('click', () => {
                    thumbs.forEach(t => t.classList.remove('active'));
                    thumb.classList.add('active');
                    const imageUrl = thumb.getAttribute('data-image');
                    if (mainImg && imageUrl) {
                        mainImg.src = imageUrl;
                    }
                });
            });
        }

        function updateGallery(images) {
            currentGalleryImages = images;
            
            // Update Main Image
            const mainImg = document.getElementById('mainImage');
            if (mainImg && images.length > 0) {
                mainImg.src = images[0].large_image_url;
            }

            // Update Thumbs
            const thumbsContainer = document.getElementById('galleryThumbs');
            if (thumbsContainer) {
                thumbsContainer.innerHTML = '';
                images.forEach((img, index) => {
                    const thumb = document.createElement('div');
                    thumb.className = `thumb ${index === 0 ? 'active' : ''}`;
                    thumb.dataset.image = img.large_image_url;
                    thumb.innerHTML = `<img src="${img.small_image_url}" alt="">`;
                    thumbsContainer.appendChild(thumb);
                });
                attachThumbListeners();
            }
        }

        // ============================================
        // Lightbox Functions
        // ============================================
        function openLightbox() {
            const lightbox = document.getElementById('lightbox');
            if (lightbox) {
                // Prevent scrolling on body
                document.body.style.overflow = 'hidden';
                
                lightbox.style.display = 'flex';
                // Find current image index
                const mainImg = document.getElementById('mainImage');
                let index = 0;
                if (mainImg) {
                    const currentSrc = mainImg.src;
                    const foundIndex = currentGalleryImages.findIndex(img => img.large_image_url === currentSrc);
                    if (foundIndex >= 0) index = foundIndex;
                }
                
                // Populate thumbnails
                populateLightboxThumbnails();
                
                showLightboxSlide(index);
            }
        }

        function populateLightboxThumbnails() {
            const container = document.getElementById('lightboxThumbnails');
            if (!container) return;
            
            container.innerHTML = '';
            
            if (currentGalleryImages && currentGalleryImages.length > 0) {
                currentGalleryImages.forEach((img, index) => {
                    const thumb = document.createElement('img');
                    thumb.src = img.small_image_url || img.large_image_url;
                    thumb.className = `lightbox-thumb ${index === currentLightboxIndex ? 'active' : ''}`;
                    thumb.onclick = () => showLightboxSlide(index);
                    container.appendChild(thumb);
                });
            }
        }

        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            if (lightbox) {
                lightbox.style.display = 'none';
                // Restore scrolling on body
                document.body.style.overflow = '';
            }
        }

        function moveLightbox(n) {
            showLightboxSlide(currentLightboxIndex + n);
        }

        function showLightboxSlide(n) {
            if (!currentGalleryImages || currentGalleryImages.length === 0) return;
            
            if (n >= currentGalleryImages.length) n = 0;
            if (n < 0) n = currentGalleryImages.length - 1;
            
            currentLightboxIndex = n;
            const img = document.getElementById('lightboxImage');
            if (img) {
                const imageObj = currentGalleryImages[n];
                img.src = imageObj.original_image_url || imageObj.large_image_url;
            }
            
            // Update active thumbnail
            const thumbs = document.querySelectorAll('.lightbox-thumb');
            thumbs.forEach((thumb, index) => {
                if (index === n) {
                    thumb.classList.add('active');
                    thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                } else {
                    thumb.classList.remove('active');
                }
            });
        }

        // Document Ready
        document.addEventListener('DOMContentLoaded', function() {
            // Initial thumb listeners
            attachThumbListeners();

            // Main Image Click -> Lightbox
            const mainImg = document.getElementById('mainImage');
            if (mainImg) {
                mainImg.addEventListener('click', openLightbox);
                mainImg.style.cursor = 'zoom-in';
            }

            // Lightbox close on outside click
            const lightbox = document.getElementById('lightbox');
            if (lightbox) {
                lightbox.addEventListener('click', function(e) {
                    if (e.target === lightbox) {
                        closeLightbox();
                    }
                });
            }
            
            // Initialize variant selection
            initVariantSelection();

            // Initialize review star rating
            initReviewStarRating();

            // Load reviews
            loadReviews();

            // Initialize voucher popup handlers
            initVoucherPopups();
        });

        // ============================================
        // Quantity Control Functions
        // ============================================
        function increaseQuantity() {
            const input = document.getElementById('quantity');
            if (input) {
                input.value = parseInt(input.value) + 1;
            }
        }

        function decreaseQuantity() {
            const input = document.getElementById('quantity');
            if (input && parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }

        // ============================================
        // Variant Selection Functions
        // ============================================
        function initVariantSelection() {
            const variants = document.querySelectorAll('.variant');
            
            variants.forEach(variant => {
                variant.addEventListener('click', function() {
                    const attributeId = this.getAttribute('data-attribute-id');
                    const optionId = this.getAttribute('data-option-id');
                    
                    // Remove active from siblings
                    const siblings = this.parentElement.querySelectorAll('.variant');
                    siblings.forEach(v => v.classList.remove('active'));
                    
                    // Add active to clicked
                    this.classList.add('active');
                    
                    // Update hidden input
                    const hiddenInput = document.getElementById('attribute_' + attributeId);
                    if (hiddenInput) {
                        hiddenInput.value = optionId;
                        // Trigger check for variant match
                        checkVariantMatch();
                    }
                });
            });
        }

        function checkVariantMatch() {
            if (!productConfig || !productConfig.index) return;

            const selectedOptions = {};
            let allSelected = true;

            // Get all attributes from config
            if (productConfig.attributes) {
                productConfig.attributes.forEach(attr => {
                   const input = document.getElementById('attribute_' + attr.id);
                   if(input && input.value) {
                       selectedOptions[attr.id] = input.value;
                   } else {
                       allSelected = false;
                   }
                });
            }

            // Only update images if we have a match (even partial? No, usually full match needed for specific variant images)
            // But if user wants Color to change images even if Size not selected:
            // Bagisto logic typically requires full match for "Variant", but we can try to find *any* variant that matches current selection?
            // "When I click to color variant, the variant's images are now showed".
            // If we want that behavior, we should look for *any* variant with this color.
            
            // Let's stick to strict match first, or try to find a variant that matches the *just clicked* attribute if others are missing?
            // Actually, standard behavior: find a variant that matches ALL selected options.
            // If only color is selected, we can't identify a unique variant ID easily unless we pick the first one.
            
            // However, the user said "When i click to color variant, the variant's images are now showed".
            // If they mean "I want them to show", I should probably try to show images for the *option* if possible.
            // But images are attached to variants (Red-S, Red-M).
            // Strategy: Find the first variant in `index` that matches the currently selected options.
            
            let matchedVariantId = null;

            // Iterate over all variants in index
            for (const [variantId, attributes] of Object.entries(productConfig.index)) {
                let isMatch = true;
                for (const [attrId, optionId] of Object.entries(selectedOptions)) {
                    // Check if this variant has this option for this attribute
                    if (attributes[attrId] != optionId) {
                        isMatch = false;
                        break;
                    }
                }
                
                if (isMatch) {
                    matchedVariantId = variantId;
                    // If we want *exact* match of all attributes (i.e. full variant selection), we should check if we have all attributes.
                    // But to support "Click Color -> Show Red Images" even if Size is empty:
                    // We pick the first variant that has this Color.
                    break; 
                }
            }

            if (matchedVariantId) {
                 // Check if this variant has images
                 if (productConfig.variant_images && productConfig.variant_images[matchedVariantId] && productConfig.variant_images[matchedVariantId].length) {
                     updateGallery(productConfig.variant_images[matchedVariantId]);
                 }
            }
        }

        // ============================================
        // Add to Cart Functions
        // ============================================
        async function addToCart() {
            const form = document.getElementById('productForm');
            const btn = document.getElementById('addToCartBtn');
            const btnText = document.getElementById('addToCartText');
            
            // Disable button
            btn.disabled = true;
            btnText.textContent = '{{ trans('ta-vpp-theme::app.products.view.adding') }}';
            
            // Set is_buy_now to 0
            document.getElementById('is_buy_now').value = '0';
            
            try {
                const formData = new FormData(form);
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });
                
                const data = await response.json();
                
                if (data.message) {
                    showMessage('success', data.message || '{{ trans('ta-vpp-theme::app.products.view.added-to-cart') }}');
                    
                    // Update mini cart count if exists
                    updateMiniCartCount();
                    
                } else if (data.redirect_uri) {
                    window.location.href = data.redirect_uri;
                } else if (data.error) {
                    showMessage('error', data.error);
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('error', 'Có lỗi xảy ra. Vui lòng thử lại.');
            } finally {
                // Re-enable button
                btn.disabled = false;
                btnText.textContent = 'Thêm vào giỏ hàng';
            }
        }

        async function buyNow() {
            const form = document.getElementById('productForm');
            const btn = document.getElementById('buyNowBtn');
            
            // Disable button
            btn.disabled = true;
            btn.textContent = 'Đang xử lý...';
            
            // Set is_buy_now to 1
            document.getElementById('is_buy_now').value = '1';
            
            try {
                const formData = new FormData(form);
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });
                
                const data = await response.json();
                
                if (data.redirect_uri) {
                    window.location.href = data.redirect_uri;
                } else {
                    // Redirect to checkout
                    window.location.href = '{{ route("shop.checkout.onepage.index") }}';
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('error', 'Có lỗi xảy ra. Vui lòng thử lại.');
                btn.disabled = false;
                btn.textContent = 'Mua ngay';
            }
        }

        // ============================================
        // Review Star Rating (from sample HTML)
        // ============================================
        let selectedRating = 0;

        function initReviewStarRating() {
            const inputStars = document.querySelectorAll('#input-stars i');
            
            inputStars.forEach(star => {
                star.addEventListener('mouseover', () => {
                    const value = parseInt(star.getAttribute('data-value'));
                    updateStars(value, 'hover');
                });

                star.addEventListener('mouseout', () => {
                    updateStars(selectedRating, 'active');
                });

                star.addEventListener('click', () => {
                    selectedRating = parseInt(star.getAttribute('data-value'));
                    updateStars(selectedRating, 'active');
                });
            });
        }

        function updateStars(value, className) {
            const inputStars = document.querySelectorAll('#input-stars i');
            inputStars.forEach(star => {
                const starValue = parseInt(star.getAttribute('data-value'));
                star.classList.remove('hover', 'active');
                if (starValue <= value) {
                    star.classList.add(className);
                }
            });
        }

        // ============================================
        // Submit Review Function
        // ============================================
        async function submitReview() {
            const comment = document.getElementById('reviewComment').value.trim();
            const submitBtn = document.querySelector('.btn-submit-review');
            const submitText = document.getElementById('reviewSubmitText');
            
            if (!selectedRating) {
                showMessage('error', 'Vui lòng chọn số sao đánh giá');
                return;
            }
            
            if (!comment) {
                showMessage('error', 'Vui lòng nhập nội dung đánh giá');
                return;
            }
            
            // Disable submit button
            submitBtn.disabled = true;
            submitText.textContent = 'Đang gửi...';
            
            try {
                const response = await fetch('{{ route("shop.api.products.reviews.store", $product->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        rating: selectedRating,
                        title: 'Review',
                        comment: comment
                    })
                });
                
                const data = await response.json();
                
                if (data.message) {
                    showMessage('success', data.message || 'Đánh giá của bạn đã được gửi thành công!');
                    
                    // Reset form
                    document.getElementById('reviewComment').value = '';
                    selectedRating = 0;
                    updateStars(0, 'active');
                    
                    // Reload reviews
                    setTimeout(() => {
                        loadReviews();
                    }, 1000);
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('error', 'Có lỗi xảy ra. Vui lòng thử lại.');
            } finally {
                submitBtn.disabled = false;
                submitText.textContent = 'Gửi nhận xét';
            }
        }

        // ============================================
        // Load Reviews Function
        // ============================================
        let currentReviewPage = 1;
        let hasMoreReviews = false;

        async function loadReviews(page = 1) {
            const reviewList = document.getElementById('reviewList');
            const loader = document.getElementById('reviewLoader');
            
            if (loader) {
                loader.style.display = 'block';
            }
            
            try {
                const response = await fetch('{{ route("shop.api.products.reviews.index", $product->id) }}?page=' + page);
                const data = await response.json();
                
                if (loader) {
                    loader.style.display = 'none';
                }
                
                if (page === 1) {
                    // Clear existing reviews
                    reviewList.innerHTML = '';
                }
                
                if (data.data && data.data.length > 0) {
                    data.data.forEach(review => {
                        const reviewHtml = createReviewHTML(review);
                        reviewList.insertAdjacentHTML('beforeend', reviewHtml);
                    });
                    
                    // Check if there are more reviews
                    hasMoreReviews = !!data.links?.next;
                    currentReviewPage = page;
                    
                    // Add load more button if needed
                    updateLoadMoreButton();
                } else if (page === 1) {
                    reviewList.innerHTML = '<div style="text-align: center; padding: 40px; color: #999;">Chưa có đánh giá nào</div>';
                }
            } catch (error) {
                console.error('Error loading reviews:', error);
                if (loader) {
                    loader.style.display = 'none';
                }
                if (page === 1) {
                    reviewList.innerHTML = '<div style="text-align: center; padding: 40px; color: #999;">Không thể tải đánh giá</div>';
                }
            }
        }

        function createReviewHTML(review) {
            const date = new Date(review.created_at).toLocaleDateString('vi-VN');
            let starsHtml = '';
            
            for (let i = 1; i <= 5; i++) {
                if (i <= review.rating) {
                    starsHtml += '<i class="fa-solid fa-star"></i>';
                } else {
                    starsHtml += '<i class="fa-regular fa-star"></i>';
                }
            }
            
            return `
                <div class="review-item">
                    <div class="reviewer-info">
                        <div class="name">${review.customer_name || 'Khách hàng'}</div>
                        <div class="date">${date}</div>
                    </div>
                    <div class="review-content">
                        <div class="stars">${starsHtml}</div>
                        <p>${review.comment}</p>
                    </div>
                </div>
            `;
        }

        function updateLoadMoreButton() {
            // Remove existing load more button
            const existingBtn = document.getElementById('loadMoreReviewsBtn');
            if (existingBtn) {
                existingBtn.remove();
            }
            
            if (hasMoreReviews) {
                const reviewList = document.getElementById('reviewList');
                const loadMoreHtml = `
                    <div id="loadMoreReviewsBtn" style="text-align: center; margin-top: 20px;">
                        <button class="btn" onclick="loadMoreReviews()">
                            <span id="loadMoreText">Xem thêm đánh giá</span>
                        </button>
                    </div>
                `;
                reviewList.insertAdjacentHTML('beforeend', loadMoreHtml);
            }
        }

        async function loadMoreReviews() {
            const loadMoreText = document.getElementById('loadMoreText');
            if (loadMoreText) {
                loadMoreText.textContent = 'Đang tải...';
            }
            await loadReviews(currentReviewPage + 1);
        }

        // ============================================
        // Utility Functions
        // ============================================
        function showMessage(type, message) {
            // Create a simple alert div
            const alertDiv = document.createElement('div');
            alertDiv.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                background: ${type === 'success' ? '#27ae60' : '#e74c3c'};
                color: white;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                z-index: 10000;
                max-width: 400px;
                animation: slideIn 0.3s ease-out;
            `;
            alertDiv.textContent = message;
            
            document.body.appendChild(alertDiv);
            
            // Remove after 3 seconds
            setTimeout(() => {
                alertDiv.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => alertDiv.remove(), 300);
            }, 3000);
        }

        function updateMiniCartCount() {
            // Reload page to update mini cart
            // You can implement a more sophisticated approach if needed
            fetch('{{ route("shop.api.checkout.cart.index") }}')
                .then(response => response.json())
                .then(data => {
                    // Update cart count in header if badge exists
                    const badge = document.querySelector('.badge-count');
                    if (badge && data.data && data.data.items_count) {
                        badge.textContent = data.data.items_count;
                    }
                })
                .catch(error => console.error('Error updating cart count:', error));
        }

        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);

        // ============================================
        // Voucher Popup Functions
        // ============================================
        function initVoucherPopups() {
            const voucherItems = document.querySelectorAll('.voucher-item');
            
            voucherItems.forEach(item => {
                const popup = item.querySelector('.voucher-popup');
                if (!popup) return;
                
                // Show popup when hovering over the item
                item.addEventListener('mouseenter', () => {
                    item.classList.add('hover-active');
                });
                
                // Hide popup when leaving the item
                item.addEventListener('mouseleave', () => {
                    item.classList.remove('hover-active');
                });
                
                // Prevent click events from bubbling (so clicking in popup doesn't trigger other actions)
                popup.addEventListener('click', (e) => {
                    e.stopPropagation();
                });
            });
        }

        // ============================================
        // Copy Voucher Code Function
        // ============================================
        window.copyVoucherCode = function(code, buttonElement) {
            const originalHTML = buttonElement ? buttonElement.innerHTML : '';
            
            // Try modern clipboard API first
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(code).then(() => {
                    showMessage('success', `Đã sao chép mã: ${code}`);
                    if (buttonElement) {
                        buttonElement.innerHTML = '<i class="fa-solid fa-check"></i>';
                        buttonElement.style.background = '#27ae60';
                        setTimeout(() => {
                            buttonElement.innerHTML = originalHTML;
                            buttonElement.style.background = '';
                        }, 2000);
                    }
                }).catch(err => {
                    console.error('Clipboard API failed:', err);
                    fallbackCopyVoucher(code, buttonElement, originalHTML);
                });
            } else {
                fallbackCopyVoucher(code, buttonElement, originalHTML);
            }
        };

        // Fallback copy method for older browsers
        function fallbackCopyVoucher(code, buttonElement, originalHTML) {
            const input = document.createElement('input');
            input.value = code;
            input.style.position = 'fixed';
            input.style.opacity = '0';
            document.body.appendChild(input);
            input.select();
            input.setSelectionRange(0, 99999); // For mobile devices
            
            try {
                document.execCommand('copy');
                showMessage('success', `Đã sao chép mã: ${code}`);
                if (buttonElement) {
                    buttonElement.innerHTML = '<i class="fa-solid fa-check"></i>';
                    buttonElement.style.background = '#27ae60';
                    setTimeout(() => {
                        buttonElement.innerHTML = originalHTML;
                        buttonElement.style.background = '';
                    }, 2000);
                }
            } catch (err) {
                console.error('Copy failed:', err);
                showMessage('error', 'Không thể sao chép mã. Vui lòng thử lại.');
            }
            
            document.body.removeChild(input);
        }
    </script>
    @endpush
</x-ta-vpp-theme::layouts>
