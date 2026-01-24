{{-- Search Bar Component --}}
<div class="search-bar">
    <form action="{{ route('shop.search.index') }}" method="GET">
        <input 
            type="search" 
            name="query" 
            value="{{ request()->get('query') }}"
            placeholder="{{ trans('ta-vpp-theme::app.components.layouts.header.desktop.bottom.search-text') }}" 
            required
        />
        <button type="submit" aria-label="{{ trans('ta-vpp-theme::app.components.layouts.header.desktop.bottom.search') }}">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </form>
</div>
