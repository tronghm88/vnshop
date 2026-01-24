<?php

namespace Webkul\TaVppTheme\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Webkul\Checkout\Facades\Cart;
use Webkul\CartRule\Repositories\CartRuleRepository;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Rule\Helpers\Validator;
use Carbon\Carbon;

class ShippingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected CartRuleRepository $cartRuleRepository,
        protected CustomerRepository $customerRepository,
        protected Validator $validator
    ) {}

    /**
     * Define North/Middle/South province lists by province codes.
     */
    protected $regions = [
        'north' => [1, 4, 8, 11, 12, 14, 15, 19, 20, 22, 24, 25, 31, 33, 37],
        'middle' => [38, 40, 42, 44, 46, 48, 51, 52, 56, 66, 68],
        'south' => [75, 79, 80, 82, 86, 91, 92, 96]
    ];

    /**
     * Get base shipping rate for a given province code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBaseRate(Request $request)
    {
        $provinceCode = $request->input('province_code');

        if (! $provinceCode) {
            return response()->json(['error' => 'Province code is required'], 400);
        }

        $region = $this->getRegionByCode((int) $provinceCode);
        $rate = core()->getConfigData('sales.carriers.vn_regional_shipping.rate_' . $region);

        return response()->json([
            'region' => $region,
            'region_name' => $this->getRegionName($region),
            'rate' => $rate ?? 0,
        ]);
    }

    /**
     * Calculate detailed shipping fee with breakdown.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateShippingFee(Request $request)
    {
        $provinceCode = $request->input('province_code');
        $ward = $request->input('ward');

        if (! $provinceCode) {
            return response()->json(['error' => 'Province code is required'], 400);
        }

        if (! $ward) {
            return response()->json(['error' => 'Ward is required'], 400);
        }

        // Get the cart
        $cart = Cart::getCart();

        if (! $cart) {
            return response()->json(['error' => 'Cart not found'], 404);
        }

        // Determine region by province code
        $region = $this->getRegionByCode((int) $provinceCode);

        // Get configuration
        $baseRate = (float) core()->getConfigData('sales.carriers.vn_regional_shipping.rate_' . $region);
        $dimDivisor = (float) (core()->getConfigData('sales.carriers.vn_regional_shipping.dim_divisor') ?: 6000);
        $dimensionRates = core()->getConfigData('sales.carriers.vn_regional_shipping.dimension_rates');
        $weightRates = core()->getConfigData('sales.carriers.vn_regional_shipping.weight_rates');

        $totalActualWeight = 0;
        $totalDimensionSurcharge = 0;
        $itemBreakdown = [];

        // Calculate per-item breakdown
        foreach ($cart->items as $item) {
            if ($item->product->getTypeInstance()->isStockable()) {
                $itemWeight = (float) $item->weight * $item->quantity;
                $totalActualWeight += $itemWeight;

                // Calculate Volumetric Weight for this product item
                $l = (float) ($item->product->length ?: 0);
                $w = (float) ($item->product->width ?: 0);
                $h = (float) ($item->product->height ?: 0);

                $itemDetail = [
                    'name' => $item->name,
                    'quantity' => $item->quantity,
                    'weight' => round($itemWeight, 2),
                    'dimensions' => null,
                    'volumetric_weight' => 0,
                    'dimension_surcharge' => 0,
                ];

                if ($l > 0 && $w > 0 && $h > 0) {
                    $volumetricWeight = ($l * $w * $h) / $dimDivisor;
                    
                    // Calculate surcharge for one unit of this product
                    $unitSurcharge = $this->calculateSurcharge($dimensionRates, $volumetricWeight);
                    
                    // Total surcharge for this item
                    $itemSurcharge = $unitSurcharge * $item->quantity;
                    $totalDimensionSurcharge += $itemSurcharge;

                    $itemDetail['dimensions'] = sprintf('%s x %s x %s cm', $l, $w, $h);
                    $itemDetail['volumetric_weight'] = round($volumetricWeight, 2);
                    $itemDetail['dimension_surcharge'] = $itemSurcharge;
                }

                $itemBreakdown[] = $itemDetail;
            }
        }

        // Weight surcharge calculated on total cart weight
        $weightSurcharge = $this->calculateSurcharge($weightRates, $totalActualWeight);

        // Calculate total
        $totalPrice = $baseRate + $totalDimensionSurcharge + $weightSurcharge;

        // Check for free shipping
        [$discountAmount, $appliedRules] = $this->applyFreeShippingRules($cart, $totalPrice);
        $finalPrice = max(0, $totalPrice - $discountAmount);

        // Build response with detailed breakdown
        return response()->json([
            'success' => true,
            'region' => $region,
            'region_name' => $this->getRegionName($region),
            'base_rate' => $baseRate,
            'base_rate_formatted' => core()->formatBasePrice($baseRate),
            'total_weight' => round($totalActualWeight, 2),
            'weight_surcharge' => $weightSurcharge,
            'weight_surcharge_formatted' => core()->formatBasePrice($weightSurcharge),
            'dimension_surcharge' => $totalDimensionSurcharge,
            'dimension_surcharge_formatted' => core()->formatBasePrice($totalDimensionSurcharge),
            'total_price' => $finalPrice,
            'total_price_formatted' => core()->formatBasePrice($finalPrice),
            'original_price' => $totalPrice,
            'original_price_formatted' => core()->formatBasePrice($totalPrice),
            'discount_amount' => $discountAmount,
            'discount_amount_formatted' => core()->formatBasePrice($discountAmount),
            'applied_rules' => $appliedRules,
            'items' => $itemBreakdown,
            'breakdown_text' => $this->buildBreakdownText($baseRate, $totalDimensionSurcharge, $weightSurcharge, $totalActualWeight),
        ]);
    }

    /**
     * Get region by province code.
     *
     * @param int $provinceCode
     * @return string
     */
    protected function getRegionByCode($provinceCode)
    {
        foreach ($this->regions as $regionKey => $codes) {
            if (in_array($provinceCode, $codes)) {
                return $regionKey;
            }
        }

        // Default to south if not found
        return 'south';
    }

    /**
     * Calculate surcharge based on threshold string.
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

    /**
     * Get region name in Vietnamese.
     *
     * @param string $region
     * @return string
     */
    protected function getRegionName($region)
    {
        $names = [
            'north' => 'Miền Bắc',
            'middle' => 'Miền Trung',
            'south' => 'Miền Nam',
        ];

        return $names[$region] ?? $region;
    }

    /**
     * Build human-readable breakdown text.
     *
     * @param float $baseRate
     * @param float $dimensionSurcharge
     * @param float $weightSurcharge
     * @param float $totalWeight
     * @return string
     */
    protected function buildBreakdownText($baseRate, $dimensionSurcharge, $weightSurcharge, $totalWeight)
    {
        $parts = [
            sprintf("Phí cơ bản: %s", core()->formatBasePrice($baseRate))
        ];

        if ($dimensionSurcharge > 0) {
            $parts[] = sprintf("Phụ phí kích thước: %s", core()->formatBasePrice($dimensionSurcharge));
        }

        if ($weightSurcharge > 0) {
            $parts[] = sprintf("Phụ phí cân nặng (%s kg): %s", number_format($totalWeight, 2), core()->formatBasePrice($weightSurcharge));
        }

        return implode(" • ", $parts);
    }

    /**
     * Apply free shipping rules.
     *
     * @param \Webkul\Checkout\Contracts\Cart $cart
     * @param float $shippingPrice
     * @return array
     */
    protected function applyFreeShippingRules($cart, $shippingPrice)
    {
        $discount = 0;
        $appliedRules = [];

        foreach ($cart->items as $item) {
            foreach ($this->getCartRules() as $rule) {
                // Skip if coupon is required but not present/matching
                // (Simplified check - for full logic see CartRule helper)
                if ($rule->coupon_type && !$cart->coupon_code) {
                    continue;
                }

                if (!$this->validator->validate($rule, $item)) {
                    continue;
                }

                if ($rule->free_shipping) {
                    $discount = $shippingPrice;
                    if (!in_array($rule->name, $appliedRules)) {
                        $appliedRules[] = $rule->name;
                    }
                    
                    if ($rule->end_other_rules) {
                        return [$discount, $appliedRules];
                    }
                }
            }
        }

        return [$discount, $appliedRules];
    }

    /**
     * Get valid cart rules.
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function getCartRules()
    {
        $customerGroup = $this->customerRepository->getCurrentGroup();

        return $this->cartRuleRepository
            ->leftJoin('cart_rule_customer_groups', 'cart_rules.id', '=', 'cart_rule_customer_groups.cart_rule_id')
            ->leftJoin('cart_rule_channels', 'cart_rules.id', '=', 'cart_rule_channels.cart_rule_id')
            ->where('cart_rule_customer_groups.customer_group_id', $customerGroup->id)
            ->where('cart_rule_channels.channel_id', core()->getCurrentChannel()->id)
            ->where(function ($query) {
                $query->where('cart_rules.starts_from', '<=', Carbon::now()->format('Y-m-d H:m:s'))
                    ->orWhereNull('cart_rules.starts_from');
            })
            ->where(function ($query) {
                $query->where('cart_rules.ends_till', '>=', Carbon::now()->format('Y-m-d H:m:s'))
                    ->orWhereNull('cart_rules.ends_till');
            })
            ->where('status', 1)
            ->orderBy('sort_order', 'asc')
            ->select('cart_rules.*')
            ->get();
    }
}
