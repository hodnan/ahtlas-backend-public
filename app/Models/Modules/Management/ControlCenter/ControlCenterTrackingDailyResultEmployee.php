<?php

namespace App\Models\Modules\Management\ControlCenter;

use App\Models\Modules\Administration\Intelligence\Indicator;
use App\Models\Modules\Employee\Employee;
use App\Models\Modules\Employee\SectorN1;
use App\Services\Modules\Management\ControlCenter\ControlCenterInterface;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class ControlCenterTrackingDailyResultEmployee extends Model
{
    use HasFactory, TimezoneTrait, UuidTrait;

    protected $connection = 'modules';
    // protected $table = '';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primeryKey = 'uuid';

    protected $fillable = [
        'uuid',
        'month_ref',
        'date_ref',
        'sector_n1_id',
        'indicator_id',
        'stage_id',
        'username',
        'factor_0',
        'factor_1',
        'result',
        'goal',
        'bypass',
        'delta',
        'stage',
        'quadrant',
    ];
    // protected function casts(): array {return [];}
    // public $appends = [];

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

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'username', 'username')
            ->select([
                'username',
                'name',
                'sector_n1_id',
                'manager_n1_id',
                'hierarchical_level',
                'position_summary',
                'active'
            ]);
    }

    public function getStageAttribute()
    {
        return ControlCenterInterface::STAGE[$this->attributes['stage']] ?? [];
    }
}
