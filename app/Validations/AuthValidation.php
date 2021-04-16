<?php

namespace App\Validations;

use Illuminate\Support\Facades\Validator;

class AuthValidation
{
    /**
	 * Validate inputs for registration
	 *
	 * @param  array  $data
	 * @return bool
	 */
	public function  register(array $data)
	{
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'min:3', 'max:255'],
            'last_name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'unique:users', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'phone' => ['nullable', 'regex:/^([0-9\s\+\(\)]*)$/', 'min:11', 'max:20'],
        ]);
	}

    /**
     * Validate inputs for login
     *
     * @param  array  $data
     * @return bool
     */
    public function  login(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);
    }
}