{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.before') !!}

<div class="flex min-h-[78px] w-full justify-between border border-b border-l-0 border-r-0 border-t-0 px-[60px] max-1180:px-8">
    <!--
        This section will provide categories for the first, second, and third levels. If
        additional levels are required, users can customize them according to their needs.
    -->
    <!-- Left Nagivation Section -->
    <div class="flex items-center gap-x-10 max-[1180px]:gap-x-5">
        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.before') !!}

        <a
            href="{{ route('shop.home.index') }}"
            aria-label="@lang('shop::app.components.layouts.header.bagisto')"
        >
            <img
                src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                width="131"
                height="29"
                alt="{{ config('app.name') }}"
            >
        </a>

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.after') !!}

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.category.before') !!}

        <v-desktop-category>
            <div class="flex items-center gap-5">
                <span
                    class="shimmer h-6 w-20 rounded"
                    role="presentation"
                ></span>
                <span
                    class="shimmer h-6 w-20 rounded"
                    role="presentation"
                ></span>
                <span
                    class="shimmer h-6 w-20 rounded"
                    role="presentation"
                ></span>
            </div>
        </v-desktop-category>

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.category.after') !!}
    </div>

    <!-- Right Nagivation Section -->
    <div class="flex items-center gap-x-9 max-[1100px]:gap-x-6 max-lg:gap-x-8">

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.search_bar.before') !!}

        <!-- Search Bar Container -->
        <div class="relative w-full">
            @if (core()->getConfigData('suggestion.suggestion.general.status'))
                <v-suggestion-searchbar></v-suggestion-searchbar>
            @else
                <form
                    action="{{ route('shop.search.index') }}"
                    class="flex max-w-[445px] items-center"
                    role="search"
                >
                    <label
                        for="organic-search"
                        class="sr-only"
                    >
                        @lang('shop::app.components.layouts.header.search')
                    </label>

                    <div class="icon-search pointer-events-none absolute top-2.5 flex items-center text-xl ltr:left-3 rtl:right-3"></div>

                    <input
                        type="text"
                        name="query"
                        value="{{ request('query') }}"
                        class="block w-full rounded-lg border border-transparent bg-[#F5F5F5] px-11 py-3 text-xs font-medium text-gray-900 transition-all hover:border-gray-400 focus:border-gray-400"
                        placeholder="@lang('shop::app.components.layouts.header.search-text')"
                        aria-label="@lang('shop::app.components.layouts.header.search-text')"
                        aria-required="true"
                        required
                    >

                    <button type="submit" class="hidden" aria-label="Submit"></button>

                    @if (core()->getConfigData('general.content.shop.image_search'))
                        @include('shop::search.images.index')
                    @endif
                </form>
            @endif
        </div>

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.search_bar.after') !!}

        <!-- Right Navigation Links -->
        <div class="mt-1.5 flex gap-x-8 max-[1100px]:gap-x-6 max-lg:gap-x-8">

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.compare.before') !!}

            <!-- Compare -->
            @if(core()->getConfigData('general.content.shop.compare_option'))
                <a
                    href="{{ route('shop.compare.index') }}"
                    aria-label="@lang('shop::app.components.layouts.header.compare')"
                >
                    <span
                        class="icon-compare inline-block cursor-pointer text-2xl"
                        role="presentation"
                    ></span>
                </a>
            @endif

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.compare.after') !!}

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.mini_cart.before') !!}

            <!-- Mini cart -->
            @include('shop::checkout.cart.mini-cart')

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.mini_cart.after') !!}

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile.before') !!}

            <!-- user profile -->
            <x-shop::dropdown position="bottom-{{ core()->getCurrentLocale()->direction === 'ltr' ? 'right' : 'left' }}">
                <x-slot:toggle>
                    <span
                        class="icon-users inline-block cursor-pointer text-2xl"
                        role="button"
                        aria-label="@lang('shop::app.components.layouts.header.profile')"
                        tabindex="0"
                    ></span>
                </x-slot>

                <!-- Guest Dropdown -->
                @guest('customer')
                    <x-slot:content>
                        <div class="grid gap-2.5">
                            <p class="font-dmserif text-xl">
                                @lang('shop::app.components.layouts.header.welcome-guest')
                            </p>

                            <p class="text-sm">
                                @lang('shop::app.components.layouts.header.dropdown-text')
                            </p>
                        </div>

                        <p class="py-2px mt-3 w-full border border-[#E9E9E9]"></p>

                        <div class="mt-6 flex gap-4">
                            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.sign_in_button.before') !!}

                            <a
                                href="{{ route('shop.customer.session.create') }}"
                                class="primary-button m-0 mx-auto block w-max rounded-2xl px-7 text-center text-base ltr:ml-0 rtl:mr-0"
                            >
                                @lang('shop::app.components.layouts.header.sign-in')
                            </a>

                            <a
                                href="{{ route('shop.customers.register.index') }}"
                                class="secondary-button m-0 mx-auto block w-max rounded-2xl border-2 px-7 text-center text-base ltr:ml-0 rtl:mr-0"
                            >
                                @lang('shop::app.components.layouts.header.sign-up')
                            </a>

                            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.sign_up__button.after') !!}
                        </div>
                    </x-slot>
                @endguest

                <!-- Customers Dropdown -->
                @auth('customer')
                    <x-slot:content class="!p-0">
                        <div class="grid gap-2.5 p-5 pb-0">
                            <p class="font-dmserif text-xl">
                                @lang('shop::app.components.layouts.header.welcome')’
                                {{ auth()->guard('customer')->user()->first_name }}
                            </p>

                            <p class="text-sm">
                                @lang('shop::app.components.layouts.header.dropdown-text')
                            </p>
                        </div>

                        <p class="py-2px mt-3 w-full border border-[#E9E9E9]"></p>

                        <div class="mt-2.5 grid gap-1 pb-2.5">
                            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile_dropdown.links.before') !!}

                            <a
                                class="cursor-pointer px-5 py-2 text-base hover:bg-gray-100"
                                href="{{ route('shop.customers.account.profile.index') }}"
                            >
                                @lang('shop::app.components.layouts.header.profile')
                            </a>

                            <a
                                class="cursor-pointer px-5 py-2 text-base hover:bg-gray-100"
                                href="{{ route('shop.customers.account.orders.index') }}"
                            >
                                @lang('shop::app.components.layouts.header.orders')
                            </a>

                            @if (core()->getConfigData('general.content.shop.wishlist_option'))
                                <a
                                    class="cursor-pointer px-5 py-2 text-base hover:bg-gray-100"
                                    href="{{ route('shop.customers.account.wishlist.index') }}"
                                >
                                    @lang('shop::app.components.layouts.header.wishlist')
                                </a>
                            @endif

                            <!--Customers logout-->
                            @auth('customer')
                                <x-shop::form
                                    method="DELETE"
                                    action="{{ route('shop.customer.session.destroy') }}"
                                    id="customerLogout"
                                />

                                <a
                                    class="cursor-pointer px-5 py-2 text-base hover:bg-gray-100"
                                    href="{{ route('shop.customer.session.destroy') }}"
                                    onclick="event.preventDefault(); document.getElementById('customerLogout').submit();"
                                >
                                    @lang('shop::app.components.layouts.header.logout')
                                </a>
                            @endauth

                            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile_dropdown.links.after') !!}
                        </div>
                    </x-slot>
                @endauth
            </x-shop::dropdown>

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile.after') !!}
        </div>
    </div>
</div>

@pushOnce('scripts')
    <script type="text/x-template" id="v-desktop-category-template">
        <div
            class="flex items-center gap-5"
            v-if="isLoading"
        >
            <span
                class="shimmer h-6 w-20 rounded"
                role="presentation"
            ></span>
            <span
                class="shimmer h-6 w-20 rounded"
                role="presentation"
            ></span>
            <span
                class="shimmer h-6 w-20 rounded"
                role="presentation"
            ></span>
        </div>

        <div
            class="flex items-center"
            v-else
        >
            <div
                class="group relative flex h-[77px] items-center border-b-[4px] border-transparent hover:border-b-[4px] hover:border-navyBlue"
                v-for="category in categories"
            >
                <span>
                    <a
                        :href="category.url"
                        class="inline-block px-5 uppercase"
                        v-text="category.name"
                    >
                    </a>
                </span>

                <div
                    class="pointer-events-none absolute top-[78px] z-[1] max-h-[580px] w-max max-w-[1260px] translate-y-1 overflow-auto overflow-x-auto border border-b-0 border-l-0 border-r-0 border-t border-[#F3F3F3] bg-white p-9 opacity-0 shadow-[0_6px_6px_1px_rgba(0,0,0,.3)] transition duration-300 ease-out group-hover:pointer-events-auto group-hover:translate-y-0 group-hover:opacity-100 group-hover:duration-200 group-hover:ease-in ltr:-left-9 rtl:-right-9"
                    v-if="category.children.length"
                >
                    <div class="aigns flex justify-between gap-x-[70px]">
                        <div
                            class="grid w-full min-w-max max-w-[150px] flex-auto grid-cols-[1fr] content-start gap-5"
                            v-for="pairCategoryChildren in pairCategoryChildren(category)"
                        >
                            <template v-for="secondLevelCategory in pairCategoryChildren">
                                <p class="font-medium text-navyBlue">
                                    <a
                                        :href="secondLevelCategory.url"
                                        v-text="secondLevelCategory.name"
                                    >
                                    </a>
                                </p>

                                <ul
                                    class="grid grid-cols-[1fr] gap-3"
                                    v-if="secondLevelCategory.children.length"
                                >
                                    <li
                                        class="text-sm font-medium text-[#6E6E6E]"
                                        v-for="thirdLevelCategory in secondLevelCategory.children"
                                    >
                                        <a
                                            :href="thirdLevelCategory.url"
                                            v-text="thirdLevelCategory.name"
                                        >
                                        </a>
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-desktop-category', {
            template: '#v-desktop-category-template',

            data() {
                return  {
                    isLoading: true,

                    categories: [],
                }
            },

            mounted() {
                this.get();
            },

            methods: {
                get() {
                    this.$axios.get("{{ route('shop.api.categories.tree') }}")
                        .then(response => {
                            this.isLoading = false;

                            this.categories = response.data.data;
                        }).catch(error => {
                            console.log(error);
                        });
                },

                pairCategoryChildren(category) {
                    return category.children.reduce((result, value, index, array) => {
                        if (index % 2 === 0) {
                            result.push(array.slice(index, index + 2));
                        }

                        return result;
                    }, []);
                }
            },
        });
    </script>
