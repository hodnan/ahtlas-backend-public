<?php

namespace App\Models\Addons;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class Calendar extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'addons';

    protected $fillable = [
        'year_ref',
        'month_ref',
        'year',
        'month',
        'day',
        'date',
        'weekday',
        'business_day',
        'active',
        'passed',
    ];
    // protected function casts(): array {return [];}
    // public $appends = [];
}
