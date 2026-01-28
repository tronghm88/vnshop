<?php

namespace Webkul\Suggestion\Http\Controllers\Shop;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Webkul\Product\Repositories\ProductRepository;

class SuggestionController extends Controller
{
    use DispatchesJobs, ValidatesRequests;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected ProductRepository $productRepository) {}

    /**
     * Handle the popular products
     *
     * @return array
     */
    public function popular()
    {
        $productIds = core()->getConfigData('suggestion.suggestion.general.popular_products');
        
        if (! $productIds) {
            return response()->json([]);
        }

        $idArray = array_map('trim', explode(',', $productIds));
        $idArray = array_slice($idArray, 0, 10);

        $results = $this->productRepository->scopeQuery(function ($query) use ($idArray) {
            return $query->with(['images'])
                ->whereIn('products.id', $idArray)
                ->whereHas('product_flats', function ($query) {
                    $query->where('status', 1)
                          ->where('visible_individually', 1);
                });
        })->get();

        $results->each(function ($result) {
            $result->price_html = $result->getTypeInstance()->getPriceHtml();
        });

        return response()->json($results);
    }

    /**
     * Handle the search results
     *
     * @return array
     */
    public function search()
    {
        $params = request()->input();

        $term = $params['term'] ?? '';

        $categoryId = $params['category'] ?? '';

        $productLimit = core()->getConfigData('suggestion.suggestion.general.limit_products');

        if (! $productLimit) {
            $productLimit = 6;
        }

        $results = $this->productRepository
            ->with(['images', 'categories'])
            ->whereHas('product_flats', function ($query) use ($term, $categoryId) {
                $query->distinct()
                    ->when($categoryId, function ($innerQuery) use ($categoryId) {
                        $innerQuery->leftJoin('product_categories', 'product_categories.product_id', '=', 'products.id')
                            ->where('product_categories.category_id', $categoryId);
                    })
                    ->where([
                        ['product_flat.status', 1],
                        ['product_flat.visible_individually', 1],
                    ])
                    ->whereNotNull('product_flat.url_key')
                    ->where(function ($innerQuery) use ($term) {
                        $innerQuery
                            ->where('product_flat.name', 'like', '%'.$term.'%')
                            ->orWhere('product_flat.sku', 'like', '%'.$term.'%');
                    })
                    ->orderByDesc('product_flat.product_id');
            })
            ->paginate($productLimit);

        $results->each(function ($result) {
            $result->price_html = $result->getTypeInstance()->getPriceHtml();
        });

        return response()->json($results);
    }
}
