<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\OtpMail;
use Carbon\Carbon;
use App\User;

class OtpService
{
	/**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Generate OTP
     *
     * @param  int  $length
     * @return string 
     * 
     * @throws \Exception
     */
    protected function generate(int $length = 6)
    {
        $result = '';

        if ($length >=2 && $length <= 10) {
            for ($i = 0; $i < abs($length); $i++) {
                $result .= mt_rand(1, 9);
            }

            return $result;
        } else {
            throw new \Exception("OTP length must not exceed 10 characters and not less than 2 characters");
        }
    }

    /**
     * Send OTP to user
     * 
     * @param  App\User  $user
     * @return bool
     * 
     * @throws \Exception
     */
    public function send(User $user)
    {
        try {
            $otp = $user->otps()->create([
                'otp' => $this->generate(),
                'user_id' => $user->uid,
                'uid' => (string) Str::uuid(),
                'expires_at' => Carbon::now()->addMinutes(20),
            ]);

            Mail::to($user->email)->send(new OtpMail($otp));

            $otp->update(['sent_at' => Carbon::now()]);

        } catch (\Exception $e) {
            \Log::error($e);
        }        
    }

    /** Attempt to resend OTP to user
    * 
    * @param  App\User  $user
    * @return void
    * 
    * @throws \Exception
    */
   public function resend(User $user)
   {
       $this->send($user);
   }

   /**
    * Verify the OTP that was sent to the user and update the verified_at
    * 
    * @param  App\User  $user
    * @param  string  otp
    * @return array
    */
    public function verify(User $user, $otp)
    {
        $data = $user->otps()->where('user_id', $user->uid)
                             ->where('otp', $otp)
                             ->where('verified_at', NULL)
                             ->first();
        
        if (! $data) {
            return [
                'status' => 'error',
                'message' => 'Invalid OTP.',
            ];
        }

        if (Carbon::now()->gt($data->expires_at)) {
            return [
                'status' => 'error',
                'message' => 'OTP has expired. Try requesting for OTP again.',
            ];
        }

        $data->update(['verified_at'=> Carbon::now()]);

        return [
            'status' => 'success',
            'message' => 'Verified successfully.',
        ];
    }

}