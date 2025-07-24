<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class VirtualUser  extends Authenticatable
{
    protected $fillable = ['domain_id', 'email', 'password'];

    public function domain()
    {
        return $this->belongsTo(VirtualDomain::class, 'domain_id');
    }
}
