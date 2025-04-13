<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    /** @use HasFactory<\Database\Factories\ComponentFactory> */
    use HasFactory;
    protected $fillable = [
        'category_id',
        'name',
        'brand',
        'price',
        'image_url',
        'shop_url',
        'compatibility_data',
        'characteristics',
    ];

    // 🛠 Связь: Component принадлежит категории
        public function category()
{
    return $this->belongsTo(Category::class);
}

public function parsedData()
{
    return $this->hasMany(ParsedData::class);
}

}
