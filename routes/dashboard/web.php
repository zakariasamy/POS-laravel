<?php

Route::group([
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
    ], function(){


    Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.', 'middleware' => 'auth'], function () {

        Route::get('', 'DashboardController@index')->name('welcome'); // The name is dashboard.index
        Route::resource('users', 'UserController')->except(['show']);
        Route::resource('categories', 'CategoryController')->except(['show']);
        Route::resource('products', 'ProductController')->except(['show']);

        Route::resource('clients', 'ClientController')->except(['show']);
        Route::resource('clients.orders', 'Client\OrderController')->except(['show']);

        Route::resource('orders', 'OrderController')->except(['show']);
        Route::get('/orders/{order}/products', 'OrderController@products')->name('orders.products');
    });

});
