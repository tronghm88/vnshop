<x-ta-vpp-theme::layouts>
    <x-slot:title>
        @lang('shop::app.customers.account.profile.edit.edit-profile')
    </x-slot>

    <div class="container">
        {{-- Breadcrumb --}}
        <nav class="breadcrumb">
            <a href="{{ route('shop.home.index') }}">Trang chủ</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Tài khoản</span>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('shop.customers.account.profile.index') }}">Thông tin tài khoản</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Chỉnh sửa</span>
        </nav>

        <div class="account-layout">
            {{-- Sidebar Navigation --}}
            <aside class="account-sidebar">
                <div class="account-menu">
                    <nav class="account-nav">
                        <a href="{{ route('shop.customers.account.profile.index') }}" class="account-nav-item active">
                            <i class="fa-solid fa-user"></i>
                            <span>Thông tin tài khoản</span>
                        </a>
                        <a href="{{ route('shop.customers.account.orders.index') }}" class="account-nav-item">
                            <i class="fa-solid fa-bag-shopping"></i>
                            <span>Đơn hàng của tôi</span>
                        </a>
                        <a href="{{ route('shop.customers.account.addresses.index') }}" class="account-nav-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>Địa chỉ</span>
                        </a>
                        <a href="{{ route('shop.customers.account.reviews.index') }}" class="account-nav-item">
                            <i class="fa-solid fa-star"></i>
                            <span>Đánh giá của tôi</span>
                        </a>
                        <a href="{{ route('shop.customer.session.destroy') }}" class="account-nav-item">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Đăng xuất</span>
                        </a>
                    </nav>
                </div>
            </aside>

            {{-- Main Content --}}
            <div class="account-content">
                <div class="account-content-header">
                    <h1>@lang('shop::app.customers.account.profile.edit.edit-profile')</h1>
                    <p>Cập nhật thông tin cá nhân của bạn</p>
                </div>

                <form action="{{ route('shop.customers.account.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- Avatar --}}
                    <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 20px;">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: #f1f1f1; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                            @if($customer->image)
                                <img src="{{ $customer->image_url }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <i class="fa-solid fa-user" style="font-size: 32px; color: #999;"></i>
                            @endif
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label for="image">Ảnh đại diện</label>
                            <input type="file" name="image[]" id="image" accept="image/*" style="border: 1px solid var(--border); border-radius: 8px; padding: 8px; width: 100%;">
                            @error('image[]')
                                <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">
                        {{-- First Name --}}
                        <div class="form-group">
                            <label class="required" for="first_name">@lang('shop::app.customers.account.profile.edit.first-name')</label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') ?? $customer->first_name }}" required>
                            @error('first_name')
                                <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Last Name --}}
                        <div class="form-group">
                            <label class="required" for="last_name">@lang('shop::app.customers.account.profile.edit.last-name')</label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') ?? $customer->last_name }}" required>
                            @error('last_name')
                                <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 16px;">
                        {{-- Email --}}
                        <div class="form-group">
                            <label class="required" for="email">@lang('shop::app.customers.account.profile.edit.email')</label>
                            <input type="email" name="email" id="email" value="{{ old('email') ?? $customer->email }}" required>
                            @error('email')
                                <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div class="form-group">
                            <label class="required" for="phone">@lang('shop::app.customers.account.profile.edit.phone')</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') ?? $customer->phone }}" required>
                            @error('phone')
                                <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 16px;">
                        {{-- Gender --}}
                        <div class="form-group">
                            <label class="required" for="gender">@lang('shop::app.customers.account.profile.edit.gender')</label>
                            <select name="gender" id="gender" required>
                                <option value="Male" {{ (old('gender') ?? $customer->gender) == 'Male' ? 'selected' : '' }}>@lang('shop::app.customers.account.profile.edit.male')</option>
                                <option value="Female" {{ (old('gender') ?? $customer->gender) == 'Female' ? 'selected' : '' }}>@lang('shop::app.customers.account.profile.edit.female')</option>
                                <option value="Other" {{ (old('gender') ?? $customer->gender) == 'Other' ? 'selected' : '' }}>@lang('shop::app.customers.account.profile.edit.other')</option>
                            </select>
                            @error('gender')
                                <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- DOB --}}
                        <div class="form-group">
                            <label for="date_of_birth">@lang('shop::app.customers.account.profile.edit.dob')</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') ?? $customer->date_of_birth }}">
                            @error('date_of_birth')
                                <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border);">
                        <h3 style="font-size: 18px; margin-bottom: 20px;">Thay đổi mật khẩu</h3>
                        <p style="color: var(--muted); font-size: 14px; margin-bottom: 20px;">Để trống nếu không muốn thay đổi mật khẩu</p>

                        <div class="form-group" style="margin-bottom: 16px;">
                            <label for="current_password">@lang('shop::app.customers.account.profile.edit.current-password')</label>
                            <input type="password" name="current_password" id="current_password">
                            @error('current_password')
                                <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label for="new_password">@lang('shop::app.customers.account.profile.edit.new-password')</label>
                                <input type="password" name="new_password" id="new_password">
                                @error('new_password')
                                    <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="new_password_confirmation">@lang('shop::app.customers.account.profile.edit.confirm-password')</label>
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation">
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 24px;">
                        <label class="checkbox-container">
                            <input type="checkbox" name="subscribed_to_news_letter" {{ (old('subscribed_to_news_letter') ?? $customer->subscribed_to_news_letter) ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            <span class="checkbox-label">@lang('shop::app.customers.account.profile.edit.subscribe-to-newsletter')</span>
                        </label>
                    </div>

                    <div style="margin-top: 32px; display: flex; gap: 16px;">
                        <button type="submit" class="btn btn-primary">@lang('shop::app.customers.account.profile.edit.save')</button>
                        <a href="{{ route('shop.customers.account.profile.index') }}" class="btn btn-outline" style="border: 1px solid var(--border); color: var(--text);">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-ta-vpp-theme::layouts>
