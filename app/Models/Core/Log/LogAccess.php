<?php

namespace App\Models\Core\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class LogAccess extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'core';

    protected $fillable = [
        'month_ref',
        'date_ref',
        'route_parameters',
        'method',
        'username',
        'route',
        'meta',
        'authorized',
    ];
    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'route_parameters' => 'array',
        ];
    }
}
