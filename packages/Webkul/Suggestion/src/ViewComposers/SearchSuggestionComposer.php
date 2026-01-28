<?php

namespace Webkul\Suggestion\ViewComposers;

use Illuminate\View\View;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Category\Repositories\CategoryRepository;

class SearchSuggestionComposer
{
    /**
     * Create a new search suggestion composer.
     *
     * @param  \Webkul\Product\Repositories\ProductRepository  $productRepository
     * @param  \Webkul\Category\Repositories\CategoryRepository  $categoryRepository
     * @return void
     */
    public function __construct(
        protected ProductRepository $productRepository,
        protected CategoryRepository $categoryRepository
    ) {
    }

    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $popularProducts = [];
        $popularCategories = [];

        $productIds = core()->getConfigData('suggestion.suggestion.general.popular_products');
        if ($productIds) {
            $idArray = array_map('trim', explode(',', $productIds));
            
            // Limit to 10 for performance
            $idArray = array_slice($idArray, 0, 10);

            $popularProducts = $this->productRepository->scopeQuery(function ($query) use ($idArray) {
                return $query->with(['images'])
                    ->whereIn('products.id', $idArray)
                    ->whereHas('product_flats', function ($query) {
                        $query->where('status', 1)
                              ->where('visible_individually', 1);
                    });
            })->get();

            $popularProducts->each(function ($product) {
                $product->price_html = $product->getTypeInstance()->getPriceHtml();
            });
        }

        $categoryIds = core()->getConfigData('suggestion.suggestion.general.popular_categories');
        if ($categoryIds) {
            $idArray = array_map('trim', explode(',', $categoryIds));
            
            $popularCategories = $this->categoryRepository->scopeQuery(function ($query) use ($idArray) {
                return $query->whereIn('categories.id', $idArray)
                    ->where('status', 1);
            })->get();
        }

        $view->with(compact('popularProducts', 'popularCategories'));
    }
}
