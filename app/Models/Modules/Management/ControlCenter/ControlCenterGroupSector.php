<?php

namespace App\Models\Modules\Management\ControlCenter;

use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;
use App\Traits\UcFirstTrait;

class ControlCenterGroupSector extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait, UcFirstTrait;

    protected $connection = 'modules';
    // protected $table = '';
    // public $incrementing = false;

    protected $fillable = [
        'name',
        'sectors',
        'active',
        'created_by',
        'updated_by',
    ];
    protected function casts(): array
    {
        return [
            'sectors' => 'array',
        ];
    }
    public $appends = [
        'label'
    ];

    public function getLabelAttribute(): string|bool
    {
        return $this->id . ' - ' . $this->name;
    }
}
