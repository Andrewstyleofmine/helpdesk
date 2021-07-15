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


Route::get('/login', 'UsersController@showLogin');
Route::post('/login', 'UsersController@login');
Route::get('/logout', 'UsersController@logout');

Route::get('/profile/{id}', 'UsersController@profile');

Route::get('/sign-up', 'UsersController@showSignUp');
Route::post('/sign-up', 'UsersController@signUp');

Route::get('/edit-user/{id}', 'UsersController@showEditUser');
Route::post('/edit-user/{id}', 'UsersController@editUser');

Route::get('/delete-user/{id}', 'UsersController@deleteUser');

Route::get('/users', 'UsersController@showUsers');

Route::get('/', 'UsersController@index');

Route::get('/requests', 'RequestsController@showRequests');
Route::post('/requests', 'RequestsController@searchRequests');

Route::get('/send-request', 'RequestsController@showSendRequest');
Route::post('/send-request', 'RequestsController@sendRequest');

Route::get('/edit-request/{id}', 'RequestsController@showEditRequest');
Route::post('/edit-request/{id}', 'RequestsController@EditRequest');

Route::get('/request/{id}', 'RequestsController@showRequest');

Route::get('/appoint/{id}', 'RequestsController@appoint');

Route::get('/admin-appoint/{id}', 'RequestsController@showAdminAppoint');
Route::post('/admin-appoint/{id}', 'RequestsController@adminAppoint');

Route::get('/ask-supplement/{id}', 'RequestsController@askSupplement');

Route::get('/close-request/{id}', 'RequestsController@closeRequest');

Route::get('/resend-request/{id}', 'RequestsController@resendRequest');

Route::get('/hide/{id}', 'RequestsController@hide');
Route::get('/show/{id}', 'RequestsController@show');
Route::get('/refuse/{id}', 'RequestsController@refuse');

Route::get('/delete-request/{id}', 'RequestsController@deleteRequest');

Route::get('/search/{request_id?}/{category?}/{priority?}/{status?}', 'RequestsController@search');

Route::get('/notifications', 'NotificationsController@showNotifications');

Route::get('/download/{id}', 'FilesController@getDownload');

Route::get('/categories', 'CategoriesController@showCategories');
Route::post('/categories', 'CategoriesController@addCategory');

Route::get('/delete-category/{id}', 'CategoriesController@deleteCategory');

Route::get('/reference', 'UsersController@reference');
Route::post('/reference', 'UsersController@contact');

Route::post('/request/{id}', 'RequestsController@sendComment');

Route::get('/answer-no/{id}', 'RequestsController@answerNo');
Route::get('/answer-yes/{id}', 'RequestsController@answerYes');
Route::get('/empty-answer/{id}', 'RequestsController@emptyAnswer');

Route::get('/rating', 'UsersController@rating');






