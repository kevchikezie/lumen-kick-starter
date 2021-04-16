<?php

namespace App\Services;

use  App\User;

class ForgotPasswordService
{
    /**
     * Class properties
     *
     * @var
     */
    private $user;

	/**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = new User;
    }

    /**
     * Verify the user via their email or phone
     *
     * @param  string  $data
     * @return mixed | bool
     */
    public function verifyUser($data)
    {
        if (filter_var($data, FILTER_VALIDATE_EMAIL)) {
            $user = $this->user->where('email', $data)->first();
        }

    	return $user ?? false;
    }

    public function verifyOtp()
    {
        //
    }

    /**
     * Reset the user's password
     *
     * @param  array $data
     * @return mixed
     */
    public function resetPassword(array $data)
    {
        return tap($this->user->findOrFail($key))->update($data);
    }

}