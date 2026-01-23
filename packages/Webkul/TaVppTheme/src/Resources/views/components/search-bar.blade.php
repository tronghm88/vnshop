{{-- Search Bar Component --}}
<div class="search-bar">
    <form action="{{ route('shop.search.index') }}" method="GET">
        <input 
            type="search" 
            name="query" 
            value="{{ request()->get('query') }}"
            placeholder="Tìm kiếm bút, vở, quà tặng..." 
            required
        />
        <button type="submit" aria-label="Tìm kiếm">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </form>
</div>
