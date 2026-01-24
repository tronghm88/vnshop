<?php

namespace Webkul\TaVppTheme\Http\Composers;

use Illuminate\View\View;

class OrdersComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view): void
    {
        $customer = auth()->guard('customer')->user();
        
        if (!$customer) {
            $view->with('orders', collect());
            return;
        }
        
        // Get all orders without pagination
        // Eager load relationships to avoid N+1 queries
        $orders = $customer->orders()
            // ->with([
            //     'items' => function ($query) {
            //         $query->with('product');
            //     },
            //     'shipping_address',
            //     'billing_address'
            // ])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $view->with('orders', $orders);
    }
}
