<?php

namespace KBox;

use Illuminate\Database\Eloquent\Model;

use Oneofftech\Identities\Facades\Identity as IdentityFacade;

class Identity extends Model
{

    /**
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'registration' => 'bool',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'provider_id',
        'provider',
        'token',
        'refresh_token',
        'expires_at',
        'registration',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'provider_id',
        'token',
        'refresh_token',
        'expires_at',
    ];
    
    /**
     *
     */
    public function user()
    {
        return $this->belongsTo(IdentityFacade::userModel());
    }
}
