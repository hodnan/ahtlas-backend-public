<?php

namespace App\Models\Modules\Administration\Incentives\Bonus;

use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class BonusBlockItemTarget extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait;

    protected $connection = 'modules';
    // protected $table = '';
    // public $incrementing = false;

    protected $fillable = [
        'fiscal_year_id',
        'month_ref',
        'item_id',
        'indicator_id',
        'block_id',
        'month_target',
        'month_result',
        'month_detour',
        'accumulated_target',
        'accumulated_result',
        'accumulated_detour',
        'weight',
        'grade',
        'grade_weight',
        'created_by',
        'updated_by',
    ];
    
    protected function casts(): array
    {
        return [
            'month_ref' => 'datetime:m-y',          
            'accumulated_detour' => 'array',          
            'month_detour' => 'array',  
            'grade' => 'decimal:2',
            'grade_weight' => 'decimal:2',        
        ];
    }

}
