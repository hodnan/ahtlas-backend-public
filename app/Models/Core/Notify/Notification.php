<?php

namespace App\Models\Core\Notify;

use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class Notification extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait;

    protected $connection = 'core';

    protected $fillable = [
        'type',
        'title',
        'notes',
        'route',
        'external_link',
        'img',
        'username',
        'sector_n1_id',
        'managers',
        'hierarchical_level',
        'staff',
        'type',
        'uf',
        'position_summary',
        'delivered',
        'received',
        'read',
        'start',
        'end',
        'status',
        'created_by',
        'updated_by',
    ];
    
}
