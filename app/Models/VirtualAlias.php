<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualAlias extends Model
{
    protected $fillable = ['source', 'destination'];
}
