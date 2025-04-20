<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketsUrls extends Model
{
    use HasFactory;

    protected $table = 'markets_urls';

    protected $fillable = [
        'category_id',
        'market_id',
        'url',
    ];

    // Связь с категорией
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Связь с маркетом
    public function market()
    {
        return $this->belongsTo(Markets::class);
    }
}