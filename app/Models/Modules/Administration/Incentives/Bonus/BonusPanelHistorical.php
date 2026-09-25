<?php

namespace App\Models\Modules\Administration\Incentives\Bonus;

use App\Services\Modules\Administration\BonusInterface;
use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class BonusPanelHistorical extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait;

    protected $connection = 'modules';

    protected $fillable = [
        'panel_id',
        'meta',
        'status',
        'notes',
        'created_by',
        'created_at'
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'created_at' => 'datetime:d/m/y H:i',
        ];
    }

    public function getStatusAttribute()
    {
        return isset($this->attributes['status']) ? BonusInterface::PANEL_STATUSES[$this->attributes['status']] : Null;
    }


}
