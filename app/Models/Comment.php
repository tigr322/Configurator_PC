<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'configuration_id',
        'user_id',
        'body',
    ];

    public function configuration()
    {
        return $this->belongsTo(Configurations::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
