document.addEventListener('DOMContentLoaded', function () {
    const checkoutForm = document.getElementById('checkout-form');
    if (!checkoutForm) return;

    const addressSection = document.getElementById('address-section');
    const shippingSection = document.getElementById('shipping-section');
    const paymentSection = document.getElementById('payment-section');
    const confirmAddressBtn = document.getElementById('confirm-address-btn');
    const confirmShippingBtn = document.getElementById('confirm-shipping-btn');
    const placeOrderBtn = document.getElementById('place-order-btn');

    const shippingMethodsContainer = document.getElementById('shipping-methods-container');
    const paymentMethodsContainer = document.getElementById('payment-methods-container');

    const summaryShipping = document.getElementById('summary-shipping');
    const summaryGrandtotal = document.getElementById('summary-grandtotal');
    const shippingRateLine = document.getElementById('shipping-rate-line');

    /**
     * Saved Address Selector
     */
    const addressSelector = document.getElementById('saved-address-selector');
    if (addressSelector) {
        addressSelector.addEventListener('change', function () {
            const option = this.options[this.selectedIndex];
            if (!option.value) {
                // Reset form for new address
                return;
            }

            document.getElementById('first_name').value = option.dataset.first_name || '';
            document.getElementById('last_name').value = option.dataset.last_name || '';
            document.getElementById('email').value = option.dataset.email || '';
            document.getElementById('phone').value = option.dataset.phone || '';
            document.getElementById('address1').value = option.dataset.address1 || '';
            document.getElementById('city').value = option.dataset.city || '';
            document.getElementById('state').value = option.dataset.state || '';
            document.getElementById('country').value = option.dataset.country || 'VN';
            document.getElementById('postcode').value = option.dataset.postcode || '';
        });
    }

    /**
     * Confirm Address
     */
    confirmAddressBtn.addEventListener('click', function () {
        const formData = new FormData(checkoutForm);
        const data = {};

        // Convert FormData to nested object for Bagisto API
        for (let [key, value] of formData.entries()) {
            if (key.includes('[')) {
                const parts = key.split(/[\[\]]+/).filter(p => p !== '');
                let current = data;
                for (let i = 0; i < parts.length; i++) {
                    const part = parts[i];
                    if (i === parts.length - 1) {
                        if (key.endsWith('[]')) {
                            if (!current[part]) current[part] = [];
                            current[part].push(value);
                        } else {
                            current[part] = value;
                        }
                    } else {
                        if (!current[part]) current[part] = {};
                        current = current[part];
                    }
                }
            } else {
                data[key] = value;
            }
        }

        confirmAddressBtn.disabled = true;
        confirmAddressBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';

        fetch('/api/checkout/onepage/addresses', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(res => {
                if (res.data && res.data.shippingMethods) {
                    renderShippingMethods(res.data.shippingMethods);
                    shippingSection.style.display = 'block';
                    shippingSection.scrollIntoView({ behavior: 'smooth' });
                } else if (res.message) {
                    showFlashMessage('error', res.message);
                }
                confirmAddressBtn.disabled = false;
                confirmAddressBtn.innerHTML = 'Xác nhận địa chỉ';
            })
            .catch(error => {
                console.error('Error:', error);
                showFlashMessage('error', 'Có lỗi xảy ra khi lưu địa chỉ');
                confirmAddressBtn.disabled = false;
                confirmAddressBtn.innerHTML = 'Xác nhận địa chỉ';
            });
    });

    /**
     * Render Shipping Methods
     */
    function renderShippingMethods(methods) {
        shippingMethodsContainer.innerHTML = '';
        Object.keys(methods).forEach(key => {
            const group = methods[key];
            group.rates.forEach(rate => {
                const div = document.createElement('div');
                div.className = 'form-group full-width';
                div.innerHTML = `
                    <label class="method-option">
                        <input type="radio" name="shipping_method" value="${rate.method}" data-price="${rate.base_price}" data-formatted-price="${rate.base_formatted_price}">
                        <span class="radio-checkmark"></span>
                        <div class="method-info">
                            <span class="method-title">${rate.method_title}</span>
                            <span class="method-description"><strong>${rate.base_formatted_price}</strong></span>
                        </div>
                    </label>
                `;
                shippingMethodsContainer.appendChild(div);
            });
        });

        // Add change listener to radio buttons
        shippingMethodsContainer.querySelectorAll('input').forEach(input => {
            input.addEventListener('change', function () {
                // Remove selected class from all options in this group
                shippingMethodsContainer.querySelectorAll('.method-option').forEach(el => el.classList.remove('selected'));
                // Add selected class to current option
                this.closest('.method-option').classList.add('selected');

                summaryShipping.textContent = this.dataset.formatted_price;
                shippingRateLine.style.display = 'flex';
            });
        });
    }

    /**
     * Confirm Shipping
     */
    confirmShippingBtn.addEventListener('click', function () {
        const selectedMethod = checkoutForm.querySelector('input[name="shipping_method"]:checked');
        if (!selectedMethod) {
            showFlashMessage('warning', 'Vui lòng chọn phương thức vận chuyển');
            return;
        }

        confirmShippingBtn.disabled = true;
        confirmShippingBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';

        fetch('/api/checkout/onepage/shipping-methods', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ shipping_method: selectedMethod.value })
        })
            .then(response => response.json())
            .then(res => {
                if (res.payment_methods || (res.data && res.data.payment_methods) || Array.isArray(res)) {
                    // Bagisto API might return payment methods directly or wrapped
                    const methods = Array.isArray(res) ? res : (res.payment_methods || res.data.payment_methods);
                    renderPaymentMethods(methods);
                    paymentSection.style.display = 'block';
                    paymentSection.scrollIntoView({ behavior: 'smooth' });
                    placeOrderBtn.style.display = 'block';
                }
                confirmShippingBtn.disabled = false;
                confirmShippingBtn.innerHTML = 'Xác nhận vận chuyển';
            })
            .catch(error => {
                console.error('Error:', error);
                showFlashMessage('error', 'Có lỗi xảy ra khi lưu phương thức vận chuyển');
                confirmShippingBtn.disabled = false;
                confirmShippingBtn.innerHTML = 'Xác nhận vận chuyển';
            });
    });

    /**
     * Render Payment Methods
     */
    function renderPaymentMethods(methods) {
        paymentMethodsContainer.innerHTML = '';
        methods.forEach(method => {
            const div = document.createElement('div');
            div.className = 'form-group full-width';
            div.innerHTML = `
                <label class="method-option">
                    <input type="radio" name="payment[method]" value="${method.method}">
                    <span class="radio-checkmark"></span>
                    <div class="method-info">
                        <span class="method-title">${method.method_title}</span>
                        <span class="method-description">${method.description || ''}</span>
                    </div>
                </label>
            `;
            paymentMethodsContainer.appendChild(div);
        });

        // Add change listener
        paymentMethodsContainer.querySelectorAll('input').forEach(input => {
            input.addEventListener('change', function () {
                // Remove selected class from all options in this group
                paymentMethodsContainer.querySelectorAll('.method-option').forEach(el => el.classList.remove('selected'));
                // Add selected class to current option
                this.closest('.method-option').classList.add('selected');

                savePaymentMethod(this.value);
            });
        });
    }

    /**
     * Save Payment Method
     */
    function savePaymentMethod(method) {
        fetch('/api/checkout/onepage/payment-methods', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ payment: { method: method } })
        })
            .then(response => response.json())
            .then(res => {
                if (res.cart || res.data) {
                    const cart = res.cart || res.data.cart;
                    // Update totals from API response
                    summaryGrandtotal.textContent = cart.formatted_grand_total;
                    placeOrderBtn.disabled = false;
                }
            });
    }

    /**
     * Place Order
     */
    placeOrderBtn.addEventListener('click', function () {
        placeOrderBtn.disabled = true;
        placeOrderBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang đặt hàng...';

        fetch('/api/checkout/onepage/orders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(response => response.json())
            .then(res => {
                if (res.data && res.data.redirect_url) {
                    window.location.href = res.data.redirect_url;
                } else if (res.redirect_url) {
                    window.location.href = res.redirect_url;
                } else if (res.message) {
                    showFlashMessage('error', res.message);
                    placeOrderBtn.disabled = false;
                    placeOrderBtn.innerHTML = 'Đặt hàng';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showFlashMessage('error', 'Có lỗi xảy ra khi đặt hàng');
                placeOrderBtn.disabled = false;
                placeOrderBtn.innerHTML = 'Đặt hàng';
            });
    });
});
