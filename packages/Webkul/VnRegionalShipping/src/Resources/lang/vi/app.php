<?php

return [
    'admin' => [
        'regional-shipping' => [
            'title' => 'Giao hàng theo miền',
            'description' => 'Giao hàng theo miền là phương thức giao hàng tính cước phí dựa trên miền khách hàng ở Việt Nam.',
            'fields' => [
                'active' => 'Kích hoạt',
                'title' => 'Tiêu đề',
                'rate_north' => 'Cước miền Bắc (VNĐ)',
                'rate_middle' => 'Cước miền Trung (VNĐ)',
                'rate_south' => 'Cước miền Nam (VNĐ)',
                'dim_divisor' => 'Hệ số quy đổi kích thước (mặc định 6000)',
                'dim_divisor_info' => 'Công thức: (Dài * Rộng * Cao) / Hệ số. Kết quả dùng để tính phụ phí kích thước cho từng sản phẩm.',
                'dimension_rates' => 'Phụ phí kích thước (tính trên từng sản phẩm)',
                'dimension_rates_info' => 'Định dạng: giá_trị:phí;giá_trị:phí (VD: 0.1:10000;0.5:20000). Nếu kích thước quy đổi của một sản phẩm đạt mức nào, sẽ cộng thêm phí tương ứng nhân với số lượng sản phẩm đó.',
                'weight_rates' => 'Phụ phí cân nặng (kg) (tính trên tổng giỏ hàng)',
                'weight_rates_info' => 'Định dạng: kg:phí;kg:phí (VD: 1:5000;5:15000). Phí sẽ áp dụng một lần cho mức cao nhất mà tổng cân nặng của cả giỏ hàng đạt tới.',
            ],
        ],
        'express-shipping' => [
            'title' => 'Giao hàng siêu tốc',
            'description' => 'Giao hàng siêu tốc là phương thức giao hàng nhanh tính cước phí dựa trên 1 tỉnh thành cụ thể ở Việt Nam.',
            'fields' => [
                'super_expresss_state' => 'Tỉnh thành giao hàng siêu tốc',
                'super_expresss_state_info' => 'Chọn một tỉnh thành giao hàng siêu tốc.',
                'super_expresss_rate' => 'Cước giao hàng siêu tốc',
                'super_expresss_rate_info' => 'Nhập phí giao hàng siêu tốc.',
                'super_expresss_limit_weight' => 'Cân nặng giới hạn (kg)',
                'super_expresss_limit_weight_info' => 'Nhập cân nặng giới hạn (kg) để áp dụng phí giao hàng siêu tốc.',
                'super_expresss_dim_divisor' => 'Hệ số quy đổi kích thước (mặc định 6000)',
                'super_expresss_dim_divisor_info' => 'Công thức: (Dài * Rộng * Cao) / Hệ số. Kết quả dùng để tính phụ phí kích thước cho từng sản phẩm.',
                'super_expresss_limit_dimension' => 'Kích thước giới hạn (m)',
                'super_expresss_limit_dimension_info' => 'Nhập kích thước giới hạn (m) để áp dụng phí giao hàng siêu tốc.',
            ],
        ],
    ],
    'view' => [
        'express-shipping' => [
            'method_title' => 'Giao hàng hoả tốc',
        ],
        'regional-shipping' => [
            'method_title' => 'Giao hàng tiêu chuẩn',
        ],
    ],
];