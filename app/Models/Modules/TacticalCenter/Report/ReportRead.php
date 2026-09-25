<?php

namespace App\Models\Modules\TacticalCenter\Report;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class ReportRead extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'modules';

    protected $fillable = [
        'month_ref',
        'date_ref',
        'report_id',
        'username',
        'file_name',
        'meta',
        'errors',
    ];
    
    protected function casts(): array {return [
        'meta' => 'array',
        'errors' => 'array',
    ];}
    // public $appends = [];
}
