<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompatibilityRule extends Model
{
    /** @use HasFactory<\Database\Factories\CompatibilityRuleFactory> */
    use HasFactory;
    protected $fillable = [
        'category1_id',
        'category2_id',
        'condition',
    ];

    protected $casts = [
        'condition' => 'array', // автоматически преобразует JSON в массив и обратно
    ];

    public function category1()
    {
        return $this->belongsTo(Category::class, 'category1_id');
    }

    public function category2()
    {
        return $this->belongsTo(Category::class, 'category2_id');
    }
}
