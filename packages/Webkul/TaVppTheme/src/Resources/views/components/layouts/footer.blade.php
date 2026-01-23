{{-- Footer Component --}}
<footer class="site-footer">
    <div class="container">
        <div class="footer-links">
            <div class="footer-col">
                <h4>Về {{ config('app.name') }}</h4>
                <p>{{ core()->getCurrentChannel()->description ?? 'Mua sắm văn phòng phẩm, bút, vở và quà tặng nhanh gọn.' }}</p>
                <div class="social-links" style="margin-top: 15px; display: flex; gap: 15px;">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
            
            <div class="footer-col">
                <h4>Chăm sóc khách hàng</h4>
                <ul style="list-style: none; padding: 0;">
                    <li><a href="#">Giao hàng & đổi trả</a></li>
                    <li><a href="#">Phương thức thanh toán</a></li>
                    <li><a href="#">Hỏi đáp</a></li>
                    <li><a href="#">Chính sách bảo mật</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h4>Liên kết nhanh</h4>
                <ul style="list-style: none; padding: 0;">
                    <li><a href="{{ route('shop.home.index') }}">Trang chủ</a></li>
                    <li><a href="#">Tất cả sản phẩm</a></li>
                    @guest('customer')
                        <li><a href="{{ route('shop.customer.session.index') }}">Đăng nhập</a></li>
                    @endguest
                    @auth('customer')
                        <li><a href="{{ route('shop.customers.account.profile.index') }}">Tài khoản</a></li>
                    @endauth
                    <li><a href="{{ route('shop.checkout.cart.index') }}">Thanh toán</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Liên hệ</h4>
                <p><i class="fa-solid fa-location-dot"></i> {{ core()->getCurrentChannel()->address ?? 'Địa chỉ cửa hàng' }}</p>
                <p><i class="fa-solid fa-phone"></i> {{ core()->getCurrentChannel()->phone ?? '1900 xxxx' }}</p>
                <p><i class="fa-solid fa-envelope"></i> {{ core()->getCurrentChannel()->contact_email ?? 'contact@example.com' }}</p>
            </div>
        </div>
        
        <div class="footer-bottom">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Bảo lưu mọi quyền.
        </div>
    </div>
</footer>
