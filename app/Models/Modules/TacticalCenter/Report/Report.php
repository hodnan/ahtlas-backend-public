<?php

namespace App\Models\Modules\TacticalCenter\Report;

use App\Services\Modules\TacticalCenter\Report\ReportInterface;
use App\Traits\SetByTrait;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;
use App\Traits\UcFirstTrait;

class Report extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait, UuidTrait, UcFirstTrait;

    protected $connection = 'modules';
    protected $table = 'reports';

    protected $fillable = [       
        'uuid',
        'title',
        'type',
        'group',
        'schedule_time',
        'report',
        'atd',
        'interval_type',
        'interval_values',
        'owners',
        'active',
        'notes',
        'created_by',
        'updated_by',
    ];
    
    protected function casts(): array
    {
        return [
            'interval_values' => 'array',
            'interval_type' => 'integer',
            'created_at' => 'datetime:Y-m-d H:i',
            'updated_at' => 'datetime:Y-m-d H:i',          
        ];
    }
   
    public $appends = [
        'label'
    ];


    public function owners()
    {
        return $this->hasMany(ReportOwner::class);
    }

    public function favorite()
    {
        return $this->belongsTo(ReportFavorite::class, 'id', 'report_id');
    }

    public function getTypeAttribute()
    {
        return ReportInterface::REPORT_TYPES[$this->attributes['type']] ?? 'Desconhecido';
    }
   
    public function getTypeValueAttribute()
    {
        return ReportInterface::REPORT_TYPES[$this->attributes['type']] ?? 'Desconhecido';
    }

    public function getLabelAttribute()
    {
        return str_pad($this->attributes['id'], 4, '0', STR_PAD_LEFT) . ' - ' . $this->attributes['title'];
    }

    public function getGroupAttribute()
    {
        return ReportInterface::GROUPS[$this->attributes['group']] ??  ['id' => null, 'label' => 'Desconhecido'];
    }

    public function getActiveAttribute()
    {
        return ReportInterface::STATUSES[$this->attributes['active']] ?? 'Desconhecido';
    }
}
