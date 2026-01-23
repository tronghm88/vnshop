<?php

namespace Webkul\TaVppTheme\Http\ViewComposers;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Webkul\CartRule\Repositories\CartRuleRepository;

class ProductVoucherComposer
{
    /**
     * Create a new composer instance.
     *
     * @param  \Webkul\CartRule\Repositories\CartRuleRepository  $cartRuleRepository
     * @return void
     */
    public function __construct(
        protected CartRuleRepository $cartRuleRepository
    ) {}

    /**
     * Bind data to the view.
     * This method queries the database and passes vouchers as view variable.
     * Uses caching to avoid repeated queries.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Cache for 5 minutes to avoid repeated queries
        // Cache key includes channel to support multi-channel
        $cacheKey = 'product_page_vouchers_' . core()->getCurrentChannel()->id;
        
        $availableVouchers = Cache::remember(
            $cacheKey, 
            300, // 5 minutes (300 seconds)
            function () {
                return $this->cartRuleRepository
                    ->getModel()
                    ->where('status', 1)
                    ->where(function($query) {
                        $query->whereNull('starts_from')
                              ->orWhere('starts_from', '<=', now());
                    })
                    ->where(function($query) {
                        $query->whereNull('ends_till')
                              ->orWhere('ends_till', '>=', now());
                    })
                    ->where('coupon_type', 1) // Specific coupon
                    ->whereHas('channels', function ($query) {
                        $query->where('channel_id', core()->getCurrentChannel()->id);
                    })
                    ->with(['coupons' => function ($query) {
                        $query->where('is_primary', 1)->orWhere('is_primary', 0);
                    }])
                    ->limit(4)
                    ->get();
            }
        );

        // Pass to view as variable - blade just displays, no query
        $view->with('availableVouchers', $availableVouchers);
    }
}
