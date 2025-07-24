<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualDomain extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(VirtualUser::class, 'domain_id');
    }
}
