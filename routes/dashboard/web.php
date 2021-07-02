<?php

Route::group([
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
    ], function(){


    Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.', 'middleware' => 'auth'], function () {

        Route::get('index', 'DashboardController@index')->name('welcome'); // The name is dashboard.index
        Route::resource('users', 'UserController')->except(['show']);
    });

});
