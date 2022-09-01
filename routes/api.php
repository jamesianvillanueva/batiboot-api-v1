<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// DECLARED MIDDLEWARE -> KERNEL.PHP
// MIDDLEWARE CLASS LOCATION -> Middleware FOLDERs
// API -> MIDDLEWARE -> [TRUE, FALSE] -> IF TRUE -> CONTROLLER -> IF FALSE -> RETURN ERR CODE

// LOGIN API
Route::post('account/login-api',  'LoginController@Login') -> middleware('validate-api-key') -> name('loginapi');

// REGISTER API
Route::post('account/register-api', 'RegisterController@Register') -> middleware('validate-api-key') -> name('registerapi');

// FORGOT PASSWORD EMAILER
Route::post('email/send-link-api', 'EmailerController@SendLink') ->  middleware('validate-api-key') -> name('emailerapi');

// CHANGE PASSWORD
Route::put('account/change-password', 'ForgotPasswordController@ForgotPassword') -> middleware('validate-api-key') -> name('changepasswordapi');

// GET VERIFICATION CODE
Route::post('account/get-verification-code', 'ForgotPasswordController@CheckEmailCode') -> middleware('validate-api-key') -> name('checkemailcodeapi');

// VALIDATE USER
Route::post('account/validate-user', 'ForgotPasswordController@ValidateUser') -> middleware('validate-api-key') -> name('validateuser');

// GET PROFILE
Route::get('account/my-profile', 'ProfileController@Profile') -> middleware(['validate-api-key', 'validate-access-token']) -> name('myprofile');

// UPDATE PROFILE 
Route::put('account/update-profile', 'ProfileController@UpdateProfile') -> middleware(['validate-api-key', 'validate-access-token']) -> name('updateprofile');

// USER MANAGEMENT

# GET USERS

Route::get('management/get-user-data', 'UserManagementController@User') -> middleware(['validate-api-key', 'validate-access-token']) -> name('getuserdata');
