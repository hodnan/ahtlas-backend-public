<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class SectorN1 extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'core';
    protected $table = 'sectors_n1';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'uf',
        'hc',
        'active',

    ];
    // protected function casts(): array {return [];}
    // public $appends = [];
}
