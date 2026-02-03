<?php
$provinces = include __DIR__ . '/provinces.php';
$provinces = collect($provinces)->map(function ($province) {
    return [
        'title' => $province['name'],
        'value' => $province['code'],
    ];
})->toArray();

return [
    // Vn Regional Shipping
    [
        'key'    => 'sales.carriers.vn_regional_shipping',
        'name'   => 'vn-regional-shipping::app.admin.regional-shipping.title',
        'info'   => 'vn-regional-shipping::app.admin.regional-shipping.description',
        'sort'   => 5,
        'fields' => [
            [
                'name'          => 'active',
                'title'         => 'vn-regional-shipping::app.admin.regional-shipping.fields.active',
                'type'          => 'boolean',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => false,
            ],
            [
                'name'          => 'rate_north',
                'title'         => 'vn-regional-shipping::app.admin.regional-shipping.fields.rate_north',
                'type'          => 'text',
                'validation'    => 'required|numeric',
                'channel_based' => false,
                'locale_based'  => false,
            ],
            [
                'name'          => 'rate_middle',
                'title'         => 'vn-regional-shipping::app.admin.regional-shipping.fields.rate_middle',
                'type'          => 'text',
                'validation'    => 'required|numeric',
                'channel_based' => false,
                'locale_based'  => false,
            ],
            [
                'name'          => 'rate_south',
                'title'         => 'vn-regional-shipping::app.admin.regional-shipping.fields.rate_south',
                'type'          => 'text',
                'validation'    => 'required|numeric',
                'channel_based' => false,
                'locale_based'  => false,
            ],
            [
                'name'          => 'dim_divisor',
                'title'         => 'vn-regional-shipping::app.admin.regional-shipping.fields.dim_divisor',  
                'info'          => 'vn-regional-shipping::app.admin.regional-shipping.fields.dim_divisor_info',
                'type'          => 'number',
                'validation'    => 'numeric',
                'default'       => 6000,
                'channel_based' => false,
                'locale_based'  => false,
            ],
            [
                'name'          => 'dimension_rates',   
                'title'         => 'vn-regional-shipping::app.admin.regional-shipping.fields.dimension_rates',
                'info'          => 'vn-regional-shipping::app.admin.regional-shipping.fields.dimension_rates_info',
                'type'          => 'text',
                'channel_based' => false,
                'locale_based'  => false,
            ],
            [
                'name'          => 'weight_rates',
                'title'         => 'vn-regional-shipping::app.admin.regional-shipping.fields.weight_rates',
                'info'          => 'vn-regional-shipping::app.admin.regional-shipping.fields.weight_rates_info',
                'type'          => 'text',
                'channel_based' => false,
                'locale_based'  => false,
            ],
            
            
        ],
    ],

    // Vn Express Shipping
    [
        'key'    => 'sales.carriers.vn_express_shipping',
        'name'   => 'vn-regional-shipping::app.admin.express-shipping.title',
        'info'   => 'vn-regional-shipping::app.admin.express-shipping.description',
        'sort'   => 5,
        'fields' => [
            [
                'name'          => 'active',
                'title'         => 'vn-regional-shipping::app.admin.express-shipping.fields.active',
                'type'          => 'boolean',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => false,
            ],
            [
                'name'          => 'super_expresss_state',
                'title'         => 'vn-regional-shipping::app.admin.express-shipping.fields.super_expresss_state',
                'info'          => 'vn-regional-shipping::app.admin.express-shipping.fields.super_expresss_state_info',
                'type'          => 'select',
                'options'       => $provinces,
                'channel_based' => false,
                'locale_based'  => false,
            ],
            [
                'name'          => 'super_expresss_rate',
                'title'         => 'vn-regional-shipping::app.admin.express-shipping.fields.super_expresss_rate',
                'info'          => 'vn-regional-shipping::app.admin.express-shipping.fields.super_expresss_rate_info',
                'type'          => 'number',
                'validation'    => 'required|numeric',
                'default'       => 30000,
                'channel_based' => false,
                'locale_based'  => false,
            ],
            [
                'name'          => 'super_expresss_limit_weight',
                'title'         => 'vn-regional-shipping::app.admin.express-shipping.fields.super_expresss_limit_weight',
                'info'          => 'vn-regional-shipping::app.admin.express-shipping.fields.super_expresss_limit_weight_info',
                'type'          => 'text',
                'validation'    => 'required',
                'default'       => 2,
                'channel_based' => false,
                'locale_based'  => false,
            ],
            [
                'name'          => 'super_expresss_dim_divisor',
                'title'         => 'vn-regional-shipping::app.admin.express-shipping.fields.super_expresss_dim_divisor',  
                'info'          => 'vn-regional-shipping::app.admin.express-shipping.fields.super_expresss_dim_divisor_info',
                'type'          => 'number',
                'validation'    => 'numeric',
                'default'       => 6000,
                'channel_based' => false,
                'locale_based'  => false,
            ],
            [
                'name'          => 'super_expresss_limit_dimension',
                'title'         => 'vn-regional-shipping::app.admin.express-shipping.fields.super_expresss_limit_dimension',
                'info'          => 'vn-regional-shipping::app.admin.express-shipping.fields.super_expresss_limit_dimension_info',
                'type'          => 'text',
                'validation'    => 'required',
                'default'       => 0.5,
                'channel_based' => false,
                'locale_based'  => false,
            ],
        ],
    ],
];
