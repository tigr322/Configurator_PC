<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParsedData extends Model
{
    /** @use HasFactory<\Database\Factories\ParsedDataFactory> */
    use HasFactory;
    protected $fillable = ['component_id', 'source', 'price', 'availability'];
    public function component()
    {
        return $this->belongsTo(Component::class, 'component_id');
    }
}