@endPushOnce

@if (core()->getConfigData('suggestion.suggestion.general.status'))
    @pushOnce('scripts')
    <script type="text/x-template" id="v-suggestion-searchbar-template">
        <div>
            <div class="relative w-full">
                <div class="flex max-w-[445px] items-center">
                    <form
                        action="{{ route('shop.search.index') }}"
                        class="flex max-w-[445px] items-center"
                        role="search"
                        id="search-form"
                    >
                        <label
                            for="organic-search"
                            class="sr-only"
                        >
                            @lang('shop::app.components.layouts.header.search')
                        </label>

                        <div class="icon-search pointer-events-none absolute top-2.5 flex items-center text-xl ltr:left-3 rtl:right-3">
                        </div>

                        <input
                            type="text"
                            name="query"
                            value="{{ request('query') }}"
                            class="block w-full rounded-lg border border-transparent bg-[#F5F5F5] px-11 py-3 text-xs font-medium text-gray-900 transition-all hover:border-gray-400 focus:border-gray-400"
                            placeholder="@lang('shop::app.components.layouts.header.search-text')"
                            aria-label="@lang('shop::app.components.layouts.header.search-text')"
                            aria-required="true"
                            v-model="term"
                            autocomplete="off"
                            @focus="showPopup = true"
                            @blur="setTimeout(() => showPopup = false, 300)"
                            @keyup="search()"
                            required
                        >

                        @if (core()->getConfigData('general.content.shop.image_search'))
                            @include('shop::search.images.index')
                        @endif

                        <button
                            class="btn"
                            type="button"
                            id="header-search-icon"
                            aria-label="Search"
                            @click="submitForm"
                        >
                        </button>
                    </form>
                </div>
            </div>

            <div
                class="absolute z-10 max-h-96 overflow-auto rounded border w-full bg-white shadow-lg" id="suggest"
                v-if="showPopup"
            >
                <!-- Search Results -->
                <div
                    :class="config.display === 'ar' ? 'ar' : ''"
                    v-if="term.length >= config.minSearchTerms && suggestsResults.length"
                >
                    <span
                        v-for="(result, index) in suggestsResults"
                    >
                        <div v-if="index < config.noOfTerms">
                            <a :href="result.url_key" @click="saveHistory(term)">
                                <div class="h-8 border bg-white p-2 text-sm hover:bg-gray-200 border-blue-100 hover:border-red-100">
                                    <p
                                        :class="config.display === 'ar' ? 'mr-1' : ''"
                                        class="overflow-hidden text-ellipsis whitespace-nowrap"
                                    >
                                        <span v-html="result.name"></span>

                                        @if (core()->getConfigData('suggestion.suggestion.general.display_categories_toggle'))
                                            <span v-if="result.categories.length">
                                                @lang('suggestion::app.shop.search-suggestion.in')
                                                <span
                                                    class="font-semibold"
                                                    v-for="(category, index) in result.categories"
                                                >
                                                    <template v-if="index < result.categories.length - 1">
                                                        @{{ category.name }},
                                                    </template>
                                                    <template v-else>
                                                        @{{ category.name }}
                                                    </template>
                                                </span>
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </a>
                        </div>
                    </span>

                    @if(core()->getConfigData('suggestion.suggestion.general.display_terms_toggle'))
                        <a :href="'search?query=' + term + '&sort=price-desc&limit=12&mode=grid'" @click="saveHistory(term)">
                            <div class="h-9 border border-blue-100 hover:border-red-100 bg-white p-2  hover:bg-gray-200">
                                <div v-if="config.display === 'ar'">
                                    @{{  term }}
                                    <span
                                        class="float-left"
                                    >
                                    @{{ suggestsResults.length }}
                                    </span>
                                </div>

                                <p v-else>
                                    @{{ term }}
                                    <span
                                        class="float-right"
                                        :class="config.display === 'ar' ? 'ml-1' : 'mr-1'"
                                    >
                                        @{{ suggestsResults.length }}
                                    </span>
                                </p>
                            </div>
                        </a>
                    @endif

                    @if(core()->getConfigData('suggestion.suggestion.general.display_product_toggle'))
                        <div class="h-9 border bg-blue-700 p-2 text-center font-bold text-blue-200">
                            <p>@lang('suggestion::app.shop.search-suggestion.popular-products')</p>
                        </div>
                        <a
                            :href="result.url_key"
                            v-for="(result, index) in productResults"
                            @click="saveHistory(term)"
                        >
                            <div class="flex w-full border bg-white hover:bg-gray-200 border-blue-100 hover:border-red-100">
                                <div class="w-1/4">
                                    <img
                                        class="max-h-20 min-h-20 min-w-20 max-w-20 p-2 rounded-full"
                                        v-if="result.images.length"
                                        :src="result.images[0].url"
                                    />
                                </div>
                                <div class="w-3/4 p-1">
                                    <div
                                        class="m-4 overflow-hidden text-ellipsis whitespace-nowrap"
                                        :class="config.display === 'ar' ? 'mr-2' : ''"
                                    >
                                        <span v-html="result.name"></span>
                                        <br>
                                        <div
                                            class="product-price gap-3 flex"
                                            v-html="result.price_html"
                                        >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a
                            href="javascript:void(0)"
                            class="show-more-btn"
                            v-if="showMoreButton"
                            @click="loadMoreResults"
                        >
                            @lang('suggestion::app.shop.search-suggestion.text-more')
                        </a>
                    @endif
                </div>

                <!-- History & Popular Products (When search term is short/empty) -->
                <div v-else-if="term.length < config.minSearchTerms">
                    <!-- History -->
                    <div v-if="searchHistory.length" class="p-4 border-b">
                        <p class="font-bold text-gray-500 text-xs mb-2 uppercase">Search History</p>
                        <div class="flex flex-wrap gap-2">
                            <span 
                                v-for="history in searchHistory" 
                                class="cursor-pointer rounded bg-gray-100 px-3 py-1 text-sm hover:bg-gray-200 transition"
                                @click="setTerm(history)"
                            >
                                @{{ history }}
                            </span>
                        </div>
                    </div>

                    <!-- Popular Products -->
                    <div v-if="popularProducts.length">
                        <div class="h-9 bg-blue-700 p-2 text-center font-bold text-blue-200">
                            <p>@lang('suggestion::app.shop.search-suggestion.popular-products')</p>
                        </div>
                        <a
                            :href="product.url_key"
                            v-for="product in popularProducts"
                        >
                            <div class="flex w-full border bg-white hover:bg-gray-200 border-blue-100 hover:border-red-100">
                                <div class="w-1/4">
                                    <img
                                        class="max-h-20 min-h-20 min-w-20 max-w-20 p-2 rounded-full"
                                        v-if="product.images.length"
                                        :src="product.images[0].url"
                                    />
                                </div>
                                <div class="w-3/4 p-1">
                                    <div class="m-4 overflow-hidden text-ellipsis whitespace-nowrap">
                                        <span v-html="product.name"></span>
                                        <br>
                                        <div class="product-price gap-3 flex" v-html="product.price_html"></div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div
                    class="h-10 border bg-white p-2"
                    :class="config.display === 'ar' ? 'ar' : ''"
                    v-if="isSearching"
                >
                    <p>@lang('suggestion::app.shop.search-suggestion.searching')</p>
                </div>
                
                <div
                    class="h-10 border bg-white p-2"
                    :class="config.display === 'ar' ? 'ar' : ''"
                    v-if="! isSearching && ! suggestsResults.length && term.length >= config.minSearchTerms"
                >
                    <p>@lang('suggestion::app.shop.search-suggestion.no-results')</p>
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-suggestion-searchbar', {
            template: '#v-suggestion-searchbar-template',

            data() {
                return {
                    term: '',

                    category: '',

                    isSearching: false,
                    
                    showPopup: false,

                    productResults: [],

                    suggestsResults: [],
                    
                    searchHistory: [],
                    
                    popularProducts: [],

                    highlightedResults: [],

                    visibleProductsCount: 10,
                    
                    defaultKeyword: "{{ core()->getConfigData('suggestion.suggestion.general.default_search_keyword') }}",

                    config: {
                        displayProductToggle: "{{ core()->getConfigData('suggestion.suggestion.general.display_product_toggle') }}",

                        noOfTerms: "{{ core()->getConfigData('suggestion.suggestion.general.show_products') }}",

                        displayTermsToggle: "{{ core()->getConfigData('suggestion.suggestion.general.display_terms_toggle') }}",

                        displayCategory: "{{ core()->getConfigData('suggestion.suggestion.general.display_categories_toggle') }}",

                        minSearchTerms: "{{ core()->getConfigData('suggestion.suggestion.general.min_search_terms') }}",

                        display: "{{ core()->getCurrentLocale()->code }}"
                    },
                };
            },

            mounted() {
                this.loadHistory();
                this.getPopularProducts();
                
                if (this.defaultKeyword && !this.term && !this.getUrlParam('query')) {
                    this.term = this.defaultKeyword;
                }
            },

            computed: {
                showMoreButton() {
                    return this.visibleProductsCount < this.suggestsResults.length;
                }
            },

            methods: {
                getUrlParam(name) {
                    const urlParams = new URLSearchParams(window.location.search);
                    return urlParams.get(name);
                },

                loadHistory() {
                    let history = localStorage.getItem('search_history');
                    if (history) {
                        try {
                            this.searchHistory = JSON.parse(history);
                        } catch (e) {
                            this.searchHistory = [];
                        }
                    }
                },
                
                saveHistory(term) {
                    if (!term) return;
                    let history = this.searchHistory;
                    // Remove if exists to push to top
                    history = history.filter(h => h !== term);
                    history.unshift(term);
                    if (history.length > 10) history = history.slice(0, 10);
                    
                    this.searchHistory = history;
                    localStorage.setItem('search_history', JSON.stringify(history));
                },
                
                getPopularProducts() {
                    this.$axios.get("{{ route('search_suggestion.popular.index') }}")
                        .then(response => {
                            this.popularProducts = response.data;
                        })
                        .catch(error => {
                            console.error("Error fetching popular products:", error);
                        });
                },
                
                setTerm(term) {
                    this.term = term;
                    this.saveHistory(term);
                    // Use a timeout to ensure term is updated before submit
                    this.$nextTick(() => {
                        this.submitForm();
                    });
                },

                search() {
                    if (this.term.length >= this.config.minSearchTerms) {
                        this.isSearching = true;

                        this.$axios.get("{{ route('search_suggestion.search.index') }}", {
                            params: { term: this.term, category: this.category }
                        })
                            .then (response => {
                                this.handleResponse(response.data);
                            })
                            .catch (error => {
                                console.error("Error:", error);
                            })
                    } else {
                        this.suggestsResults = [];
                    }
                },

                handleResponse(data) {
                    const escapeHtml = (unsafe) => {
                        return unsafe
                            .replace(/&/g, "&amp;")
                            .replace(/</g, "&lt;")
                            .replace(/>/g, "&gt;")
                            .replace(/"/g, "&quot;")
                            .replace(/'/g, "&#039;");
                    };

                    const searchTerm = this.term.toLowerCase();

                    const searchTermReversed = searchTerm.split('').reverse().join('');

                    const results = data.data;

                    const formattedResults = results.map(result => {
                        const originalText = result.name.toLowerCase();

                        const index1 = originalText.indexOf(searchTerm);

                        const index2 = originalText.indexOf(searchTermReversed);

                        let formattedName = escapeHtml(result.name);

                        if (index1 !== -1 || index2 !== -1) {
                            const startIndex = index1 !== -1 ? index1 : index2;

                            const foundTerm = index1 !== -1 ? searchTerm : searchTermReversed;

                            const escapedName = escapeHtml(result.name);

                            formattedName = `${escapedName.slice(0, startIndex)}<span class="font-semibold">${escapedName.slice(startIndex, startIndex + foundTerm.length)}</span>${escapedName.slice(startIndex + foundTerm.length)}`;
                        }

                        return { ...result, name: formattedName };
                    });

                    this.suggestsResults = formattedResults;

                    this.isSearching = false;
                },

                loadMoreResults() {
                    this.visibleProductsCount += 10;

                    this.updateDisplayedResults();
                },

                updateDisplayedResults() {
                    this.productResults = this.suggestsResults.slice(0, this.visibleProductsCount);
                },

                focusInput(event) {
                    $(event.target.parentElement.parentElement).find('input').focus();

                    this.search();
                },

                submitForm() {
                    if (this.term !== '') {
                        this.saveHistory(this.term);
                        
                        document.getElementsByName('query')[0].value = this.term;

                        document.getElementById('search-form').submit();
                    }
                }
            },

            watch: {
                suggestsResults: {
                    immediate: true,
                    handler() {
                        this.updateDisplayedResults();
                    }
                }
            }
        });
    </script>
    @endPushOnce
@endif

{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.after') !!}
