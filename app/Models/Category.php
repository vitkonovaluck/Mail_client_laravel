<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    public function emails()
    {
        return $this->belongsToMany(Email::class);
    }

}
