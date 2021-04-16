<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\OtpMail;
use Carbon\Carbon;
use App\Models\Otp;

class OtpService
{
    /**
     * Class properties
     *
     * @var
     */
    private $otp;

	/**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->otp = new Otp;
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
     * Send OTP to an email or phone
     * 
     * @param  string  $senderId
     * @param  string|array  $destination
     * @return bool
     * 
     * @throws \Exception
     */
    public function send($senderId, $destination)
    {
        try {
            $otp = $this->otp->create([
                'otp' => $this->generate(),
                'sender_id' => $senderId,
                'uid' => (string) Str::uuid(),
                'expires_at' => Carbon::now()->addMinutes(20),
            ]);

            if (is_array($destination)) {
                if (isset($destination['email'])) {
                    if (filter_var($destination['email'], FILTER_VALIDATE_EMAIL)) {
                        Mail::to($destination)->send(new OtpMail($otp));
                    }
                }

                if (isset($destination['phone'])) {
                    if (is_numeric($destination['phone'])) {
                        // TODO: Send SMS to phone number
                    }
                }
            } else {
                if (filter_var($destination, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($destination)->send(new OtpMail($otp));
                }

                if (is_numeric($destination)) {
                    // TODO: Send SMS to phone number
                }
            }

            $otp->update(['sent_at' => Carbon::now()]);

        } catch (\Exception $e) {
            \Log::error('Error while sending OTP: '. $e);
        }        
    }

    /** 
     *Attempt to resend OTP
     * 
     * @param  string  $senderId
     * @param  string  $destination
     * @return void
     * 
     * @throws \Exception
     */
   public function resend($senderId, $destination)
   {
       $this->send($senderId, $destination);
   }

    /**
     * Verify the OTP that was sent to the sender and update the verified_at
     * 
     * @param  string  $senderId
     * @param  string  otp
     * @return array
     */
    public function verify($senderId, $otp)
    {
        $data = $this->otp->where('sender_id', $senderId)
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
