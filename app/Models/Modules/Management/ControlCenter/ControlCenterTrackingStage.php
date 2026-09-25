<?php

namespace App\Models\Modules\Management\ControlCenter;

use App\Models\Modules\Administration\Intelligence\Indicator;
use App\Models\Modules\Employee\SectorN1;
use App\Services\Modules\Management\ControlCenter\ControlCenterInterface;
use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class ControlCenterTrackingStage extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait ;

    protected $connection = 'modules';

    protected $fillable = [
        'month_ref',
        'date_ref',
        'sector_n1_id',
        'indicator_id',
        'factor_0',
        'factor_1',
        'result',
        'goal',
        'bypass',
        'stage',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'daily_goals' => 'array',
            'owners' => 'array',
            'goal' => 'float',
            'bypass' => 'float',
            'factor_0' => 'float',
            'factor_1' => 'float',
            'result' => 'float',
            'created_at' =>  'datetime:Y-m-d',
            'updated_at' =>  'datetime:Y-m-d',
            'date_ref' =>  'datetime:Y-m-d',
            
        ];
    }

    public function sectorN1()
    {
        return $this->belongsTo(SectorN1::class, 'sector_n1_id', 'id')->select('id', 'name');
    }

    public function sectorN1Group()
    {
        return $this->belongsTo(ControlCenterGroupSector::class, 'sector_n1_id', 'id')->select('id', 'name');
    }


    public function indicator()
    {
        return $this->belongsTo(Indicator::class)->select(
            'id',
            'name',
            'direction',
            'symbol',
            'calc',
            'is_percent',
        );
    }

    public function getStageAttribute()
    {
        return ControlCenterInterface::STAGE[$this->attributes['stage']] ?? []; 
    }

    public function getStatusAttribute()
    {
        return ControlCenterInterface::STAGE_STATUSES[$this->attributes['status']] ?? []; 
    }
}
