<?php

use Illuminate\Support\Facades\Route;
use Webkul\Suggestion\Http\Controllers\Shop\SuggestionController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {
    Route::controller(SuggestionController::class)->group(function () {
        Route::get('/ajax-search', 'search')->name('search_suggestion.search.index');
        Route::get('/ajax-popular', 'popular')->name('search_suggestion.popular.index');
    });
});
