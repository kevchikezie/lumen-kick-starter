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
        $validator = $this->authValidation->register($request->all());

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        try {
            DB::beginTransaction();
            $user = $this->authService->registerUser($request->toArray());
            DB::commit();

            $this->otpService->send($user->uid, $user->email);

            $credentials = $request->only(['email', 'password']);
            $token = Auth::attempt($credentials);

            $data = [ 
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => Auth::factory()->getTTL() * 60
            ];

            return $this->successResponse($data, 'Registration successful', 201);

        } catch (\Exception $e) {
            \Log::error(['Error during registration: ' => $e]);
            return $this->errorResponse('Something went wrong!', 500);
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
        $validator = $this->authValidation->login($request->all());

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Invalid email or password!'
            ], 403);
        }

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
