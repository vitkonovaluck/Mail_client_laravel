<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    //
    use HasFactory;

    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
