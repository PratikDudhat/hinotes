<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
    //return $request->user();
//});


Route::group(['middleware' => 'api', 'prefix' => 'auth', 'namespace' => 'Api'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@create');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('forgot-password', 'AuthController@forgotPassword');
    Route::post('resend-code', 'AuthController@doResendCode');
    Route::post('reset-password', 'AuthController@doResetPassword');
    Route::post('verify-email', 'AuthController@doVerifyEmail');
    Route::post('verify-token', 'AuthController@doVerifyToken');
    Route::post('sociallogin', 'AuthController@social_login');
});

Route::group(['middleware' => ['api'], 'namespace' => 'Api'], function () {
	Route::get('users/profile/{id}', 'UsersController@show');
	Route::post('users/user-profile', 'UsersController@updateUserProfile');
	Route::post('users/update_profile', 'UsersController@update_Profile');
    Route::post('users/user-token', 'UsersController@updateUserToken');
    Route::post('users/update-password', 'UsersController@updatePassword');
	Route::post('users/change-password', 'UsersController@changepassword');
	Route::get('category', 'CategoryController@index');
	Route::get('tags', 'TagController@index');
	Route::get('sponsers', 'SponsersController@index');
	//Route::get('updatetag', 'ChallangesController@updatetag');
	
	Route::get('challange', 'ChallangesController@index');
	Route::post('challange/create', 'ChallangesController@createChallange');
	Route::put('challange/update/{id}', 'ChallangesController@updateChallange');
	Route::post('challange/join', 'ChallangesController@joinChallange');
	Route::post('challange/likes', 'ChallangesController@addlikechallange');
	Route::post('follow', 'FollowController@follows');
	Route::post('challange/votes', 'ChallangesController@votechallange');
	Route::get('challangebycategory', 'ChallangesController@challangebycategory');
	Route::get('load_more_tag_by_video', 'ChallangesController@load_more_tag_by_video');
	Route::post('discover_challange', 'ChallangesController@discover_challange');
	Route::post('tag_by_challange', 'ChallangesController@tag_by_challange');
	
	Route::get('search/{name}', 'FollowController@search');
    Route::post('following_list_by_user', 'FollowController@following_list_by_user');
    Route::post('follows_list_by_user', 'FollowController@follows_list_by_user');
	Route::post('suggested_users', 'FollowController@suggested_users');
	Route::get('search_suggested_users', 'FollowController@search_suggested_users');
	Route::post('verify_challenge', 'ChallangesController@verify_challenge');
	
	Route::post('get_conversation_id', 'MessageController@get_conversation_id');
	Route::post('get_message_conversation', 'MessageController@get_message_conversation');
	Route::get('search_conversation_list', 'MessageController@search_conversation_list');
	Route::delete('delete_message/{id}', 'MessageController@delete_message');
});