<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;
    protected $fillable = ['name'];

    // 🔁 Категория имеет много компонентов
    public function components()
    {
        return $this->hasMany(Component::class);
    }
}
