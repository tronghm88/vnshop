<x-ta-vpp-theme::layouts>
    <x-slot:title>
        @lang('shop::app.customers.account.addresses.index.title')
    </x-slot>

    <div class="container">
        {{-- Breadcrumb --}}
        <nav class="breadcrumb">
            <a href="{{ route('shop.home.index') }}">Trang chủ</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Tài khoản</span>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Sổ địa chỉ</span>
        </nav>

        <div class="account-layout">
            {{-- Sidebar --}}
            <aside class="account-sidebar">
                <div class="account-menu">
                    <nav class="account-nav">
                        <a href="{{ route('shop.customers.account.profile.index') }}" class="account-nav-item">
                            <i class="fa-solid fa-user"></i>
                            <span>Thông tin tài khoản</span>
                        </a>
                        <a href="{{ route('shop.customers.account.orders.index') }}" class="account-nav-item">
                            <i class="fa-solid fa-bag-shopping"></i>
                            <span>Đơn hàng của tôi</span>
                        </a>
                        <a href="{{ route('shop.customers.account.addresses.index') }}" class="account-nav-item active">
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

            {{-- Content --}}
            <div class="account-content">
                 <div class="account-content-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h1>@lang('shop::app.customers.account.addresses.index.title')</h1>
                        <p>Quản lý sổ địa chỉ nhận hàng của bạn</p>
                    </div>
                    
                    <a href="{{ route('shop.customers.account.addresses.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i>
                        @lang('shop::app.customers.account.addresses.index.add-address')
                    </a>
                </div>

                @if (! $addresses->isEmpty())
                    <div class="address-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                        @foreach ($addresses as $address)
                            <div class="address-card" style="border: 1px solid var(--border); border-radius: 12px; padding: 20px; position: relative; background: #fff; {{ $address->default_address ? 'border-color: var(--primary);' : '' }}">
                                @if ($address->default_address)
                                    <div class="default-badge" style="position: absolute; top: 12px; right: 12px; background: var(--primary); color: white; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase;">
                                        @lang('shop::app.customers.account.addresses.index.default-address')
                                    </div>
                                @endif

                                <h3 style="margin: 0 0 12px; font-size: 18px; font-weight: 600; padding-right: 80px;">{{ $address->first_name }} {{ $address->last_name }}</h3>
                                
                                <div class="address-info" style="color: var(--text); font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
                                    @if ($address->company_name)
                                        <p style="margin: 0; font-weight: 500;">{{ $address->company_name }}</p>
                                    @endif
                                    
                                    <p style="margin: 0;">{{ $address->address1 }}</p>
                                    @if (isset($address->address2))
                                        <p style="margin: 0;">{{ $address->address2 }}</p>
                                    @endif
                                    
                                    <p style="margin: 0;">{{ $address->city }}, {{ $address->state }} {{ $address->postcode }}</p>
                                    <p style="margin: 0;">{{ $address->country }}</p>
                                    
                                    <p style="margin-top: 12px; display: flex; align-items: center; gap: 8px;">
                                        <i class="fa-solid fa-phone" style="color: var(--muted); font-size: 13px;"></i> 
                                        {{ $address->phone }}
                                    </p>
                                </div>

                                <div class="address-actions" style="display: flex; gap: 16px; border-top: 1px solid var(--border); padding-top: 16px; align-items: center;">
                                    <a href="{{ route('shop.customers.account.addresses.edit', $address->id) }}" style="color: var(--text); font-weight: 500; font-size: 14px; display: flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-pen" style="font-size: 13px;"></i>
                                        @lang('shop::app.customers.account.addresses.index.edit')
                                    </a>
                                    
                                    <form action="{{ route('shop.customers.account.addresses.delete', $address->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa địa chỉ này?');">
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit" style="background: none; border: none; color: #dc3545; font-weight: 500; font-size: 14px; cursor: pointer; padding: 0; display: flex; align-items: center; gap: 6px;">
                                            <i class="fa-solid fa-trash" style="font-size: 13px;"></i>
                                            @lang('shop::app.customers.account.addresses.index.delete')
                                        </button>
                                    </form>

                                    @if (! $address->default_address)
                                        <form action="{{ route('shop.customers.account.addresses.update.default', $address->id) }}" method="POST" style="margin-left: auto;">
                                            @method('PATCH')
                                            @csrf
                                            <button type="submit" style="background: none; border: none; color: var(--primary); font-weight: 500; font-size: 13px; cursor: pointer; padding: 0;">
                                                @lang('shop::app.customers.account.addresses.index.set-as-default')
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                         <div class="empty-icon"><i class="fa-solid fa-location-dot"></i></div>
                         <h3>@lang('shop::app.customers.account.addresses.index.empty-address')</h3>
                         <p>Bạn chưa có địa chỉ nào trong sổ địa chỉ.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-ta-vpp-theme::layouts>
