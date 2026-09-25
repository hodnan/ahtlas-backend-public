<?php

namespace App\Models\Modules\Administration\Intelligence;

use App\Services\Modules\Administration\IndicatorInterface;
use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;
use App\Traits\UcFirstTrait;

class Indicator extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait, UcFirstTrait;


    protected $connection = 'modules';
    protected $table = 'indicators';

    protected $fillable = [
        'id',
        'name',
        'direction',
        'symbol',
        'calc',
        'notes',
        'active',
        'is_percent',
        'created_by',
        'updated_by',
    ];
    protected function casts(): array
    {
        return [];
    }
    public $appends = [
        'label'
    ];

    public function getSymbolAttribute()
    {
        return IndicatorInterface::SYMBOLS[$this->attributes['symbol']] ?? null;
    }

    public function getIsPercentAttribute()
    {
        return [
            'value' => $this->attributes['is_percent'],
            'label' =>   $this->attributes['is_percent'] ? 'sim' : 'não'
        ];
    }

    public function getActiveAttribute()
    {
        return IndicatorInterface::STATUSES[$this->attributes['active']] ?? null;
    }

    public function getCalcAttribute()
    {
        return IndicatorInterface::CALCS[$this->attributes['calc']] ?? null;
    }

    public function getDirectionAttribute()
    {
        return IndicatorInterface::DIRECIONS[$this->attributes['direction']] ?? null;
    }

    public function getLabelAttribute()
    {
        return str_pad($this->attributes['id'], 4, '0', STR_PAD_LEFT) . ' - ' . $this->attributes['name'];
    }
}
