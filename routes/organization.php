<?php


Route::group(['prefix' => '/test'], function () {
    return view('organization.test.alerts');
});

Route::group(['prefix' => '/', 'namespace'=> 'Organization'], function () {

    Route::get('register', 'EmployeeController@showRegistrationForm')->name('organization.auto.show.register');
    Route::post('register', 'EmployeeController@register')->name('organization.auto.register');
    Route::get('login', 'EmployeeController@showLoginForm')->name('organization.auto.show.login');
    Route::post('login', 'EmployeeController@login')->name('organization.auto.login');
    Route::get('', 'EmployeeController@index');
    Route::get('test', function () {
        return view('organization.test.alerts');
    });
});
