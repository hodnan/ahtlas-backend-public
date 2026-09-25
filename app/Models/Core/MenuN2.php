<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuN2 extends Model
{
    use SoftDeletes, HasFactory, TimezoneTrait;

    protected $connection = 'core';
    protected $table = 'menus_n2';

    protected $fillable = [
        'menu_n1_id',
        'title',
        'icon',
        'to',
        'component',
        'layout',
        'order',
        'active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    protected $hidden = [
        'uuid',
        'menu_n1_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
        'created_at',
        'updated_at',
    ];
}
