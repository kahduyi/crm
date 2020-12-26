<?php


Route::get('test', function () {
    return view('organization.auth.verify');
});

Route::group(['prefix' => '/', 'namespace'=> 'Organization'], function () {
    Route::get('register', 'EmployeeController@showRegistrationForm')->name('organization.auth.show.register');
    Route::post('register', 'EmployeeController@register')->name('organization.auth.register');
    Route::get('verify/{mobile}', 'EmployeeController@verify')->middleware('verify.employee:employee')->name('organization.auth.verify');
    Route::post('resend-verify', 'EmployeeController@resendVerify')->middleware('verify.employee:employee')->name('organization.auth.resend.verify');
    Route::post('doVerify', 'EmployeeController@doVerify')->name('organization.auth.doVerify');
    Route::get('login', 'EmployeeController@showLoginForm')->name('organization.auth.show.login');
    Route::post('login', 'EmployeeController@login')->name('organization.auth.login');
    Route::post('logout', 'EmployeeController@logout')->middleware('auth.employee:employee')->name('organization.auth.logout');
    Route::get('', 'EmployeeController@index')->middleware('auth.employee:employee')->name('organization.index');
});
