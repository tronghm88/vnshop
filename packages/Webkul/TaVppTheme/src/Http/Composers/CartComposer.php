<?php

namespace Webkul\TaVppTheme\Http\Composers;

use Illuminate\View\View;
use Webkul\Checkout\Facades\Cart;

class CartComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $cart = Cart::getCart();

        if ($cart) {
            // Gather product IDs
            $productIds = $cart->items->pluck('product_id')->unique();

            // Fetch products with only necessary fields and relationships
            // We select '*' to ensure we don't miss fields needed by TypeInstance or Image helper, 
            // but we could limit this to ['id', 'type', 'sku', 'attribute_family_id', 'additional'] if strict optimization is needed.
            // Using '*' is safer for now to avoid breaking helpers.
            $products = \Webkul\Product\Models\Product::whereIn('id', $productIds)
                ->with([
                    'attribute_values',
                    'images',
                    'inventory_indices',
                    'variants',
                ])
                ->get()
                ->keyBy('id');

            foreach ($cart->items as $item) {
                if ($product = $products->get($item->product_id)) {
                    // Manually set the relation to use our eager-loaded instance
                    $item->setRelation('product', $product);

                    // Calculate display values
                    $product->cart_base_image = product_image()->getProductBaseImage($product);
                    $product->cart_regular_price = $product->getTypeInstance()->getMaximumPrice();
                }
            }
        }
    }
}
