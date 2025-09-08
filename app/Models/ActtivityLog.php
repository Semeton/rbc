<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActtivityLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'loggable_type',
        'loggable_id',
        'event',
        'properties',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the user that performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent loggable model.
     */
    public function loggable()
    {
        return $this->morphTo();
    }
}
