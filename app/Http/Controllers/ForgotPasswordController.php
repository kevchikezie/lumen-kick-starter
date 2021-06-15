<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OtpService;
use App\Services\ForgotPasswordService;

class ForgotPasswordController extends Controller
{
	/**
     * Class properties
     *
     * @var
     */
    private $otpService;
    private $forgotPasswordService;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->otpService = new OtpService;
        $this->forgotPasswordService = new ForgotPasswordService;
    }

	/**
     * Send OTP to user's email
     *
     * @param  Request  $request
     * @return Response
     */
    public function sendOtp(Request $request)
    {
    	$validator = \Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255'],
        ]);

    	if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $user = $this->forgotPasswordService->verifyUser($request->email);
        // Only send OTP if the supplied email exists in the database
        if ($user) {
        	$this->otpService->send($user->uid, $user->email);
        }

        return $this->successResponse('', 'If the email you entered is correct, an OTP has been sent to the email');
    }

    /**
     * Reset user's password
     *
     * @param  Request  $request
     * @return Response
     */
    public function reset(Request $request)
    {

    }
}
