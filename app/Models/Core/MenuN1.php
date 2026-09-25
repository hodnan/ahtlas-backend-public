<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuN1 extends Model
{
    use SoftDeletes, HasFactory, TimezoneTrait;

    protected $connection = 'core';
    protected $table = 'menus_n1';

    protected $fillable = [
        'module_id',
        'title',
        'icon',
        'to',
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
        'module_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function menuN2()
    {
        return $this->hasMany(MenuN2::class, 'menu_n1_id', 'uuid')
        ->orderBy('order')
        ;
    }

}
