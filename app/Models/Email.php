<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $fillable = [
        'message_id',
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

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

}
