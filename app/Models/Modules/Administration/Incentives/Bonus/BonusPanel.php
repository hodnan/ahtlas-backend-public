<?php

namespace App\Models\Modules\Administration\Incentives\Bonus;

use App\Models\Modules\Employee\Employee;
use App\Services\Modules\Administration\BonusInterface;
use App\Services\Modules\Employee\EmployeeInterface;
use App\Traits\PublicIdTrait;
use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class BonusPanel extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait, PublicIdTrait;

    protected $connection = 'modules';
  
    protected $fillable = [
        'fiscal_year_id',
        'public_id',
        'owner_id',
        'hierarchical_level',
        'area',
        'status',
        'created_by',
        'updated_by',
    ];
  
    protected function casts(): array
    {
        return [
            'grade' => 'decimal:2',
            'grade_weight' => 'decimal:2', 
            'created_at' => 'datetime:d/m/y H:i',
            'updated_at' => 'datetime:d/m/y H:i',
        ];
    }

    public function owner()
    {
        return $this->belongsTo(Employee::class, 'owner_id', 'username')
            ->select(
                'username',
                'name',
                'nickname',
                'position_summary',
                'hierarchical_level'
            );
    }

    public function historical()
    {
        return $this->hasMany(BonusPanelHistorical::class, 'panel_id', 'public_id')
            ->select(
                'panel_id',
                'status',
                'created_by',
                'created_at',
            )
            ->orderBy('created_at', 'desc');
    }

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by', 'username')
            ->select(
                'username',
                'name',
                'nickname',
                'position_summary',
                'hierarchical_level'
            );
    }

    public function updatedBy()
    {
        return $this->belongsTo(Employee::class, 'updated_by', 'username')
            ->select(
                'username',
                'name',
                'nickname',
                'position_summary',
                'hierarchical_level'
            );
    }

    public function fiscalYear()
    {
        return $this->belongsTo(BonusFiscalYear::class, 'fiscal_year_id', 'public_id')
            ->select(
                'id',
                'public_id',
                'year',
                'month_start',
                'month_end',
            );
    }

    public function blocks()
    {
        return $this->hasMany(BonusPanelBlock::class, 'panel_id', 'public_id')
            ;
    }

    public function getHierarchicalLevelAttribute()
    {
        return $this->attributes['hierarchical_level'] ? EmployeeInterface::HIERARCHICAL_LEVEL[$this->attributes['hierarchical_level']] : Null;
    }

    public function getStatusAttribute()
    {
        return isset($this->attributes['status']) ? BonusInterface::PANEL_STATUSES[$this->attributes['status']] : Null;
    }

}
