<?php

namespace App\Models\Modules\Management\ControlCenter;

use App\Models\Modules\Administration\Intelligence\Indicator;
use App\Services\Modules\Management\ControlCenter\ControlCenterInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class ControlCenterTrackingDailyResultSector extends Model
{
    use HasFactory, TimezoneTrait;

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
    ];
    // protected function casts(): array {return [];}
    // public $appends = [];

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
        return ControlCenterInterface::STAGE[$this->attributes['stage']] ?? ControlCenterInterface::STAGE_NULL; 
    }
}
