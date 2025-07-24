<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $fillable = [
        'virtual_user_id',
        'from',
        'to',
        'subject',
        'body',
        'received_at',
    ];

    public function virtualUser()
    {
        return $this->belongsTo(VirtualUser::class);
    }
}
