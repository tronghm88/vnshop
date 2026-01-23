<?php

namespace Webkul\TaVppTheme\Carriers;

use Webkul\Checkout\Facades\Cart;
use Webkul\Checkout\Models\CartShippingRate;
use Webkul\Shipping\Carriers\AbstractShipping;

class VnRegionalShipping extends AbstractShipping
{
    /**
     * Shipping method carrier code.
     *
     * @var string
     */
    protected $code = 'vn_regional_shipping';

    /**
     * Shipping method code.
     *
     * @var string
     */
    protected $method = 'vn_regional_shipping_vn_regional_shipping';

    /**
     * Define North/Middle/South province lists.
     */
    protected $regions = [
        'north'  => [
            'Hà Nội', 'Hà Giang', 'Cao Bằng', 'Bắc Kạn', 'Tuyên Quang', 'Lào Cai', 'Điện Biên', 'Lai Châu', 'Sơn La', 'Yên Bái',
            'Hoà Bình', 'Thái Nguyên', 'Lạng Sơn', 'Quảng Ninh', 'Bắc Giang', 'Phú Thọ', 'Vĩnh Phúc', 'Bắc Ninh', 'Hải Dương',
            'Hải Phòng', 'Hưng Yên', 'Thái Bình', 'Hà Nam', 'Nam Định', 'Ninh Bình'
        ],
        'middle' => [
            'Thanh Hóa', 'Nghệ An', 'Hà Tĩnh', 'Quảng Bình', 'Quảng Trị', 'Thừa Thiên Huế', 'Đà Nẵng', 'Quảng Nam', 'Quảng Ngãi',
            'Bình Định', 'Phú Yên', 'Khánh Hòa', 'Ninh Thuận', 'Bình Thuận', 'Kon Tum', 'Gia Lai', 'Đắk Lắk', 'Đắk Nông', 'Lâm Đồng'
        ],
        'south'  => [
            'TP Hồ Chí Minh', 'Bình Phước', 'Tây Ninh', 'Bình Dương', 'Đồng Nai', 'Bà Rịa - Vũng Tàu', 'Long An', 'Tiền Giang',
            'Bến Tre', 'Trà Vinh', 'Vĩnh Long', 'Đồng Tháp', 'An Giang', 'Kiên Giang', 'Cần Thơ', 'Hậu Giang', 'Sóc Trăng',
            'Bạc Liêu', 'Cà Mau'
        ],
    ];

    /**
     * Calculate rate for VnRegionalShipping.
     *
     * @return \Webkul\Checkout\Models\CartShippingRate|false
     */
    public function calculate()
    {
        if (! $this->isAvailable()) {
            return false;
        }

        $cart = Cart::getCart();
        $shippingAddress = $cart->shipping_address;

        if (! $shippingAddress) {
            return false;
        }

        $province = $shippingAddress->state;
        $region = 'south'; // Default

        foreach ($this->regions as $key => $provinces) {
            if (in_array($province, $provinces)) {
                $region = $key;
                break;
            }
        }

        $baseRate = (float) $this->getConfigData('rate_' . $region);
        $dimDivisor = (float) ($this->getConfigData('dim_divisor') ?: 6000);
        $dimensionRates = $this->getConfigData('dimension_rates');
        
        $totalActualWeight = 0;
        $totalDimensionSurcharge = 0;

        foreach ($cart->items as $item) {
            if ($item->product->getTypeInstance()->isStockable()) {
                $totalActualWeight += (float) $item->weight * $item->quantity;

                // Calculate Volumetric Weight for THIS product item
                $l = (float) ($item->product->length ?: 0);
                $w = (float) ($item->product->width ?: 0);
                $h = (float) ($item->product->height ?: 0);

                if ($l > 0 && $w > 0 && $h > 0) {
                    $volumetricWeight = ($l * $w * $h) / $dimDivisor;
                    
                    // Calculate surcharge for one unit of this product
                    $unitSurcharge = $this->calculateSurcharge($dimensionRates, $volumetricWeight);
                    
                    // Add to total: surcharge * quantity
                    $totalDimensionSurcharge += $unitSurcharge * $item->quantity;
                }
            }
        }

        // Weight surcharge is still calculated on total cart weight
        $weightSurcharge = $this->calculateSurcharge($this->getConfigData('weight_rates'), $totalActualWeight);

        $totalPrice = $baseRate + $totalDimensionSurcharge + $weightSurcharge;

        $cartShippingRate = new CartShippingRate;
        $cartShippingRate->carrier = $this->getCode();
        $cartShippingRate->carrier_title = $this->getConfigData('title');
        $cartShippingRate->method = $this->getMethod();
        $cartShippingRate->method_title = $this->getConfigData('title');
        
        $breakdownParts = [
            sprintf("Phí cơ bản: %s", core()->formatBasePrice($baseRate))
        ];

        if ($totalDimensionSurcharge > 0) {
            $breakdownParts[] = sprintf("Phụ phí kích thước (tổng): %s", core()->formatBasePrice($totalDimensionSurcharge));
        }

        if ($weightSurcharge > 0) {
            $breakdownParts[] = sprintf("Phụ phí cân nặng (%s kg): %s", number_format($totalActualWeight, 2), core()->formatBasePrice($weightSurcharge));
        }
        
        $cartShippingRate->method_description = implode(". ", $breakdownParts) . ".";
        $cartShippingRate->price = core()->convertPrice($totalPrice);
        $cartShippingRate->base_price = $totalPrice;

        return $cartShippingRate;
    }

    /**
     * Calculate surcharge based on threshold string (e.g., "0.1:1000;0.5:2000")
     *
     * @param string $rateString
     * @param float $value
     * @return float
     */
    protected function calculateSurcharge($rateString, $value)
    {
        if (! $rateString) {
            return 0;
        }

        $rules = explode(';', $rateString);
        $applicableSurcharge = 0;
        $maxThreshold = -1;

        foreach ($rules as $rule) {
            $parts = explode(':', $rule);
            if (count($parts) !== 2) {
                continue;
            }

            $threshold = (float) trim($parts[0]);
            $cost = (float) trim($parts[1]);

            if ($value >= $threshold && $threshold > $maxThreshold) {
                $maxThreshold = $threshold;
                $applicableSurcharge = $cost;
            }
        }

        return $applicableSurcharge;
    }
}
