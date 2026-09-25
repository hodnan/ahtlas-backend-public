<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use SoftDeletes, HasFactory, TimezoneTrait;

    protected $connection = 'core';
    protected $table = 'modules';

    protected $fillable = [
        'uuid',
        'title',
        'icon',
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
        'icon',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function menuN1()
    {
        return $this->hasMany(MenuN1::class, 'module_id', 'uuid')->orderBy('order');
    }

}
