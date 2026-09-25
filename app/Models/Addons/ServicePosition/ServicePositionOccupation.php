<?php

namespace App\Models\Addons\ServicePosition;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class ServicePositionOccupation extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'addons';
    // protected $table = '';
    // public $incrementing = false;

    protected $fillable = [
        'month_ref',
        'date_ref',
        'username',
        'hostname',
        'log',
        'meta',
    ];

    protected function casts(): array {return [
        'meta' => 'array',
    ];}
}