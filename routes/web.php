<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Auth::routes(['register' => false]);

Route::get('/forgot', function () { return view('auth.passwords.email'); });
Route::post('reset_password', 'Auth\ForgotPasswordController@passwordRequest');

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => ['auth']], function() {
    Route::resource('home','HomeController');
    
    Route::resource('roles','RoleController');
    Route::post('rolefilter', 'RoleController@rolefilter')->name('rolefilter');

    Route::resource('permissions','PermissionController');
    Route::post('permissionsfilter', 'PermissionController@permissionsfilter')->name('permissionsfilter');

    Route::resource('users','UserController');
    Route::post('userfilter', 'UserController@userfilter')->name('userfilter');

    Route::resource('categories','CategoryController');
    Route::post('categoryfilter', 'CategoryController@categoryfilter')->name('categoryfilter');

    Route::resource('sponsers','SponsersController');
    Route::post('sponserfilter', 'SponsersController@sponserfilter')->name('sponserfilter');

    Route::resource('tags','TagController');
    Route::post('tagfilter', 'TagController@tagfilter')->name('tagfilter');

    Route::resource('challanges','ChallangesController');
    Route::post('challangesfilter', 'ChallangesController@challangesfilter')->name('challangesfilter');
});