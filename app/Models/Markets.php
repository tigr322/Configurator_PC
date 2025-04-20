<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Markets extends Model
{
    use HasFactory;
    protected $fillable =['name'];

    public function componets()
    {
        return $this->hasMany(Component::class);
    }
}
