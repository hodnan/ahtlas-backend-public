<?php

namespace App\Models\Modules\Administration\Intelligence;

use App\Models\Modules\Employee\Employee;
use App\Services\Modules\Administration\IndicatorInterface;
use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class KpiSource extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait;

    protected $connection = 'modules';
    protected $table = 'kpi_sources';
    // public $incrementing = false;

    protected $fillable = [
        'id',
        'db',
        'source',
        'owner',
        'indicator',
        'sla',
        'report_id',
        'data_location',
        'notes',
        'active',
        'created_by',
        'updated_by',
    ];
    protected function casts(): array
    {
        return [
            'updated_at' => 'datetime:Y/m/d H:i',
            'created_at' => 'datetime:Y/m/d H:i',
        ];
    }
    // public $appends = [];

    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator', 'id')->select('id', 'name');
    }


    public function owner()
    {
        return $this->belongsTo(Employee::class, 'owner', 'username')->select('username', 'name', 'position_summary', 'hierarchical_level', 'manager_n2_id');
    }

    public function getActiveAttribute()
    {
        return IndicatorInterface::STATUSES[$this->attributes['active']] ?? null;
    }

    public function sectors()
    {
        return $this->hasMany(KpiRelated::class, 'source_id')->select([
            'source_id',
            'sector_n1_id',
            'indicator_id',
            'report_id',
            'data_location',
        ]);
    }
}
