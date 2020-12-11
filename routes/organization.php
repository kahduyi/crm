<?php


Route::group(['prefix' => '/test'], function () {
    return view('organization.test.alerts');
});

Route::group(['prefix' => '/', 'namespace'=> 'Organization'], function () {

    Route::get('register', 'EmployeeController@showRegistrationForm')->name('organization.auth.show.register');
    Route::post('register', 'EmployeeController@register')->name('organization.auth.register');
    Route::get('login', 'EmployeeController@showLoginForm')->name('organization.auth.show.login');
    Route::post('login', 'EmployeeController@login')->name('organization.auth.login');
    Route::post('logout', 'EmployeeController@logout')->middleware('auth.employee:employee')->name('organization.auth.logout');
    Route::get('', 'EmployeeController@index')->middleware('auth.employee:employee')->name('organization.index');
    Route::get('test', function () {
        return view('organization.test.alerts');
    });
});
