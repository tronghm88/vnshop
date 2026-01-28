{{-- Search Bar Component --}}
@php
    $status = core()->getConfigData('suggestion.suggestion.general.status');
    $placeholder = core()->getConfigData('suggestion.suggestion.general.search_placeholder') 
        ?? trans('ta-vpp-theme::app.components.layouts.header.desktop.bottom.search-text');
    $minSearchTerms = core()->getConfigData('suggestion.suggestion.general.min_search_terms') ?? 2;
@endphp

<div class="search-bar">
    <form action="{{ route('shop.search.index') }}" method="GET" id="search-form-suggestion">
        <input 
            type="search" 
            name="query" 
            id="search-input-suggestion"
            value="{{ request()->get('query') }}"
            placeholder="{{ $placeholder }}" 
            required
            autocomplete="off"
        />
        <button 
            type="submit" 
            aria-label="{{ trans('ta-vpp-theme::app.components.layouts.header.desktop.bottom.search') }}"
        >
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </form>

    @if ($status)
        <div id="search-suggestion-popup" class="hidden">
            <div id="search-suggestion-content">
                <div id="search-suggestion-default-view">
                    {{-- Default view: History and Hot Keywords (SSR) --}}
                    {{-- History --}}
                    <div id="search-history-section" class="search-suggestion-section">
                        <div class="search-suggestion-title">
                            <i class="fa-solid fa-history"></i> @lang('suggestion::app.shop.search-suggestion.search-history')
                        </div>
                        <div id="search-history-list">
                            {{-- JS will populate this --}}
                        </div>
                    </div>
                    {{-- Popular Products --}}
                    @if (count($popularProducts) > 0)
                    <div class="search-suggestion-section">
                        <div class="search-suggestion-title">
                            <i class="fa-solid fa-fire"></i> @lang('suggestion::app.shop.search-suggestion.popular-products')
                        </div>
                        <div id="popular-products-list" class="search-suggestion-items">
                        @foreach ($popularProducts as $product)
                            @php
                                $flatProduct = $product->product_flats->first();
                                $urlKey = $flatProduct ? $flatProduct->url_key : $product->url_key;
                            @endphp
                            <div class="search-suggestion-item">
                                <a href="{{ route('shop.product_or_category.index', $urlKey) }}">
                                    <img src="{{ $product->base_image_url }}" alt="{{ $product->name }}">
                                    <span>{{ $product->name }}</span>
                                </a>
                            </div>
                        @endforeach
                        </div>
                    </div>
                    @endif
                    {{-- Popular Categories --}}
                    @if (count($popularCategories) > 0)
                    <div class="search-suggestion-section">
                        <div class="search-suggestion-title">
                            <i class="fa-solid fa-fire"></i> @lang('suggestion::app.shop.search-suggestion.popular-categories')
                        </div>
                        <div id="popular-categories-list" class="search-suggestion-items">
                        
                        @foreach ($popularCategories as $category)
                            @php
                                $urlPath = $category->url_path;
                            @endphp
                            <div class="search-suggestion-item">
                                <a href="{{ route('shop.product_or_category.index', $urlPath) }}">
                                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}">
                                    <span>{{ $category->name }}</span>
                                </a>
                            </div>
                        @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                {{-- Live Search View --}}
                <div id="search-live-results" class="hidden">
                    <div class="search-suggestion-title">
                        <i class="fa-solid fa-magnifying-glass"></i> @lang('suggestion::app.shop.search-suggestion.search-results')
                    </div>
                    <div id="search-live-results-list" class="search-suggestion-items">
                        {{-- JS will populate this --}}
                    </div>
                </div>
            </div>
        </div>

        @pushOnce('scripts')
            <script>
                (function() {
                    const config = {
                        minSearchTerms: {{ $minSearchTerms }},
                        searchUrl: "{{ route('search_suggestion.search.index') }}",
                        trans: {
                            searching: "{{ trans('suggestion::app.shop.search-suggestion.searching') }}",
                            noResults: "{{ trans('suggestion::app.shop.search-suggestion.no-results') }}",
                        }
                    };

                    const searchInput = document.getElementById('search-input-suggestion');
                    const searchForm = document.getElementById('search-form-suggestion');
                    const popup = document.getElementById('search-suggestion-popup');
                    const defaultView = document.getElementById('search-suggestion-default-view');
                    const liveResults = document.getElementById('search-live-results');
                    const liveResultsList = document.getElementById('search-live-results-list');
                    const historySection = document.getElementById('search-history-section');
                    const historyList = document.getElementById('search-history-list');
                    let searchTimeout = null;

                    // Initialize History
                    renderHistory();

                    // Event Listeners
                    searchInput.addEventListener('focus', () => {
                        showPopup();
                    });

                    searchInput.addEventListener('input', () => {
                        const term = searchInput.value.trim();
                        
                        if (term.length > 0 && term.length >= config.minSearchTerms) {
                            liveResults.classList.remove('hidden');
                            defaultView.classList.add('hidden');
                            
                            clearTimeout(searchTimeout);
                            searchTimeout = setTimeout(() => {
                                performSearch(term);
                            }, 300);
                        } else {
                            defaultView.classList.remove('hidden');
                            liveResults.classList.add('hidden');
                            liveResultsList.innerHTML = '';
                        }
                    });

                    document.addEventListener('click', (e) => {
                        if (!searchInput.contains(e.target) && !popup.contains(e.target)) {
                            popup.classList.add('hidden');
                        }
                    });

                    searchForm.addEventListener('submit', (e) => {
                        const term = searchInput.value.trim();
                        if (term) {
                            saveHistory(term);
                        }
                    });

                    // Functions
                    function showPopup() {
                        popup.classList.remove('hidden');
                        const term = searchInput.value.trim();
                        if (term.length > 0) {
                            defaultView.classList.add('hidden');
                            liveResults.classList.remove('hidden');
                            if (term.length >= config.minSearchTerms) {
                                performSearch(term);
                            }
                        } else {
                            defaultView.classList.remove('hidden');
                            liveResults.classList.add('hidden');
                        }
                    }

                    function performSearch(term) {
                        liveResultsList.innerHTML = `<div class="p-4 text-center text-gray-500">${config.trans.searching}</div>`;
                        
                        fetch(`${config.searchUrl}?term=${encodeURIComponent(term)}`)
                            .then(response => response.json())
                            .then(response => {
                                console.log(response);
                                renderSearchResults(term, response.data);
                            })
                            .catch(err => {
                                console.error(err);
                                liveResultsList.innerHTML = `<div class="p-4 text-center text-red-500">Error</div>`;
                            });
                    }

                    function renderSearchResults(term, results) {
                        if (results.length === 0) {
                            liveResultsList.innerHTML = `<div class="p-4 text-center text-gray-500">${config.trans.noResults}</div>`;
                            return;
                        }

                        let html = '';
                        results.slice(0, 10).forEach(product => {
                            const imgUrl = product.images && product.images.length > 0 ? product.images[0].url : '';
                            html += `
                            <div class="search-suggestion-item">
                                <a href="${product.url_key}">
                                    <img src="${imgUrl}" alt="${product.name}">
                                    <span>${product.name}</span>
                                </a>
                            </div>
                            `;
                        });
                        html += '</div>';
                        liveResultsList.innerHTML = html;
                    }

                    function highlightTerm(text, term) {
                        const regex = new RegExp(`(${term})`, 'gi');
                        return text.replace(regex, '<span class="font-bold text-black">$1</span>');
                    }

                    function renderHistory() {
                        const history = JSON.parse(localStorage.getItem('search_history') || '[]');
                        if (history.length > 0) {
                            historySection.classList.remove('hidden');
                            historyList.innerHTML = history.map(term => `
                                <div class="search-history-item">
                                    <span class="cursor-pointer text-gray-700 hover:text-gray-900" onclick="fillSearch('${term.replace(/'/g, "\\'")}')">${term}</span>
                                    <i class="fa-solid fa-xmark text-xs cursor-pointer" onclick="removeHistoryItem('${term.replace(/'/g, "\\'")}')"></i>
                                </div>
                            `).join('');
                        } else {
                            historySection.classList.add('hidden');
                        }
                    }

                    window.fillSearch = function(term) {
                        searchInput.value = term;
                        searchForm.submit();
                    };

                    window.saveHistory = function(term) {
                        if (!term) return;
                        let history = JSON.parse(localStorage.getItem('search_history') || '[]');
                        history = history.filter(h => h !== term);
                        history.unshift(term);
                        localStorage.setItem('search_history', JSON.stringify(history.slice(0, 10)));
                    };

                    window.removeHistoryItem = function(term) {
                        let history = JSON.parse(localStorage.getItem('search_history') || '[]');
                        history = history.filter(h => h !== term);
                        localStorage.setItem('search_history', JSON.stringify(history));
                        renderHistory();
                    };
                })();
            </script>
        @endPushOnce
    @endif
</div>
