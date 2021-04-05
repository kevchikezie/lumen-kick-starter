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

	$router->post('auth/register', [
		'as' => 'register', 'uses' => 'AuthController@register'
	]);
	   
	$router->post('auth/login', [
		'as' => 'login', 'uses' => 'AuthController@login'
	]);

   	$router->group(['middleware' => ['auth']], function () use ($router) {
		$router->post('auth/profile', [
			'as' => 'profile', 'uses' => 'AuthController@profile'
		]); //TODO: Apply the verify middleware here

		$router->post('verify/email', [
			'as' => 'verify.email', 'uses' => 'VerificationController@verifyEmail'
		]);

		$router->get('verify/email/resend', [
			'as' => 'verify.email.resend', 'uses' => 'VerificationController@resendVerificiationEmail'
		]);
		
	});

});
