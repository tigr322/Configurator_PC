<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigurationComponent extends Model
{
    /** @use HasFactory<\Database\Factories\ConfigurationComponentFactory> */
    use HasFactory;
    protected $fillable = [
        'configuration_id',
        'component_id'
        
    ];
}
