<?php

return [
    'admin' => [
        'regional-shipping' => [
            'title' => 'Vietnam Regional Shipping',
            'description' => 'Vietnam Regional Shipping is a shipping method that calculates the shipping cost based on the region of the customer in Vietnam.',
            'fields' => [
                'active' => 'Active',
                'title' => 'Title',
                'rate_north' => 'North Region Rate (VND)',
                'rate_middle' => 'Middle Region Rate (VND)',
                'rate_south' => 'South Region Rate (VND)',
                'dim_divisor' => 'Dimension Divisor (default 6000)',
                'dim_divisor_info' => 'Formula: (Length * Width * Height) / Divisor. Result is used to calculate dimension surcharge for each product.',
                'dimension_rates' => 'Dimension Surcharge (per product)',
                'dimension_rates_info' => 'Format: value:surcharge;value:surcharge (e.g., 0.1:10000;0.5:20000). Surcharge will be added if the converted dimension reaches the specified value, multiplied by the quantity of that product.',
                'weight_rates' => 'Weight Surcharge (kg) (total cart weight)',
                'weight_rates_info' => 'Format: kg:surcharge;kg:surcharge (e.g., 1:5000;5:15000). Surcharge will be applied once for the highest value where the total cart weight reaches.',
            ],
        ],
        'express-shipping' => [
            'title' => 'Vietnam Express Shipping',
            'description' => 'Vietnam Express Shipping is a fast shipping method that calculates the shipping cost based on a specific region in Vietnam.',
            'fields' => [
                'active' => 'Active',
                'super_expresss_state' => 'Super Express State',
                'super_expresss_state_info' => 'Select a state where Super Express will be applied.',
                'super_expresss_rate' => 'Super Express Rate',
                'super_expresss_rate_info' => 'Enter the surcharge for Super Express.',
                'super_expresss_limit_weight' => 'Limit Weight (kg)',
                'super_expresss_limit_weight_info' => 'Enter the limit weight (kg) to apply Super Express.',
                'super_expresss_dim_divisor' => 'Dimension Divisor (default 6000)',
                'super_expresss_dim_divisor_info' => 'Formula: (Length * Width * Height) / Divisor. Result is used to calculate dimension surcharge for each product.',
                'super_expresss_limit_dimension' => 'Limit Dimension (m)',
                'super_expresss_limit_dimension_info' => 'Enter the limit dimension (m) to apply Super Express.',
            ],
        ],
    ],
    'view' => [
        'express-shipping' => [
            'method_title' => 'Super Express',
        ],
        'regional-shipping' => [
            'method_title' => 'Standard Shipping',
        ],
    ],
];