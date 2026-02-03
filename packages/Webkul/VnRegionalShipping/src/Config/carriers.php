<?php

return [
    'vn_regional_shipping' => [
        'code'         => 'vn_regional_shipping',
        'title'        => 'vn-regional-shipping::app.admin.regional-shipping.title',
        'description'  => 'vn-regional-shipping::app.admin.regional-shipping.description',
        'active'       => true,
        'default_rate' => '30000',
        'type'         => 'per_order',
        'class'        => 'Webkul\VnRegionalShipping\Carriers\VnRegionalShipping',
    ],
    'vn_express_shipping' => [
        'code'         => 'vn_express_shipping',
        'title'        => 'vn-regional-shipping::app.admin.express-shipping.title',
        'description'  => 'vn-regional-shipping::app.admin.express-shipping.description',
        'active'       => true,
        'default_rate' => '30000',
        'type'         => 'per_order',
        'class'        => 'Webkul\VnRegionalShipping\Carriers\VnExpressShipping',
    ],
];
