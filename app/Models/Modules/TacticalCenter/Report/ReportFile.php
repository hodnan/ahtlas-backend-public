<?php

namespace App\Models\Modules\TacticalCenter\Report;

use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;
use Carbon\Carbon;

class ReportFile extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait;

    protected $connection = 'modules';
    protected $with = ['report'];

    protected $fillable = [
        'year_ref',
        'month_ref',
        'report_uuid',
        'file',
        'created_by',
        'updated_by',
        'updated_at',
    ];

    public $appends = [
        'month_label'
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i',
            'updated_at' => 'datetime:Y-m-d H:i',          
        ];
    }

    public function getMonthLabelAttribute()
    {
        // Verifica se 'month_ref' está definido
        if (!empty($this->attributes['month_ref'])) {
            $date = Carbon::parse($this->attributes['month_ref']);
            $month = Carbon::parse($this->attributes['month_ref'])->format('m');
            return $month ." - ". ucfirst($date->locale('pt_BR')->translatedFormat('F')); // 'F' retorna o nome completo do mês
        }

        return null;
    }

    public function report()
    {
        return $this->belongsTo(Report::class, 'report_uuid', 'uuid')->select('id', 'uuid', 'title');
    }
}
