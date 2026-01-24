{{-- 
    Shipping Estimator Component
    
    Usage:
    <x-ta-vpp-theme::shipping-estimator />
--}}

<div class="shipping-estimation-section" style="margin-top: 15px; padding: 10px; background: #f9f9f9; border-radius: 8px;">
    <label style="display: block; margin-bottom: 5px; font-weight: 600;">{{ trans('ta-vpp-theme::app.checkout.cart.summary.estimate-shipping.title') }}:</label>
    <div style="display: flex; flex-direction: column; gap: 10px;">
        <select id="shipping_province" class="control" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            <option value="">{{ trans('ta-vpp-theme::app.checkout.onepage.address.select-state') }}</option>
        </select>
        <select id="shipping_ward" class="control" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            <option value="">{{ trans('ta-vpp-theme::app.checkout.onepage.address.select-ward') }}</option>
        </select>
    </div>
    <div id="shipping_estimate_result" style="margin-top: 8px; font-size: 14px; color: #333;"></div>
</div>

@pushOnce('scripts')
<script>
    // ============================================
    // Shipping Estimation Functions
    // ============================================
    function initShippingEstimation() {
        const provinceSelect = document.getElementById('shipping_province');
        const wardSelect = document.getElementById('shipping_ward');
        
        if (!provinceSelect || !wardSelect) return;

        // Load provinces from API
        fetch('https://provinces.open-api.vn/api/v2/p')
            .then(res => res.json())
            .then(provinces => {
                provinces.forEach(province => {
                    const option = document.createElement('option');
                    option.value = province.name;
                    option.dataset.code = province.code;
                    option.textContent = province.name;
                    provinceSelect.appendChild(option);
                });
            })
            .catch(err => console.error('Error loading provinces:', err));

        // Handle province change - load wards
        provinceSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const provinceCode = selectedOption?.dataset.code;
            
            // Reset ward select
            wardSelect.innerHTML = '<option value="">{{ trans('ta-vpp-theme::app.checkout.onepage.address.select-ward') }}</option>';
            document.getElementById('shipping_estimate_result').innerHTML = '';
            
            if (!provinceCode) return;
            
            // Load wards for selected province
            fetch(`https://provinces.open-api.vn/api/v2/w?province=${provinceCode}`)
                .then(res => res.json())
                .then(wards => {
                    wards.forEach(ward => {
                        const option = document.createElement('option');
                        option.value = ward.name;
                        option.textContent = ward.name;
                        wardSelect.appendChild(option);
                    });
                })
                .catch(err => console.error('Error loading wards:', err));
        });

        // Handle ward change - estimate shipping
        wardSelect.addEventListener('change', function() {
            if (this.value && provinceSelect.value) {
                estimateShipping();
            } else {
                document.getElementById('shipping_estimate_result').innerHTML = '';
            }
        });
    }

    async function estimateShipping() {
        const provinceSelect = document.getElementById('shipping_province');
        const ward = document.getElementById('shipping_ward').value;
        const resultDiv = document.getElementById('shipping_estimate_result');
        
        const selectedOption = provinceSelect.options[provinceSelect.selectedIndex];
        const provinceCode = selectedOption?.dataset.code;
        
        if (!provinceCode || !ward) {
            resultDiv.innerHTML = '';
            return;
        }

        resultDiv.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ trans('ta-vpp-theme::app.checkout.onepage.shipping.calculating-shipping') }}';

        try {
            const response = await fetch('{{ route("tavpp.shipping.estimate") }}?province_code=' + provinceCode);
            const data = await response.json();
            
            if (data.rate !== undefined) {
                const formattedRate = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(data.rate);
                let html = `
                    <div style="padding: 10px; background: #e8f5e9; border-radius: 6px; border-left: 4px solid #4caf50; margin-top: 10px;">
                        <div style="font-weight: 600; color: #2e7d32; margin-bottom: 5px;">
                            <i class="fa-solid fa-truck"></i> ${data.region_name}
                        </div>
                        <div style="font-size: 15px; color: #333;">
                            {{ trans('ta-vpp-theme::app.checkout.onepage.summary.delivery-charges') }}: <strong>${formattedRate}</strong>
                        </div>
                        <div style="font-size: 12px; color: #666; margin-top: 5px;">
                            <i class="fa-solid fa-circle-info"></i> {{ trans('ta-vpp-theme::app.checkout.cart.index.summary-note') }}
                        </div>
                    </div>
                `;
                resultDiv.innerHTML = html;
            } else if (data.error) {
                resultDiv.innerHTML = '<div style="color: #d32f2f; padding: 10px;">' + data.error + '</div>';
            } else {
                resultDiv.innerHTML = '<div style="color: #d32f2f; padding: 10px;">{{ trans('ta-vpp-theme::app.checkout.onepage.shipping.cannot-calculate-shipping') }}</div>';
            }
        } catch (error) {
            console.error('Error estimating shipping:', error);
            resultDiv.innerHTML = '<div style="color: #d32f2f; padding: 10px;">{{ trans('ta-vpp-theme::app.checkout.onepage.shipping.error-calculating-shipping') }}</div>';
        }
    }

    // Auto-initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initShippingEstimation);
    } else {
        initShippingEstimation();
    }
</script>
@endPushOnce
