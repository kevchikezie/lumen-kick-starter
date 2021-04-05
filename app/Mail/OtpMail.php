<?php

namespace App\Mail;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use  App\Models\Otp;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The otp instance.
     *
     * @var Order
     */
    public $otp;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Otp $otp)
    {
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.otp');
    }
}
