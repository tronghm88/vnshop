<?php

namespace Webkul\VnRegionalShipping\Carriers;

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
     * Helper instance.
     *
     * @var \Webkul\VnRegionalShipping\Carriers\Helper
     */
    protected $helper;

    /**
     * Create a new VnExpressShipping instance.
     *
     * @return void
     */
    public function __construct() {
        $this->helper = app(Helper::class);
    }

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

        $region = $this->helper->getRegionByFullProvinceName($province);
        if (! $region) {
            return false;
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

        $basePrice = $baseRate + $totalDimensionSurcharge + $weightSurcharge;

        // Check for free shipping
        [$discountAmount, $appliedRules] = $this->helper->applyFreeShippingRules($cart, $basePrice);
        $totalPrice = $basePrice - $discountAmount;

        $cartShippingRate = new CartShippingRate;
        $cartShippingRate->carrier = $this->getCode();
        $cartShippingRate->carrier_title = trans(
            'vn-regional-shipping::app.view.regional-shipping.method_title'
        );
        $cartShippingRate->method = $this->getMethod();
        $cartShippingRate->method_title = trans(
            'vn-regional-shipping::app.view.regional-shipping.method_title'
        );

        $breakdownText = $this->helper->buildBreakdownText(
            $basePrice,
            $totalDimensionSurcharge,
            $weightSurcharge,
            $totalActualWeight,
            $appliedRules
        );
        
        $cartShippingRate->method_description = $breakdownText;
        $cartShippingRate->price = core()->convertPrice($totalPrice);
        $cartShippingRate->base_price = $basePrice;
        $cartShippingRate->discount_amount = $discountAmount;
        $cartShippingRate->base_discount_amount = $discountAmount;

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
