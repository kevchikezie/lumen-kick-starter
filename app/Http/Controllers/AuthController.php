<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Validations\AuthValidation;
use Illuminate\Support\Facades\DB;
use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Services\OtpService;

class AuthController extends Controller
{
    /**
     * Class properties
     *
     * @var
     */
    private $authValidation;
    private $authService;
    private $otpService;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() 
    {
        $this->authValidation = new AuthValidation;
        $this->authService = new AuthService;
        $this->otpService = new OtpService;
    }

    /**
     * Register a new client
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {        
        $validator = $this->authValidation->store($request->all());

        if ($validator->fails()) {
            return response()->json([
                'status' =>  'error', 
                'message' =>  $validator->errors()->first()
            ], 400);
        }

        try {
            DB::beginTransaction();
            $user = $this->authService->registerUser($request->toArray());
            DB::commit();

            $this->otpService->send($user);

            return response()->json([
                'status' => 'success',
                'message' => 'Registration successful',
                'data' => $user
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error during registration', $e);

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!'
            ], 500);
        }
    }

    /**
     * Authenticate a client via login and return a JWT
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Invalid email or password!'
            ], 403);
        }

        // TODO: Check if email has been verified (this should be a middleware)
        // TODO: Check if user has an is_active status (this should be a middleware)

        return response()->json([
            'status' => 'success',
            'message' => 'Authenticated successfully',
            'data' => [
                'user' => Auth::user(),
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => Auth::factory()->getTTL() * 60
            ]
        ], 200);
    }

    /**
     * Get the authenticated user.
     *
     * @return Response
     */
    public function profile()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Fetched user profile',
            'data' => Auth::user()
        ], 200);
    }

}
