{{-- SEO Meta Content --}}
@push('meta')
    <meta name="description" content="Đăng nhập hoặc đăng ký tài khoản VPP Shop"/>
    <meta name="keywords" content="đăng nhập, đăng ký, tài khoản"/>
@endPush

<x-ta-vpp-theme::layouts>
    <x-slot:title>
        Đăng nhập - VPP Shop
    </x-slot>

    <main class="page">
        <div class="container">
            <div class="breadcrumb">Trang chủ / Tài khoản</div>
            
            <div class="auth-container">
                <!-- Tab Navigation -->
                <div class="auth-tabs">
                    <button class="auth-tab active" data-tab="login">
                        Đăng nhập
                    </button>
                    <button class="auth-tab" data-tab="register">
                        Đăng ký
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="auth-content">
                    <!-- Login Panel -->
                    <div id="login" class="auth-panel active">
                        <div>
                            <h2>Đăng nhập</h2>
                            <p class="subtitle">Chào mừng bạn quay lại. Vui lòng đăng nhập để tiếp tục.</p>
                        </div>

                        <form action="{{ route('shop.customer.session.create') }}" method="POST">
                            @csrf
                            <div class="form-grid">
                                <!-- Email -->
                                <div class="control-group">
                                    <label for="email" class="required">Email</label>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        id="email" 
                                        value="{{ old('email') }}" 
                                        placeholder="you@email.com" 
                                        required
                                        class="{{ $errors->has('email') ? 'border-red-500' : '' }}"
                                    >
                                    @if ($errors->has('email'))
                                        <p class="text-red-500">{{ $errors->first('email') }}</p>
                                    @endif
                                </div>

                                <!-- Password -->
                                <div class="control-group">
                                    <label for="login-password" class="required">Mật khẩu</label>
                                    <div class="password-wrapper">
                                        <input 
                                            type="password" 
                                            name="password" 
                                            id="login-password" 
                                            placeholder="Nhập mật khẩu" 
                                            required
                                            class="{{ $errors->has('password') ? 'border-red-500' : '' }}"
                                        >
                                        <i class="fa-solid fa-eye toggle-password" data-target="login-password"></i>
                                    </div>
                                    @if ($errors->has('password'))
                                        <p class="text-red-500">{{ $errors->first('password') }}</p>
                                    @endif
                                </div>

                                <!-- Remember Me & Forgot Password -->
                                <div class="form-actions">
                                    <label>
                                        <input type="checkbox" name="remember" /> 
                                        Ghi nhớ đăng nhập
                                    </label>
                                    <a href="{{ route('shop.customers.forgot_password.create') }}">Quên mật khẩu?</a>
                                </div>

                                <!-- Captcha -->
                                @if (core()->getConfigData('customer.captcha.credentials.status'))
                                    <div class="control-group">
                                        {!! \Webkul\Customer\Facades\Captcha::render() !!}
                                        @if ($errors->has('g-recaptcha-response'))
                                            <p class="text-red-500">{{ $errors->first('g-recaptcha-response') }}</p>
                                        @endif
                                    </div>
                                @endif

                                <!-- Submit Button -->
                                <button class="btn btn-primary" type="submit">
                                    Đăng nhập
                                </button>

                                <!-- Social Login (Optional) -->
                                @if (core()->getConfigData('customer.social_login.enable_facebook') || core()->getConfigData('customer.social_login.enable_google'))
                                    <div class="social-login">
                                        @if (core()->getConfigData('customer.social_login.enable_google'))
                                            <a href="{{ route('customer.social-login.index', 'google') }}" class="btn">
                                                <i class="fa-brands fa-google"></i> Đăng nhập bằng Google
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Register Panel -->
                    <div id="register" class="auth-panel">
                        <div>
                            <h2>Tạo tài khoản</h2>
                            <p class="subtitle">Đăng ký để quản lý đơn hàng và lưu sản phẩm yêu thích.</p>
                        </div>

                        <form action="{{ route('shop.customers.register.store') }}" method="POST">
                            @csrf
                            <div class="form-grid">
                                <!-- First Name -->
                                <div class="control-group">
                                    <label for="first_name" class="required">Họ</label>
                                    <input 
                                        type="text" 
                                        name="first_name" 
                                        id="first_name" 
                                        value="{{ old('first_name') }}" 
                                        placeholder="Nhập họ" 
                                        required
                                        class="{{ $errors->has('first_name') ? 'border-red-500' : '' }}"
                                    >
                                    @if ($errors->has('first_name'))
                                        <p class="text-red-500">{{ $errors->first('first_name') }}</p>
                                    @endif
                                </div>

                                <!-- Last Name -->
                                <div class="control-group">
                                    <label for="last_name" class="required">Tên</label>
                                    <input 
                                        type="text" 
                                        name="last_name" 
                                        id="last_name" 
                                        value="{{ old('last_name') }}" 
                                        placeholder="Nhập tên" 
                                        required
                                        class="{{ $errors->has('last_name') ? 'border-red-500' : '' }}"
                                    >
                                    @if ($errors->has('last_name'))
                                        <p class="text-red-500">{{ $errors->first('last_name') }}</p>
                                    @endif
                                </div>

                                <!-- Email -->
                                <div class="control-group">
                                    <label for="reg-email" class="required">Email</label>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        id="reg-email" 
                                        value="{{ old('email') }}" 
                                        placeholder="you@email.com" 
                                        required
                                        class="{{ $errors->has('email') ? 'border-red-500' : '' }}"
                                    >
                                    @if ($errors->has('email'))
                                        <p class="text-red-500">{{ $errors->first('email') }}</p>
                                    @endif
                                </div>

                                <!-- Password -->
                                <div class="control-group">
                                    <label for="register-password" class="required">Mật khẩu</label>
                                    <div class="password-wrapper">
                                        <input 
                                            type="password" 
                                            name="password" 
                                            id="register-password" 
                                            placeholder="Tạo mật khẩu" 
                                            required
                                            class="{{ $errors->has('password') ? 'border-red-500' : '' }}"
                                        >
                                        <i class="fa-solid fa-eye toggle-password" data-target="register-password"></i>
                                    </div>
                                    @if ($errors->has('password'))
                                        <p class="text-red-500">{{ $errors->first('password') }}</p>
                                    @endif
                                </div>

                                <!-- Confirm Password -->
                                <div class="control-group">
                                    <label for="register-password-confirm" class="required">Xác nhận mật khẩu</label>
                                    <div class="password-wrapper">
                                        <input 
                                            type="password" 
                                            name="password_confirmation" 
                                            id="register-password-confirm" 
                                            placeholder="Nhập lại mật khẩu" 
                                            required
                                        >
                                        <i class="fa-solid fa-eye toggle-password" data-target="register-password-confirm"></i>
                                    </div>
                                </div>

                                <!-- Newsletter Subscription -->
                                @if (core()->getConfigData('customer.settings.create_new_account_options.news_letter'))
                                    <div class="form-actions">
                                        <label>
                                            <input type="checkbox" name="is_subscribed" value="1" id="is-subscribed" {{ old('is_subscribed') ? 'checked' : '' }} />
                                            Đăng ký nhận tin tức và ưu đãi
                                        </label>
                                    </div>
                                @endif

                                <!-- GDPR Agreement -->
                                @if(
                                    core()->getConfigData('general.gdpr.settings.enabled')
                                    && core()->getConfigData('general.gdpr.agreement.enabled')
                                )
                                    <div class="form-actions">
                                        <label>
                                            <input type="checkbox" name="agreement" value="1" id="agreement" required />
                                            {{ core()->getConfigData('general.gdpr.agreement.agreement_label') }}
                                        </label>
                                    </div>
                                    @if ($errors->has('agreement'))
                                        <p class="text-red-500">{{ $errors->first('agreement') }}</p>
                                    @endif
                                @endif

                                <!-- Captcha -->
                                @if (core()->getConfigData('customer.captcha.credentials.status'))
                                    <div class="control-group">
                                        {!! \Webkul\Customer\Facades\Captcha::render() !!}
                                        @if ($errors->has('g-recaptcha-response'))
                                            <p class="text-red-500">{{ $errors->first('g-recaptcha-response') }}</p>
                                        @endif
                                    </div>
                                @endif

                                <!-- Submit Button -->
                                <button class="btn btn-primary" type="submit">
                                    Đăng ký
                                </button>

                                <!-- Terms Text -->
                                <p class="terms-text">
                                    Bằng việc đăng ký, bạn đồng ý với 
                                    <a href="#">Điều khoản</a> và 
                                    <a href="#">Chính sách bảo mật</a> của chúng tôi.
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @push('scripts')
        {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
    @endpush
</x-ta-vpp-theme::layouts>
