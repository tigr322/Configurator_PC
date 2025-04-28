<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Component extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'category_id',
        'name',
        'brand',
        'price',
        'image_url',
        'shop_url',
        'market_id',
        'compatibility_data',
        'characteristics',
    ];

   

    // Связи
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function markets()
    {
        return $this->belongsTo(Markets::class);
    }
    
    public function parsedData()
    {
        return $this->hasMany(ParsedData::class);
    }

    // 🛠 Методы для работы с compatibility_data
   
}