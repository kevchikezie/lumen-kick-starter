<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Services\OtpService;
use Carbon\Carbon;

class VerificationController extends Controller
{
    /**
     * Class properties
     *
     * @var
     */
    private $otpService;
    private $authService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->otpService = new OtpService;
        $this->authService = new AuthService;
    }

    /** 
     * Verify user's email using OTP
     * 
     * @param  Request  $request
     * @return Response
     */
    public function verifyEmail(Request $request)
    {
        $this->validate($request, [
            'otp' => ['required', 'string', 'min:2', 'max:10'],
        ]);

        try {
            $verified = $this->otpService->verify(Auth::user()->uid, $request->otp);

            if (isset($verified['status']) && $verified['status'] === 'error' ) {
                return response()->json([
                    'status' => $verified['status'],
                    'message' => $verified['message'],
                ], 400);
            }

            $this->authService->updateUser(
                Auth::user()->uid, 
                ['email_verified_at' => Carbon::now()]
            );

            return response()->json([
                'status' => $verified['status'],
                'message' => $verified['message'],
            ], 200);

        } catch (\Exception $e) {
            \Log::error($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!'
            ], 500);
        }
    }

    /**
     * Resend OTP request
     * 
     * @return Response
     */
    public function resendVerificiationEmail()
    {
        try {
            $user = Auth::user();

            $this->otpService->resend($user->uid, $user->email);

            return response()->json([
                'status' => 'success',
                'message' => 'Verification mail sent successfully. Check your email.',
            ], 201);

        } catch (\Exception $e) {
            \Log::error($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!'
            ], 500);
        }
    }
}
