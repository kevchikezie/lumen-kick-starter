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
    private $otpService;
    private $authService;
    private $authValidation;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() 
    {
        $this->otpService = new OtpService;
        $this->authService = new AuthService;
        $this->authValidation = new AuthValidation;
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
            return $this->errorResponse('Invalid email or password!', 403);
        }

        $data = [
            'user' => Auth::user(),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ];

        return $this->successResponse($data, 'Authenticated successfully');
    }

    /**
     * Get the authenticated user.
     *
     * @return Response
     */
    public function profile()
    {
        return $this->successResponse(Auth::user(), 'Fetched loggd in user');
    }

}
