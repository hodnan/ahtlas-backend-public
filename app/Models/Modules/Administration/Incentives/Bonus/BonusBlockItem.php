<?php

namespace App\Models\Modules\Administration\Incentives\Bonus;

use App\Models\Modules\Administration\Intelligence\Indicator;
use App\Models\Modules\Employee\Employee;
use App\Services\Modules\Administration\BonusInterface;
use App\Traits\PublicIdTrait;
use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;
use App\Traits\UcFirstTrait;

class BonusBlockItem extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait, UcFirstTrait, PublicIdTrait;

    protected $connection = 'modules';

    protected $fillable = [
        'fiscal_year_id',
        'block_id',
        'indicator_id',
        'owner_id',
        'leader_id',
        'area',
        'proof',
        'weight',
        'range',
        'accumulation_type',
        
    ];
    protected function casts(): array
    {
        return [
            'range' => 'array',
        ];
    }


    public function getAccumulationTypeAttribute($value)
    {
        if (isset($this->attributes['accumulation_type'])) {
         
            $type= $this->attributes['accumulation_type'];
            return BonusInterface::ACCUMULATION_TYPES[$type];
        }
        return null;
    }

    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator_id', 'id')->select('id', 'name', 'direction');
    }


    public function owner()
    {
        return $this->belongsTo(Employee::class, 'owner_id', 'username')
            ->select(
                'username',
                'name',
                'nickname',
                'position_summary',
                'hierarchical_level'
            );
    }
    public function leader()
    {
        return $this->belongsTo(Employee::class, 'owner_id', 'username')
            ->select(
                'username',
                'name',
                'nickname',
                'position_summary',
                'hierarchical_level'
            );
    }

    public function targets()
    {
        return $this->hasMany(BonusBlockItemTarget::class, 'item_id', 'id')
            ->select(
                'id',
                'item_id',
                'month_ref',
                'month_target',
                'month_result',
                'month_detour',
                'accumulated_target',
                'accumulated_result',
                'accumulated_detour'
            )
            ->orderBy('month_ref', 'asc');
    }

    public function grade()
    {
        return $this->hasOne(BonusBlockItemTarget::class, 'item_id', 'id')
            ->select(
                'item_id',
                'month_ref',
                'month_target',
                'month_result',
                'month_detour',
                'accumulated_target',
                'accumulated_result',
                'accumulated_detour',
                'weight',
                'grade',
                'grade_weight',
            )
            ->orderBy('month_ref', 'asc');
    }
}
