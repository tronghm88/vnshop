<?php

namespace Webkul\TaVppTheme\Http\Composers;

use Illuminate\View\View;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Theme\Repositories\ThemeCustomizationRepository;
use Webkul\Theme\Models\ThemeCustomization;

class CmsPageComposer
{
    /**
     * Create a new composer instance.
     *
     * @param  \Webkul\Product\Repositories\ProductRepository  $productRepository
     * @return void
     */
    public function __construct(
        protected ThemeCustomizationRepository $themeCustomizationRepository
    ) {}

    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view): void
    {
        // i want to load customizations here
        $customizations = $this->themeCustomizationRepository->findWhere([
            'type' => ThemeCustomization::PRODUCT_CAROUSEL,
            'status' => 1,
        ]);

        $view->with([
            'customizations' => $customizations,
        ]);
    }
}
