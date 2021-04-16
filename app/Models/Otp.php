<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'otp', 'sender_id', 'uid', 'expires_at', 'verified_at', 'sent_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'otp',
    ];

    // Table Relationships
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id', 'uid')->withDefault();
    }

}