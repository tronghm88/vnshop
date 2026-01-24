<x-ta-vpp-theme::layouts>
    <x-slot:title>
        @lang('shop::app.customers.account.addresses.create.add-address')
    </x-slot>

    <div class="container">
         {{-- Breadcrumb --}}
        <nav class="breadcrumb">
            <a href="{{ route('shop.home.index') }}">Trang chủ</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Tài khoản</span>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('shop.customers.account.addresses.index') }}">Sổ địa chỉ</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Thêm địa chỉ mới</span>
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
                         {{-- Other links --}}
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
                <div class="account-content-header">
                    <h1>@lang('shop::app.customers.account.addresses.create.add-address')</h1>
                    <p>Thêm địa chỉ mới vào sổ địa chỉ của bạn</p>
                </div>

                <form action="{{ route('shop.customers.account.addresses.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-grid" style="grid-template-columns: 1fr; gap: 20px;">
                        
                        <div class="form-group">
                            <label for="company_name">@lang('shop::app.customers.account.addresses.create.company-name')</label>
                            <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}">
                             @error('company_name')
                                <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="required" for="first_name">@lang('shop::app.customers.account.addresses.create.first-name')</label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                    <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="required" for="last_name">@lang('shop::app.customers.account.addresses.create.last-name')</label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required>
                                @error('last_name')
                                    <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="required" for="email">@lang('shop::app.customers.account.addresses.create.email')</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                            @error('email')
                                <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="vat_id">@lang('shop::app.customers.account.addresses.create.vat-id')</label>
                            <input type="text" name="vat_id" id="vat_id" value="{{ old('vat_id') }}">
                            @error('vat_id')
                                <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                             <label class="required" for="address_0">@lang('shop::app.customers.account.addresses.create.street-address')</label>
                             <input type="text" name="address[]" id="address_0" value="{{ old('address.0') }}" required placeholder="Địa chỉ dòng 1">
                             @if (core()->getConfigData('customer.address.information.street_lines') > 1)
                                @for ($i = 1; $i < core()->getConfigData('customer.address.information.street_lines'); $i++)
                                    <input type="text" name="address[{{ $i }}]" id="address_{{ $i }}" value="{{ old('address.'.$i) }}" style="margin-top: 10px;" placeholder="Địa chỉ dòng {{ $i+1 }}">
                                @endfor
                             @endif
                             @error('address.*')
                                <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                             @enderror
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="required" for="country">@lang('shop::app.customers.account.addresses.create.country')</label>
                                <select name="country" id="country" required onchange="updateStates(this.value)">
                                    <option value="">@lang('shop::app.customers.account.addresses.create.select-country')</option>
                                    @foreach (core()->countries() as $country)
                                        <option value="{{ $country->code }}" {{ old('country') == $country->code ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                @error('country')
                                    <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="required" for="state">@lang('shop::app.customers.account.addresses.create.state')</label>
                                {{-- We will swap between select and input via JS --}}
                                <div id="state-container">
                                    <input type="text" name="state" id="state" value="{{ old('state') }}" placeholder="@lang('shop::app.customers.account.addresses.create.state')">
                                </div>
                                @error('state')
                                    <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="required" for="city">@lang('shop::app.customers.account.addresses.create.city')</label>
                                <input type="text" name="city" id="city" value="{{ old('city') }}" required>
                                @error('city')
                                    <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="required" for="postcode">@lang('shop::app.customers.account.addresses.create.post-code')</label>
                                <input type="text" name="postcode" id="postcode" value="{{ old('postcode') }}" required>
                                @error('postcode')
                                    <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="required" for="phone">@lang('shop::app.customers.account.addresses.create.phone')</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required>
                            @error('phone')
                                <p style="color: #dc3545; font-size: 13px; margin-top: 4px;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                             <label class="checkbox-container">
                                <input type="checkbox" name="default_address" value="1" {{ old('default_address') ? 'checked' : '' }}>
                                <span class="checkmark"></span>
                                <span class="checkbox-label">@lang('shop::app.customers.account.addresses.create.set-as-default')</span>
                            </label>
                        </div>

                    </div>

                    <div style="margin-top: 32px; display: flex; gap: 16px;">
                        <button type="submit" class="btn btn-primary">@lang('shop::app.customers.account.addresses.create.save')</button>
                        <a href="{{ route('shop.customers.account.addresses.index') }}" class="btn btn-outline" style="border: 1px solid var(--border); color: var(--text);">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const countryStates = @json(core()->groupedStatesByCountries());
            const oldState = "{{ old('state') }}";

            function updateStates(countryCode) {
                const container = document.getElementById('state-container');
                const states = countryStates[countryCode];

                if (states && states.length > 0) {
                    let html = '<select name="state" id="state" required style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border);">';
                    html += '<option value="">@lang("shop::app.customers.account.addresses.create.select-state")</option>';
                    
                    states.forEach(state => {
                        const selected = (state.code === oldState || state.default_name === oldState) ? 'selected' : '';
                        html += `<option value="${state.code}" ${selected}>${state.default_name}</option>`;
                    });
                    
                    html += '</select>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `<input type="text" name="state" id="state" value="${oldState}" required placeholder="@lang('shop::app.customers.account.addresses.create.state')" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border);">`;
                }
            }

            // Initialize on page load if country is selected
            document.addEventListener('DOMContentLoaded', function() {
                const countrySelect = document.getElementById('country');
                if (countrySelect.value) {
                    updateStates(countrySelect.value);
                }
            });
        </script>
    @endpush
</x-ta-vpp-theme::layouts>
