@inject('themeCustomizationRepository', 'Webkul\Theme\Repositories\ThemeCustomizationRepository')

@php
    $channel = core()->getCurrentChannel();

    $customization = $themeCustomizationRepository->findOneWhere([
        'type'       => 'footer_links',
        'status'     => 1,
        'theme_code' => $channel->theme,
        'channel_id' => $channel->id,
    ]);

@endphp

{!! view_render_event('bagisto.shop.layout.footer.before') !!}

{{-- Footer Component --}}
<footer class="site-footer">
    <div class="container">
        <div class="footer-links">
            <div class="footer-col">
                @if ($logo = core()->getCurrentChannel()->logo_url)
                    <img src="{{ $logo }}" alt="{{ config('app.name') }}" style="max-height: 40px;">
                @else
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" style="max-height: 40px;">
                @endif
                <p>{!! core()->getCurrentChannel()->description ?? trans('ta-vpp-theme::app.emails.customers.subscribed.description') !!}</p>
            </div>
            
            @if ($customization?->options)
                @foreach ($customization->options as $footerLinkSection)
                    <div class="footer-col" v-pre>
                        @php
                            usort($footerLinkSection, function ($a, $b) {
                                return $a['sort_order'] - $b['sort_order'];
                            });
                        @endphp

                        <ul style="list-style: none; padding: 0;">
                            @foreach ($footerLinkSection as $link)
                                <li>
                                    <a href="{{ $link['url'] }}">
                                        {{ $link['title'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @endif

            <div class="footer-col">
                @if (core()->getConfigData('customer.settings.newsletter.subscription'))
                    {!! view_render_event('bagisto.shop.layout.footer.newsletter_subscription.before') !!}

                    <h4>@lang('shop::app.components.layouts.footer.newsletter-text')</h4>
                    
                    <p style="margin-bottom: 20px; color: #9ca3af; font-size: 14px;">
                        @lang('shop::app.components.layouts.footer.subscribe-stay-touch')
                    </p>

                    <x-shop::form
                        :action="route('shop.subscription.store')"
                        style="margin-top: 10px;"
                    >
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <x-shop::form.control-group.control
                                type="email"
                                name="email"
                                rules="required|email"
                                label="Email"
                                :aria-label="trans('shop::app.components.layouts.footer.email')"
                                placeholder="email@example.com"
                                style="width: 100%; padding: 12px 15px; border-radius: 8px; border: 1px solid #374151; background: #1f2937; color: #fff; font-size: 14px;"
                            />

                            <button
                                type="submit"
                                style="width: 100%; padding: 12px 25px; border-radius: 8px; background: var(--primary, #2563eb); color: #fff; border: none; cursor: pointer; font-weight: 600; font-size: 14px; transition: opacity 0.2s;"
                                onmouseover="this.style.opacity='0.9'"
                                onmouseout="this.style.opacity='1'"
                            >
                                @lang('shop::app.components.layouts.footer.subscribe')
                            </button>
                        </div>

                        <x-shop::form.control-group.error control-name="email" style="margin-top: 5px; color: #ef4444; font-size: 12px;" />
                    </x-shop::form>

                    {!! view_render_event('bagisto.shop.layout.footer.newsletter_subscription.after') !!}
                @endif
            </div>
        </div>

        <div class="footer-bottom">
            {!! view_render_event('bagisto.shop.layout.footer.footer_text.before') !!}

            @lang('shop::app.components.layouts.footer.footer-text', ['current_year' => date('Y')])

            {!! view_render_event('bagisto.shop.layout.footer.footer_text.after') !!}
        </div>
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}
