<?php

namespace Webkul\VnRegionalShipping\Carriers;

use Webkul\CartRule\Repositories\CartRuleRepository;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\CartRule\Repositories\CartRuleCustomerRepository;
use Webkul\Rule\Helpers\Validator;
use Carbon\Carbon;

class Helper
{
    /**
     * Define North/Middle/South province lists by province codes.
     */
    protected $regions = [
        'north' => [1, 4, 8, 11, 12, 14, 15, 19, 20, 22, 24, 25, 31, 33, 37],
        'middle' => [38, 40, 42, 44, 46, 48, 51, 52, 56, 66, 68],
        'south' => [75, 79, 80, 82, 86, 91, 92, 96]
    ];

    protected $provinces = [];
    /**
     * Create a new helper instance.
     *
     * @return void
     */
    public function __construct(
        protected CartRuleRepository $cartRuleRepository,
        protected CustomerRepository $customerRepository,
        protected CartRuleCustomerRepository $cartRuleCustomerRepository,
        protected Validator $validator
    ) {
        $this->loadProvinces();
    }

    public function loadProvinces()
    {
        $provinces = include dirname(__DIR__) . '/Config/provinces.php';
        $this->provinces = collect($provinces);
    }
    public function getRegionByName($provinceName)
    {
        
        // get province code from province name
        $province = $this->provinces->first(function ($province) use ($provinceName) {
            return str_contains($province['name'], $provinceName);
        });

        if (! $province) {
            return null;
        }
       
        $provinceCode = $province['code'];
        if (! $provinceCode) {
            return null;
        }
        // get region by province code
        foreach ($this->regions as $key => $provinces) {
            if (in_array($provinceCode, $provinces)) {
                return $key;
            }
        }
        return null;
    }

    public function getProvinceCodeByFullName($fullProvinceName)
    {
        $provinceName = str_replace('Tỉnh ', '', $fullProvinceName);
        $provinceName = str_replace('Thành phố ', '', $provinceName);
        $province = $this->provinces->first(function ($province) use ($provinceName) {
            return str_contains($province['name'], $provinceName);
        });

        if (! $province) {
            return null;
        }
        return $province['code'];
    }

    public function getRegionByFullProvinceName($fullProvinceName)
    {
        $provinceCode = $this->getProvinceCodeByFullName($fullProvinceName);
        if (! $provinceCode) {
            return null;
        }
        // get region by province code
        foreach ($this->regions as $key => $provinces) {
            if (in_array($provinceCode, $provinces)) {
                return $key;
            }
        }
        return null;
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

     /**
     * Apply free shipping rules.
     *
     * @param \Webkul\Checkout\Contracts\Cart $cart
     * @param float $shippingPrice
     * @return array
     */
    public function applyFreeShippingRules($cart, $basePrice)
    {
        $discount = 0;
        $appliedRules = [];

        foreach ($this->getCartRules() as $rule) {
            // Skip if coupon is required but not present/matching
            if ($rule->coupon_type && !$cart->coupon_code) {
                continue;
            }

            if (! $this->validator->validate($rule, $cart)) {
                continue;
            }

            // Check Uses Per Customer Limit
            if ($rule->usage_per_customer) {
                // Only check if customer is logged in
                if ($cart->customer_id) {
                    $ruleCustomer = $this->cartRuleCustomerRepository->findOneWhere([
                        'cart_rule_id' => $rule->id,
                        'customer_id'  => $cart->customer_id,
                    ]);

                    if ($ruleCustomer && $ruleCustomer->times_used >= $rule->usage_per_customer) {
                        continue; // Skip this rule if limit reached
                    }
                }
            }


            if ($rule->apply_to_shipping) {
                $disount = 0;
                if ($rule->free_shipping) {
                    $discount = $basePrice;
                } else {
                    if ($rule->action_type === 'by_percent') {
                        $discount = $basePrice * ($rule->discount_amount / 100);
                    } else if ($rule->action_type === 'by_fixed') {
                        $discount = $rule->discount_amount;
                    } else {
                        // TODO: Handle other action types
                        $discount = 0;
                    }
                }


                if (!in_array($rule->name, $appliedRules)) {
                    $appliedRules[] = $rule->name;
                }
                
                if ($rule->end_other_rules) {
                    return [$discount, $appliedRules];
                }
            }
        }

        return [$discount, $appliedRules];
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
    public function buildBreakdownText(
        $basePrice,
        $dimensionSurcharge,
        $weightSurcharge,
        $totalWeight,
        $appliedRules
    ){
        $parts = [
            sprintf("<span class='shipping-breakdown-item base-rate'>Phí cơ bản: %s</span>", core()->formatBasePrice($basePrice))
        ];

        if ($dimensionSurcharge > 0) {
            $parts[] = sprintf("<span class='shipping-breakdown-item dimension-surcharge'>Phụ phí kích thước: %s</span>", core()->formatBasePrice($dimensionSurcharge));
        }

        if ($weightSurcharge > 0) {
            $parts[] = sprintf("<span class='shipping-breakdown-item weight-surcharge'>Phụ phí cân nặng (%s kg): %s</span>", number_format($totalWeight, 2), core()->formatBasePrice($weightSurcharge));
        }

        if ($appliedRules) {
            $parts[] = sprintf("<span class='shipping-breakdown-item applied-rules'>Áp dụng quyền lợi: %s</span>", implode(", ", $appliedRules));
        }

        return implode(" <span class='shipping-breakdown-separator'>•</span> ", $parts);
    }
}