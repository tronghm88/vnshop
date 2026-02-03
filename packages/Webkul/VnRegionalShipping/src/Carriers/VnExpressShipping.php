<?php

namespace Webkul\VnRegionalShipping\Carriers;

use Webkul\Shipping\Carriers\AbstractShipping;
use Webkul\Checkout\Facades\Cart;
use Webkul\Checkout\Models\CartShippingRate;

class VnExpressShipping extends AbstractShipping
{
    /**
     * Shipping method carrier code.
     *
     * @var string
     */
    protected $code = 'vn_express_shipping';

    /**
     * Shipping method code.
     *
     * @var string
     */
    protected $method = 'vn_express_shipping_vn_express_shipping';
    
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
     * Calculate rate for VnExpressShipping.
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

        // check if shipping address is accepted by vn express
        $superExpresssStateCode = (int) $this->getConfigData('super_expresss_state');
        $provinceCode = $this->helper->getProvinceCodeByFullName($shippingAddress->state);

        if ($superExpresssStateCode !== $provinceCode) {
            return false;
        }

        $totalWeight = $cart->items->sum(function ($item) {
            return $item->quantity * $item->product->weight;
        });

        // check total weight is greater than limit weight
        $limitWeight = (float) $this->getConfigData('super_expresss_limit_weight');
        if ($totalWeight > $limitWeight) {
            return false;
        }
    
        // check total dimension is greater than limit dimension
        $limitDimension = (float) $this->getConfigData('super_expresss_limit_dimension');
        $limitDimensionDivisor = (float) $this->getConfigData('super_expresss_dim_divisor') ?: 6000;

        $isOverLimitDimension = false;
        foreach ($cart->items as $item) {
            if ($item->product->length && $item->product->width && $item->product->height) {
                $dimension = $item->quantity * $item->product->length * $item->product->width * $item->product->height;
                $dimension = $dimension / $limitDimensionDivisor;
                if ($dimension > $limitDimension) {
                    $isOverLimitDimension = true;
                    break;
                }
            }
        }

        if ($isOverLimitDimension) {
            return false;
        }

        $basePrice = (float) $this->getConfigData('super_expresss_rate');

        [$discountAmount, $appliedRules] = $this->helper->applyFreeShippingRules($cart, $basePrice);
        $totalPrice = $basePrice - $discountAmount;

        $cartShippingRate = new CartShippingRate;
        $cartShippingRate->carrier = $this->getCode();
        $cartShippingRate->carrier_title = trans(
            'vn-regional-shipping::app.view.express-shipping.method_title'
        );
        $cartShippingRate->method = $this->getMethod();
        $cartShippingRate->method_title = trans(
            'vn-regional-shipping::app.view.express-shipping.method_title'
        );

        $cartShippingRate->method_description = $this->helper->buildBreakdownText(
            $basePrice,
            null,
            null,
            $totalWeight,
            $appliedRules
        );

        $cartShippingRate->price = core()->convertPrice($totalPrice);
        $cartShippingRate->base_price = $basePrice;
        $cartShippingRate->discount_amount = $discountAmount;
        $cartShippingRate->base_discount_amount = $discountAmount;

        return $cartShippingRate;
    }
}