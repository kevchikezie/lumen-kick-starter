<?php

namespace App\Services;

use Illuminate\Support\Str;
use  App\User;

class AuthService
{
    /**
     * The default column used as a primary key for searching a table
     *
     * @var string
     */
    // protected $defaultAttribute = 'uid';

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
     * Register a new client to the app
     *
     * @param  array  $data
     * @return mixed
     */
    public function registerUser(array $data)
    {
        $data['uid'] = (string) Str::uuid();
        $data['password'] = app('hash')->make($data['password']);

    	return $this->user->create($data);
    }

    /**
     * Update a cleint data in the database
     *
     * @param  string $key
     * @param  array $data
     * @return mixed
     */
    public function updateUser(string $key, array $data)
    {
        return tap($this->user->findOrFail($key))->update($data);
    }

}