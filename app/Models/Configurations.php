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
        return $this->belongsToMany(Component::class, 'configuration_components', 'configuration_id', 'component_id')->with('category');
    }

}   
