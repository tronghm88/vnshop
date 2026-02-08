@props([
    'src' => null,
])

@php
    $categories = [];
    $categoryRepository = app('Webkul\Category\Repositories\CategoryRepository');

    if ($src) {
        
        $urlComponents = parse_url($src);

        $queryParams = [];
        if (isset($urlComponents['query'])) {
            parse_str($urlComponents['query'], $queryParams);
        }
        $queryParams['parent_id'] = 1;
        $categories = $categoryRepository->getAll($queryParams);
        
        // work arround to deduplicate categories
        $categoriesNames = [];
        $deduplicatedCategories = [];
        foreach ($categories as $category) {
            if (!in_array($category->name, $categoriesNames)) {
                $categoriesNames[] = $category->name;
                $deduplicatedCategories[] = $category;
            }
        }
        $categories = $deduplicatedCategories;
    }

@endphp

@if (count($categories))
    <section class="section container">
        @if (isset($options['title']) && $options['title'])
            <div class="section-header">
                <h2>{{ $options['title'] }}</h2>
            </div>
        @endif

        <div class="category-flat-list">
            <div class="carousel">
                @foreach ($categories as $category)
                    <a href="/{{ $category->slug }}" class="category-card-flat">
                        @if ($category->logo_url)
                        <div class="category-image">
                            <img src="{{ $category->logo_url }}" alt="{{ $category->name }}">
                        </div>
                        @else
                            <div class="placeholder-image">
                                <span class="category-name">No Image</span>
                            </div>
                        @endif
                        <span class="category-name">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
