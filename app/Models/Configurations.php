<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
class Configurations extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'name', 'total_price'];

    public function components()
    {
        return $this->belongsToMany(Component::class, 'configuration_components', 
        'configuration_id', 'component_id')->with('category');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class, 'configuration_id');
    }
    public function votes()
{
    return $this->hasMany(ConfigurationVote::class, 'configuration_id');
}

// Подсчёт лайков
public function likes()
{
    return $this->votes()->where('is_like', true);
}

// Подсчёт дизлайков
public function dislikes()
{
    return $this->votes()->where('is_like', false);
}

// Подсчёт голосов за "лучшую сборку"
public function bestBuildVotes()
{
    return $this->votes()->where('is_best_build_vote', true);
}
}   
