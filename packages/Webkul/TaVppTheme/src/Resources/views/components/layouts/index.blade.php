{{-- Layout Component --}}
@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title . ' - ' : '' }}{{ config('app.name') }}</title>

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @bagistoVite(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'], 'ta-vpp-theme')

    @stack('styles')

    {!! view_render_event('bagisto.shop.layout.head.after') !!}
</head>

<body>
    {!! view_render_event('bagisto.shop.layout.body.before') !!}

    <x-ta-vpp-theme::layouts.header />

    <main class="page">
        {{-- Flash Messages --}}
        @foreach (['success', 'error', 'warning', 'info'] as $type)
            @if (session($type))
                <script>
                    window.addEventListener('DOMContentLoaded', () => {
                        if (window.showFlashMessage) {
                            window.showFlashMessage('{{ $type }}', '{{ session($type) }}');
                        }
                    });
                </script>
            @endif
        @endforeach

        {{ $slot }}
    </main>

    <x-ta-vpp-theme::layouts.footer />

    {!! view_render_event('bagisto.shop.layout.body.after') !!}

    {{ $scripts ?? '' }}
    @stack('scripts')
</body>
</html>
