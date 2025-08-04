<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigurationVote extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'configuration_id', 'is_like', 'is_best_build_vote'];

    public function configuration()
    {
        return $this->belongsTo(Configurations::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

