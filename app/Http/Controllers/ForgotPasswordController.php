<?php

namespace App\Http\Controllers;

use App\Services\ForgotPasswordService;
use App\Services\OtpService;
use Illuminate\Http\Request;

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
            return response()->json([
                'status' =>  'error', 
                'message' =>  $validator->errors()->first()
            ], 400);
        }

        $user = $this->forgotPasswordService->verifyUser($request->email);
        // Only send OTP if email exists
        if ($user) {
        	$this->otpService->send($user->uid, $user->email);
        }

    	return response()->json([
            'status' => 'success',
            'message' => 'If the email you entered is correct, an OTP has been sent to the email',
        ], 200);
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
