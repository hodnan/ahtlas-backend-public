<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class Info extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'core';
    protected $table = 'infos';
    // public $incrementing = false;

    protected $fillable = [
        'route',
        'infos',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'infos' => 'array',
        ];
    }
  
}
