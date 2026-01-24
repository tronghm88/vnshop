@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Pagination Navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="btn-pagination disabled" aria-disabled="true" aria-label="{{ trans('ta-vpp-theme::app.partials.pagination.prev-page') }}">
                <i class="fa-solid fa-chevron-left"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn-pagination" rel="prev" aria-label="{{ trans('ta-vpp-theme::app.partials.pagination.prev-page') }}">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="btn-pagination disabled" aria-disabled="true">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="btn-pagination active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="btn-pagination" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn-pagination" rel="next" aria-label="{{ trans('ta-vpp-theme::app.partials.pagination.next-page') }}">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        @else
            <span class="btn-pagination disabled" aria-disabled="true" aria-label="{{ trans('ta-vpp-theme::app.partials.pagination.next-page') }}">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
        @endif
    </nav>
@endif
