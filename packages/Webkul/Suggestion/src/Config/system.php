<?php

return [
    [
        'key'  => 'suggestion',
        'name' => 'suggestion::app.admin.system.search-suggestion',
        'info' => 'suggestion::app.admin.system.set-search-setting',
        'sort' => 1,
    ], [
        'key'  => 'suggestion.suggestion',
        'name' => 'suggestion::app.admin.system.settings',
        'info' => 'suggestion::app.admin.system.set-search-setting',
        'icon' => 'settings/store.svg',
        'sort' => 1,
    ], [
        'key'    => 'suggestion.suggestion.general',
        'name'   => 'suggestion::app.admin.system.general',
        'info'   => 'suggestion::app.admin.system.set-search-setting',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'status',
                'title'         => 'suggestion::app.admin.system.status',
                'type'          => 'boolean',
                'channel_based' => true,
            ], [
                'name'          => 'search_placeholder',
                'title'         => 'suggestion::app.admin.system.search_placeholder',
                'type'          => 'text',
                'channel_based' => true,
            ], [
                'name'          => 'min_search_terms',
                'title'         => 'suggestion::app.admin.system.min-search-terms',
                'type'          => 'text',
                'validation'    => 'required|numeric|between:1,20',
                'channel_based' => true,
            ], [
                'name'          => 'limit_products',
                'title'         => 'suggestion::app.admin.system.limit-products',
                'type'          => 'number',
                'validation'    => 'numeric|min:1',
                'default'       => '6',
                'channel_based' => true,
            ], [
                'name'          => 'popular_products',
                'title'         => 'suggestion::app.admin.system.popular-products',
                'type'          => 'text',
                'info'          => 'suggestion::app.admin.system.popular-products-info',
                'channel_based' => true,
            ], [
                'name'          => 'popular_categories',
                'title'         => 'suggestion::app.admin.system.popular-categories',
                'type'          => 'text',
                'info'          => 'suggestion::app.admin.system.popular-categories-info',
                'channel_based' => true,
            ], 
        ],
    ],
];
