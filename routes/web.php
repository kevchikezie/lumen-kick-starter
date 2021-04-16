<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/


$router->group(['prefix' => 'api/v1'], function () use ($router) {
	$router->get('/', function () use ($router) {
	    return response()->json([
	    	'status' => 'success',
	    	'message' => 'API is running',
	    	'version' => 1
	    ], 200);
	});

	$router->post('auth/register','AuthController@register');
	$router->post('auth/login', 'AuthController@login');
	$router->post('auth/password/forgot', "ForgotPasswordController@sendOtp");
	$router->post('auth/password/reset', "ForgotPasswordController@reset");

   	$router->group(['middleware' => ['auth']], function () use ($router) {
		$router->post('auth/profile', 'AuthController@profile'); //TODO: Apply the verify middleware here
		$router->post('verify/email', 'VerificationController@verifyEmail');
		$router->get('verify/email/resend', 'VerificationController@resendVerificiationEmail');
	});

});
