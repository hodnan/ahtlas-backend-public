<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class RoutePermission extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'core';
    protected $table = 'route_permissions';
    // public $incrementing = false;

    protected $fillable = [
        'route',
        'username',
        'sector_n1_id',
        'manager_n5_id',
        'manager_n4_id',
        'manager_n3_id',
        'manager_n2_id',
        'hierarchical_level',
        'staff',
        'type',
        'uf',
        'position_summary',
        'expiration_at',
    ];

    protected function casts(): array
    {
        return [
            'username' => 'array',
            'sector_n1_id'=> 'array',
            'manager_n5_id'=> 'array',
            'manager_n4_id'=> 'array',
            'manager_n3_id'=> 'array',
            'manager_n2_id'=> 'array',
            'hierarchical_level'=> 'array',
            'uf'=> 'array',
            'position_summary'=> 'array',
        ];
    }
    // protected function casts(): array {return [];}
    // public $appends = [];
}
