<x-ta-vpp-theme::layouts>
    <x-slot:title>
        {{ trans('shop::app.checkout.success.thanks') }}
    </x-slot>

    <div class="container" style="padding: 100px 20px; text-align: center;">
        <div style="margin-bottom: 30px;">
            <i class="fa-solid fa-circle-check" style="font-size: 80px; color: #4CAF50;"></i>
        </div>

        <h1 style="margin-bottom: 20px;">{{ trans('shop::app.checkout.success.thanks') }}</h1>

        <p style="font-size: 18px; margin-bottom: 30px;">
            @if (auth()->guard('customer')->user())
                {!! trans('shop::app.checkout.success.order-id-info', [
                    'order_id' => '<a href="'.route('shop.customers.account.orders.view', $order->id).'" style="color: var(--primary-color); font-weight: bold;">'.$order->increment_id.'</a>'
                ]) !!}
            @else
                {{ trans('shop::app.checkout.success.order-id-info', ['order_id' => $order->increment_id]) }}
            @endif
        </p>

        <p style="color: #666; margin-bottom: 40px; max-width: 600px; margin-left: auto; margin-right: auto;">
            @if (! empty($order->checkout_message))
                {!! nl2br($order->checkout_message) !!}
            @else
                {{ trans('shop::app.checkout.success.info') }}
            @endif
        </p>

        <a href="{{ route('shop.home.index') }}" class="btn btn-primary" style="padding: 12px 40px;">
            {{ trans('shop::app.checkout.cart.index.continue-shopping') }}
        </a>
    </div>
</x-ta-vpp-theme::layouts>
