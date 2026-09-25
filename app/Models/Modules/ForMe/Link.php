<?php

namespace App\Models\Modules\ForMe;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class Link extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'modules';
    protected $table = 'links';

    protected $fillable = [
        'title',
        'url',
        'created_by',
        'updated_by',

    ];
    // protected function casts(): array {return [];}
    // public $appends = [];

    protected $hidden = [
        "created_by",
        "updated_by",
        "created_at",
        "updated_at",
        "deleted_at",
    ];
}
