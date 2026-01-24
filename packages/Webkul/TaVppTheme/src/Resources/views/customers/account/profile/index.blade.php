<x-ta-vpp-theme::layouts>
    <x-slot:title>
        @lang('shop::app.customers.account.profile.index.title')
    </x-slot>

    <div class="container">
        {{-- Breadcrumb --}}
        <nav class="breadcrumb">
            <a href="{{ route('shop.home.index') }}">Trang chủ</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Tài khoản</span>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Thông tin tài khoản</span>
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
                <div class="account-content-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h1>@lang('shop::app.customers.account.profile.index.title')</h1>
                        <p>Quản lý thông tin cá nhân của bạn</p>
                    </div>
                    
                    <a href="{{ route('shop.customers.account.profile.edit') }}" class="btn btn-primary">
                        <i class="fa-solid fa-pen-to-square"></i>
                        @lang('shop::app.customers.account.profile.index.edit')
                    </a>
                </div>

                <div class="profile-details-card" style="border: 1px solid var(--border); border-radius: 12px; padding: 24px; margin-bottom: 24px;">
                    <div class="profile-row" style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; padding: 16px 0; border-bottom: 1px solid var(--border);">
                        <div style="font-weight: 600; color: var(--muted);">@lang('shop::app.customers.account.profile.index.first-name')</div>
                        <div style="font-weight: 500; color: var(--text);">{{ $customer->first_name }}</div>
                    </div>

                    <div class="profile-row" style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; padding: 16px 0; border-bottom: 1px solid var(--border);">
                        <div style="font-weight: 600; color: var(--muted);">@lang('shop::app.customers.account.profile.index.last-name')</div>
                        <div style="font-weight: 500; color: var(--text);">{{ $customer->last_name }}</div>
                    </div>

                    <div class="profile-row" style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; padding: 16px 0; border-bottom: 1px solid var(--border);">
                        <div style="font-weight: 600; color: var(--muted);">@lang('shop::app.customers.account.profile.index.gender')</div>
                        <div style="font-weight: 500; color: var(--text);">{{ $customer->gender ?? '-' }}</div>
                    </div>

                    <div class="profile-row" style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; padding: 16px 0; border-bottom: 1px solid var(--border);">
                        <div style="font-weight: 600; color: var(--muted);">@lang('shop::app.customers.account.profile.index.dob')</div>
                        <div style="font-weight: 500; color: var(--text);">{{ $customer->date_of_birth ?? '-' }}</div>
                    </div>

                    <div class="profile-row" style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; padding: 16px 0;">
                        <div style="font-weight: 600; color: var(--muted);">@lang('shop::app.customers.account.profile.index.email')</div>
                        <div style="font-weight: 500; color: var(--text);">{{ $customer->email }}</div>
                    </div>
                </div>

                <div class="delete-account" style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border);">
                    <h3 style="color: #dc3545; font-size: 18px; margin-bottom: 12px;">Xóa tài khoản</h3>
                    <p style="color: var(--muted); margin-bottom: 16px;">Hành động này sẽ xóa vĩnh viễn tài khoản của bạn và không thể hoàn tác.</p>
                    <button type="button" class="btn" style="background-color: #dc3545; color: white; border: none;" onclick="document.getElementById('delete-profile-dialog').showModal()">
                        @lang('shop::app.customers.account.profile.index.delete-profile')
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Profile Modal --}}
    <dialog id="delete-profile-dialog" style="padding: 0; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); max-width: 400px; width: 90%;">
        <div style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; font-size: 20px;">@lang('shop::app.customers.account.profile.index.delete-profile')</h3>
                <button type="button" onclick="document.getElementById('delete-profile-dialog').close()" style="background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
            </div>
            
            <form action="{{ route('shop.customers.account.profile.destroy') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="password" style="display: block; margin-bottom: 8px; font-weight: 500;">@lang('shop::app.customers.account.profile.index.enter-password')</label>
                    <input type="password" name="password" id="password" required style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 8px;">
                    @error('password')
                        <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" onclick="document.getElementById('delete-profile-dialog').close()" class="btn" style="background: #f1f1f1; color: #333;">Hủy</button>
                    <button type="submit" class="btn" style="background: #dc3545; color: white;">@lang('shop::app.customers.account.profile.index.delete')</button>
                </div>
            </form>
        </div>
    </dialog>
</x-ta-vpp-theme::layouts>
