<?php

namespace App\Models\Modules\Management\ControlCenter;

use App\Models\Modules\Administration\Intelligence\Indicator;
use App\Models\Modules\Employee\SectorN1;
use App\Services\Modules\Management\ControlCenter\ControlCenterInterface;
use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class ControlCenterTrackingModel extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait;

    protected $connection = 'modules';
    protected $table = 'control_center_trackings';

    protected $fillable = [
        'month_ref',
        'sector_n1_id',
        'indicator_id',
        'notify_level',
        'owners',
        'goal',
        'bypass',
        'q1',
        'q2',
        'q3',
        'q4',
        'daily_goals',
        'active',
        'stage',
    ];
    
    protected function casts(): array
    {
        return [
            'daily_goals' => 'array',
            'owners' => 'array',
            'goal' => 'float',
            'bypass' => 'float',
            'q1' => 'float',
            'q2' => 'float',
            'q3' => 'float',
            'q4' => 'float',
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


}
