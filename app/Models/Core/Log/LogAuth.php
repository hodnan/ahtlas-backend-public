<?php

namespace App\Models\Core\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class LogAuth extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'core';

    protected $fillable = [
        'month_ref',
        'date_ref',
        'username',
        'meta',
        'authorized',
        'notes',
    ];
    protected function casts(): array
    {
        return ['meta' => 'array',];
    }
    // public $appends = [];
}
