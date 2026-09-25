<?php

namespace App\Models\Modules\Administration\Intelligence;

use App\Models\Modules\Employee\SectorN1;
use App\Models\Modules\TacticalCenter\Report\Report;
use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;
use Carbon\Carbon;

class KpiRelated extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait;

    protected $connection = 'modules';
    protected $table = 'kpi_relateds';
    //    public $incrementing = false;

    protected $fillable = [
        'source_id',
        'sector_n1_id',
        'indicator_id',
        'report_id',
        'data_location',
        'last_update',
        'last_result',
        'delay',
        'on_update',
    ];

    protected function casts(): array
    {
        return [
            'last_update' => 'datetime:Y/m/d',
            'last_result' => 'decimal:2',
            'updated_at' => 'datetime:Y/m/d H:i',
            'created_at' => 'datetime:Y/m/d H:i',
            'on_update' => 'boolean',
        ];
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

    public function sectorN1()
    {
        return $this->belongsTo(SectorN1::class, 'sector_n1_id', 'id')->select('id', 'name');
    }

    public function source()
    {
        return $this->belongsTo(KpiSource::class, 'source_id', 'id')->select('id', 'owner', 'sla', 'active');
    }

    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id', 'id');
    }

   
}
