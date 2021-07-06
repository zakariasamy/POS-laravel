<?php

Route::group([
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
    ], function(){


    Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.', 'middleware' => 'auth'], function () {

        Route::get('index', 'DashboardController@index')->name('welcome'); // The name is dashboard.index
        Route::resource('users', 'UserController')->except(['show']);
        Route::resource('categories', 'CategoryController')->except(['show']);
        Route::resource('products', 'ProductController')->except(['show']);
        Route::resource('clients', 'ClientController')->except(['show']);
    });

});
