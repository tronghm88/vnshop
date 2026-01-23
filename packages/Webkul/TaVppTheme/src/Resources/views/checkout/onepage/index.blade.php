@php
    $cart = cart()->getCart();
@endphp

<x-ta-vpp-theme::layouts>
    <x-slot:title>
        {{ trans('shop::app.checkout.onepage.index.checkout') }}
    </x-slot>

    <div class="container">
        <div class="breadcrumb">{{ trans('shop::app.checkout.onepage.index.home') }} / {{ trans('shop::app.checkout.onepage.index.checkout') }}</div>
        
        <form id="checkout-form">
            @csrf
            <div class="checkout-grid">
                <section>
                    {{-- Address Section --}}
                    <div class="auth-card" id="address-section">
                        <h2>{{ trans('shop::app.checkout.onepage.address.shipping-address') }}</h2>
                        <div class="form-grid">
                            @if (auth()->guard('customer')->check() && auth()->guard('customer')->user()->addresses->count() > 0)
                                <div class="form-group full-width">
                                    <label>{{ trans('shop::app.checkout.onepage.address.saved-addresses') }}</label>
                                    <select id="saved-address-selector">
                                        <option value="">{{ trans('shop::app.checkout.onepage.address.add-new') }}</option>
                                        @foreach (auth()->guard('customer')->user()->addresses as $address)
                                            <option value="{{ $address->id }}" 
                                                data-first_name="{{ $address->first_name }}"
                                                data-last_name="{{ $address->last_name }}"
                                                data-email="{{ $address->email }}"
                                                data-phone="{{ $address->phone }}"
                                                data-address1="{{ $address->address1 }}"
                                                data-city="{{ $address->city }}"
                                                data-state="{{ $address->state }}"
                                                data-country="{{ $address->country }}"
                                                data-postcode="{{ $address->postcode }}"
                                            >
                                                {{ $address->first_name }} {{ $address->last_name }} - {{ $address->address1 }}, {{ $address->city }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="first_name">{{ trans('shop::app.checkout.onepage.address.first-name') }}</label>
                                <input type="text" id="first_name" name="billing[first_name]" value="{{ auth()->guard('customer')->user()?->first_name }}" required placeholder="{{ trans('shop::app.checkout.onepage.address.first-name') }}" />
                            </div>
                            <div class="form-group">
                                <label for="last_name">{{ trans('shop::app.checkout.onepage.address.last-name') }}</label>
                                <input type="text" id="last_name" name="billing[last_name]" value="{{ auth()->guard('customer')->user()?->last_name }}" required placeholder="{{ trans('shop::app.checkout.onepage.address.last-name') }}" />
                            </div>
                            <div class="form-group">
                                <label for="email">{{ trans('shop::app.checkout.onepage.address.email') }}</label>
                                <input type="email" id="email" name="billing[email]" value="{{ auth()->guard('customer')->user()?->email }}" required placeholder="you@email.com" />
                            </div>
                            <div class="form-group">
                                <label for="phone">{{ trans('shop::app.checkout.onepage.address.telephone') }}</label>
                                <input type="tel" id="phone" name="billing[phone]" value="{{ auth()->guard('customer')->user()?->phone }}" required placeholder="0xxx xxx xxx" />
                            </div>
                            <div class="form-group full-width">
                                <label for="address">{{ trans('shop::app.checkout.onepage.address.title') }}</label>
                                <input type="text" id="address" name="billing[address][]" required placeholder="{{ trans('shop::app.checkout.onepage.address.title') }}" />
                            </div>
                            <div class="form-group" style="display: none;">
                                <label for="country">{{ trans('shop::app.checkout.onepage.address.select-country') }}</label>
                                <select id="country" name="billing[country]" required>
                                    <option value="VN" selected>Vietnam</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="state">{{ trans('shop::app.checkout.onepage.address.select-state') }}</label>
                                <select id="state" name="billing[state]" required>
                                    <option value="">{{ trans('shop::app.checkout.onepage.address.select-state') }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="city">{{ trans('shop::app.checkout.onepage.address.select-ward') }}</label>
                                <select id="city" name="billing[city]" required>
                                    <option value="">{{ trans('shop::app.checkout.onepage.address.select-ward') }}</option>
                                </select>
                            </div>

                            <input type="hidden" name="billing[use_for_shipping]" value="1" />
                            <input type="hidden" name="billing[postcode]" value="10000" />
                        </div>
                    </div>

                    {{-- Shipping Method Section --}}
                    <div class="auth-card" id="shipping-section" style="margin-top: 24px;">
                        <h2>{{ trans('shop::app.checkout.onepage.shipping.shipping-method') }}</h2>
                        <div id="shipping-methods-container" class="form-grid">
                            <div class="shipping-placeholder" style="padding: 20px; text-align: center; color: #666; background: #f5f5f5; border-radius: 8px;">
                                <p style="margin: 0;">{{ trans('shop::app.checkout.onepage.shipping.select-address-first') }}</p>
                            </div>
                        </div>
                        <div id="shipping-breakdown" style="margin-top: 15px; padding: 15px; background: #fff3e0; border-radius: 8px; border-left: 4px solid #f57c00; display: none;">
                            <h4 style="margin: 0 0 10px 0; font-size: 14px; font-weight: 600; color: #e65100;">⚠️ Phụ phí áp dụng</h4>
                            <div id="shipping-breakdown-content"></div>
                        </div>
                    </div>

                    {{-- Payment Method Section --}}
                    <div class="auth-card" id="payment-section" style="margin-top: 24px;">
                        <h2>{{ trans('shop::app.checkout.onepage.payment.payment-method') }}</h2>
                        <div id="payment-methods-container" class="form-grid">
                            {{-- Payment methods will be injected here --}}
                        </div>
                    </div>
                </section>

                <aside>
                    <div class="summary-card">
                        <h3>{{ trans('shop::app.checkout.onepage.summary.cart-summary') }}</h3>
                        @foreach ($cart->items as $item)
                            <div class="cart-item">
                                @php
                                    $images = $item->product->getTypeInstance()->getBaseImage($item);
                                @endphp
                                <div class="cart-image">
                                    <img src="{{ $images['small_image_url'] }}" alt="{{ $item->name }}" style="width: 100%; border-radius: 4px;" />
                                </div>
                                <div style="flex: 1; padding: 0 10px;">
                                    <div class="product-title">{{ $item->name }}</div>
                                    <div class="product-meta">x {{ $item->quantity }}</div>
                                </div>
                                <div class="price">{{ core()->currency($item->base_total) }}</div>
                            </div>
                        @endforeach
                        
                        <div class="summary-line">
                            <span>{{ trans('shop::app.checkout.onepage.summary.sub-total') }}</span>
                            <span id="summary-subtotal">{{ core()->currency($cart->base_sub_total) }}</span>
                        </div>
                        <div class="summary-line" id="shipping-rate-line" style="display: none;">
                            <span>{{ trans('shop::app.checkout.onepage.summary.delivery-charges') }}</span>
                            <span id="summary-shipping">0 đ</span>
                        </div>
                        @if ($cart->base_tax_total > 0)
                            <div class="summary-line">
                                <span>{{ trans('shop::app.checkout.onepage.summary.tax') }}</span>
                                <span id="summary-tax">{{ core()->currency($cart->base_tax_total) }}</span>
                            </div>
                        @endif
                        @if ($cart->base_discount_amount > 0)
                            <div class="summary-line">
                                <span>{{ trans('shop::app.checkout.onepage.summary.discount-amount') }}</span>
                                <span id="summary-discount">-{{ core()->currency($cart->base_discount_amount) }}</span>
                            </div>
                        @endif
                        <div class="summary-line summary-total">
                            <span>{{ trans('shop::app.checkout.onepage.summary.grand-total') }}</span>
                            <span id="summary-grandtotal">{{ core()->currency($cart->base_grand_total) }}</span>
                        </div>
                        <button type="button" id="place-order-btn" class="btn btn-primary" style="display: none;" disabled>
                            {{ trans('shop::app.checkout.onepage.summary.place-order') }}
                        </button>
                    </div>
                </aside>
            </div>
        </form>
    </div>

    <x-slot:scripts>
        <script>
            (function() {
                let currentShippingData = null;

                const initVietnamProvinces = () => {
                    const provinceSelect = document.getElementById('state');
                    const wardSelect = document.getElementById('city');
                    const addressSelector = document.getElementById('saved-address-selector');

                    if (!provinceSelect || !wardSelect) return;

                    // Helper to load wards
                    const loadWards = (provinceCode, selectedWard = null) => {
                        wardSelect.innerHTML = '<option value="">{{ trans('shop::app.checkout.onepage.address.select-ward') }}</option>';
                        
                        if (!provinceCode) return;

                        fetch(`https://provinces.open-api.vn/api/v2/w?province=${provinceCode}`)
                            .then(res => res.json())
                            .then(wards => {
                                wards.forEach(ward => {
                                    const option = document.createElement('option');
                                    option.value = ward.name;
                                    option.textContent = ward.name;
                                    if (selectedWard && (ward.name === selectedWard || ward.codename === selectedWard)) {
                                        option.selected = true;
                                    }
                                    wardSelect.appendChild(option);
                                });
                                
                                // If a ward was pre-selected, trigger shipping calculation
                                if (selectedWard) {
                                    setTimeout(() => calculateShippingFee(), 100);
                                }
                            })
                            .catch(err => console.error('Error loading wards:', err));
                    };

                    // Load Provinces
                    fetch('https://provinces.open-api.vn/api/v2/p')
                        .then(res => res.json())
                        .then(provinces => {
                            // Keep the first option
                            const firstOption = provinceSelect.options[0];
                            provinceSelect.innerHTML = '';
                            provinceSelect.appendChild(firstOption);

                            provinces.forEach(province => {
                                const option = document.createElement('option');
                                option.value = province.name;
                                option.dataset.code = province.code;
                                option.textContent = province.name;
                                provinceSelect.appendChild(option);
                            });

                            // Check if there's a value already set
                            const currentValue = provinceSelect.getAttribute('data-initial-value') || provinceSelect.value;
                            if (currentValue) {
                                for (let i = 0; i < provinceSelect.options.length; i++) {
                                    if (provinceSelect.options[i].value === currentValue) {
                                        provinceSelect.selectedIndex = i;
                                        const code = provinceSelect.options[i].dataset.code;
                                        loadWards(code, wardSelect.getAttribute('data-initial-value') || wardSelect.value);
                                        break;
                                    }
                                }
                            }
                        })
                        .catch(err => console.error('Error loading provinces:', err));

                    // Handle Province Change
                    provinceSelect.addEventListener('change', function() {
                        const option = this.options[this.selectedIndex];
                        loadWards(option ? option.dataset.code : null);
                        
                        // Reset shipping when province changes
                        resetShippingDisplay();
                    });

                    // Handle Ward Change
                    wardSelect.addEventListener('change', function() {
                        if (this.value && provinceSelect.value) {
                            calculateShippingFee();
                        } else {
                            resetShippingDisplay();
                        }
                    });

                    // Sync with saved address selector
                    if (addressSelector) {
                        addressSelector.addEventListener('change', function() {
                            const option = this.options[this.selectedIndex];
                            if (!option || !option.value) return;

                            // Small delay to let other scripts set values first
                            setTimeout(() => {
                                const stateName = document.getElementById('state').value;
                                const cityName = document.getElementById('city').value;

                                // Find the option in province select to get the code
                                for (let i = 0; i < provinceSelect.options.length; i++) {
                                    if (provinceSelect.options[i].value === stateName) {
                                        provinceSelect.selectedIndex = i;
                                        loadWards(provinceSelect.options[i].dataset.code, cityName);
                                        break;
                                    }
                                }
                            }, 50);
                        });
                    }
                };

                const calculateShippingFee = () => {
                    const provinceSelect = document.getElementById('state');
                    const ward = document.getElementById('city').value;

                    if (!provinceSelect.value || !ward) {
                        resetShippingDisplay();
                        return;
                    }

                    // Get the province code from the selected option
                    const selectedOption = provinceSelect.options[provinceSelect.selectedIndex];
                    const provinceCode = selectedOption.dataset.code;

                    if (!provinceCode) {
                        console.error('Province code not found');
                        resetShippingDisplay();
                        return;
                    }

                    // Show loading state
                    const container = document.getElementById('shipping-methods-container');
                    container.innerHTML = '<div style="padding: 20px; text-align: center;"><p>Đang tính phí vận chuyển...</p></div>';

                    // Call API to calculate shipping
                    fetch('{{ route('tavpp.shipping.calculate') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            province_code: provinceCode,
                            ward: ward
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            currentShippingData = data;
                            displayShippingDetails(data);
                            updateOrderSummary(data);
                            loadPaymentMethods();
                        } else {
                            container.innerHTML = `<div style="padding: 20px; text-align: center; color: #d32f2f;"><p>${data.error || 'Không thể tính phí vận chuyển'}</p></div>`;
                        }
                    })
                    .catch(err => {
                        console.error('Error calculating shipping:', err);
                        container.innerHTML = '<div style="padding: 20px; text-align: center; color: #d32f2f;"><p>Đã xảy ra lỗi khi tính phí vận chuyển</p></div>';
                    });
                };

                const displayShippingDetails = (data) => {
                    const container = document.getElementById('shipping-methods-container');
                    
                    // Display shipping method with radio button
                    let html = `
                        <div style="border: 2px solid #4caf50; border-radius: 8px; padding: 15px; background: #f1f8f4;">
                            <label style="display: flex; align-items: start; cursor: pointer; margin: 0;">
                                <input type="radio" name="shipping_method" value="vn_regional_shipping_vn_regional_shipping" 
                                       checked style="margin-top: 4px; margin-right: 10px;">
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; margin-bottom: 5px;">
                                        Vận chuyển nội địa - ${data.region_name}
                                    </div>
                                    <div style="font-size: 14px; color: #666; margin-bottom: 8px;">
                                        ${data.breakdown_text}
                                    </div>
                                    <div style="font-weight: 700; color: #2e7d32; font-size: 16px;">
                                        ${data.total_price_formatted}
                                    </div>
                                </div>
                            </label>
                        </div>
                    `;
                    
                    container.innerHTML = html;

                    // Display detailed breakdown - only show if there are surcharges
                    const breakdownContainer = document.getElementById('shipping-breakdown');
                    const breakdownContent = document.getElementById('shipping-breakdown-content');
                    
                    const hasSurcharges = data.dimension_surcharge > 0 || data.weight_surcharge > 0;
                    
                    if (hasSurcharges) {
                        let breakdownHtml = '<div style="font-size: 13px;">';
                        
                        // Filter items that have surcharges
                        const itemsWithSurcharges = data.items.filter(item => item.dimension_surcharge > 0);
                        
                        if (itemsWithSurcharges.length > 0) {
                            breakdownHtml += '<div style="margin-bottom: 10px;"><strong>Sản phẩm có phụ phí kích thước:</strong></div>';
                            
                            itemsWithSurcharges.forEach(item => {
                                breakdownHtml += `
                                    <div style="margin-left: 10px; margin-bottom: 8px; padding: 8px; background: white; border-radius: 4px; border-left: 3px solid #f57c00;">
                                        <div style="font-weight: 600; margin-bottom: 4px;">${item.name} (x${item.quantity})</div>
                                        <div style="color: #666; font-size: 12px;">
                                            Kích thước: ${item.dimensions}
                                            <br>Trọng lượng quy đổi: ${item.volumetric_weight} kg
                                            <br><span style="color: #f57c00; font-weight: 600;">Phụ phí: +${new Intl.NumberFormat('vi-VN').format(item.dimension_surcharge)} đ</span>
                                        </div>
                                    </div>
                                `;
                            });
                        }
                        
                        if (data.weight_surcharge > 0) {
                            breakdownHtml += `
                                <div style="margin-top: 10px; padding: 8px; background: white; border-radius: 4px; border-left: 3px solid #f57c00;">
                                    <strong>Phụ phí cân nặng tổng:</strong>
                                    <div style="color: #666; font-size: 12px; margin-top: 4px;">
                                        Tổng cân nặng đơn hàng: ${data.total_weight} kg
                                        <br><span style="color: #f57c00; font-weight: 600;">Phụ phí: +${data.weight_surcharge_formatted}</span>
                                    </div>
                                </div>
                            `;
                        }
                        
                        breakdownHtml += '</div>';
                        
                        breakdownContent.innerHTML = breakdownHtml;
                        breakdownContainer.style.display = 'block';
                    } else {
                        // No surcharges, hide the breakdown section
                        breakdownContainer.style.display = 'none';
                    }
                };

                const resetShippingDisplay = () => {
                    const container = document.getElementById('shipping-methods-container');
                    container.innerHTML = `
                        <div class="shipping-placeholder" style="padding: 20px; text-align: center; color: #666; background: #f5f5f5; border-radius: 8px;">
                            <p style="margin: 0;">Vui lòng chọn tỉnh/thành phố và phường/xã để tính phí vận chuyển</p>
                        </div>
                    `;
                    
                    document.getElementById('shipping-breakdown').style.display = 'none';
                    currentShippingData = null;
                    
                    // Reset order summary
                    document.getElementById('shipping-rate-line').style.display = 'none';
                    updateGrandTotal();
                };

                const updateOrderSummary = (data) => {
                    const shippingLine = document.getElementById('shipping-rate-line');
                    const shippingAmount = document.getElementById('summary-shipping');
                    
                    shippingAmount.textContent = data.total_price_formatted;
                    shippingLine.style.display = 'flex';
                    
                    updateGrandTotal();
                };

                const updateGrandTotal = () => {
                    const subtotalText = document.getElementById('summary-subtotal').textContent;
                    const subtotal = parseFloat(subtotalText.replace(/[^\d]/g, ''));
                    
                    const taxText = document.getElementById('summary-tax')?.textContent || '0';
                    const tax = parseFloat(taxText.replace(/[^\d]/g, ''));
                    
                    const discountText = document.getElementById('summary-discount')?.textContent || '0';
                    const discount = parseFloat(discountText.replace(/[^\d]/g, ''));
                    
                    const shippingText = document.getElementById('summary-shipping').textContent;
                    const shipping = parseFloat(shippingText.replace(/[^\d]/g, ''));
                    
                    const grandTotal = subtotal + tax + shipping - discount;
                    
                    document.getElementById('summary-grandtotal').textContent = 
                        new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(grandTotal);
                };

                const loadPaymentMethods = () => {
                    // Load payment methods (placeholder - adjust based on your actual implementation)
                    const paymentContainer = document.getElementById('payment-methods-container');
                    
                    if (!paymentContainer.innerHTML.trim() || paymentContainer.innerHTML.includes('placeholder')) {
                        // You can fetch payment methods via AJAX or display default ones
                        paymentContainer.innerHTML = `
                            <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin-bottom: 10px;">
                                <label style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                                    <input type="radio" name="payment_method" value="cashondelivery" checked style="margin-right: 10px;">
                                    <div>
                                        <div style="font-weight: 600;">Thanh toán khi nhận hàng (COD)</div>
                                        <div style="font-size: 14px; color: #666; margin-top: 5px;">Thanh toán bằng tiền mặt khi nhận hàng</div>
                                    </div>
                                </label>
                            </div>
                        `;
                        
                        // Enable place order button
                        const placeOrderBtn = document.getElementById('place-order-btn');
                        placeOrderBtn.style.display = 'block';
                        placeOrderBtn.disabled = false;
                    }
                };

                // Initialize on page load
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initVietnamProvinces);
                } else {
                    initVietnamProvinces();
                }

                // Handle place order button
                document.addEventListener('click', async function(e) {
                    if (e.target.id === 'place-order-btn') {
                        e.preventDefault();
                        
                        const placeOrderBtn = e.target;
                        
                        // Validate form
                        const form = document.getElementById('checkout-form');
                        if (!form.checkValidity()) {
                            form.reportValidity();
                            return;
                        }

                        // Check if shipping method is selected
                        if (!currentShippingData) {
                            alert('Vui lòng chọn địa chỉ giao hàng để tính phí vận chuyển');
                            return;
                        }

                        // Check if payment method is selected
                        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
                        if (!paymentMethod) {
                            alert('Vui lòng chọn phương thức thanh toán');
                            return;
                        }

                        // Disable button and show loading
                        placeOrderBtn.disabled = true;
                        placeOrderBtn.textContent = 'Đang xử lý...';

                        try {
                            const formData = new FormData(form);
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                            
                            // Step 1: Save address
                            const addressData = {
                                billing: {
                                    first_name: formData.get('billing[first_name]'),
                                    last_name: formData.get('billing[last_name]'),
                                    email: formData.get('billing[email]'),
                                    phone: formData.get('billing[phone]'),
                                    address: [formData.get('billing[address][]')],
                                    country: formData.get('billing[country]'),
                                    state: formData.get('billing[state]'),
                                    city: formData.get('billing[city]'),
                                    postcode: formData.get('billing[postcode]'),
                                    use_for_shipping: true
                                }
                            };

                            const addressResponse = await fetch('/api/checkout/onepage/addresses', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify(addressData)
                            });

                            if (!addressResponse.ok) {
                                throw new Error('Lỗi khi lưu địa chỉ');
                            }

                            // Step 2: Save shipping method
                            const shippingMethod = document.querySelector('input[name="shipping_method"]:checked');
                            const shippingResponse = await fetch('/api/checkout/onepage/shipping-methods', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({
                                    shipping_method: shippingMethod.value
                                })
                            });

                            if (!shippingResponse.ok) {
                                throw new Error('Lỗi khi lưu phương thức vận chuyển');
                            }

                            // Step 3: Save payment method
                            const paymentResponse = await fetch('/api/checkout/onepage/payment-methods', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({
                                    payment: {
                                        method: paymentMethod.value
                                    }
                                })
                            });

                            if (!paymentResponse.ok) {
                                throw new Error('Lỗi khi lưu phương thức thanh toán');
                            }

                            // Step 4: Place order
                            const orderResponse = await fetch('/api/checkout/onepage/orders', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({})
                            });

                            const orderResult = await orderResponse.json();

                            if (!orderResponse.ok) {
                                throw new Error(orderResult.message || 'Lỗi khi tạo đơn hàng');
                            }

                            // Success! Redirect to success page
                            if (orderResult.redirect_url) {
                                window.location.href = orderResult.redirect_url;
                            } else {
                                window.location.href = '/checkout/onepage/success';
                            }

                        } catch (error) {
                            console.error('Checkout error:', error);
                            alert(error.message || 'Đã xảy ra lỗi khi đặt hàng. Vui lòng thử lại.');
                            
                            // Re-enable button
                            placeOrderBtn.disabled = false;
                            placeOrderBtn.textContent = '{{ trans('shop::app.checkout.onepage.summary.place-order') }}';
                        }
                    }
                });
            })();
        </script>
    </x-slot:scripts>
</x-ta-vpp-theme::layouts>
