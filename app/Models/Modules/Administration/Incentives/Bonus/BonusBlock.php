<?php

namespace App\Models\Modules\Administration\Incentives\Bonus;

use App\Models\Modules\Employee\Employee;
use App\Traits\PublicIdTrait;
use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;
use App\Traits\UcFirstTrait;

class BonusBlock extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait, UcFirstTrait, PublicIdTrait;

    protected $connection = 'modules';


    // protected $table = '';
    // public $incrementing = false;

    protected $fillable = [
        'public_id',
        'name',
        'fiscal_year_id',
        'default',
        'owner_id',
        'order',
        'weight',
        'grade',
        'grade_weight',
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
                'public_id',
                'year',
                'month_start',
                'month_end',
            );
    }

    public function blockItens()
    {
        return $this->hasMany(BonusBlockItem::class, 'block_id', 'public_id')->select([
            'id',
            'block_id',
            'indicator_id',
            'owner_id',
            'leader_id',
            'area',
            'proof',
            'accumulation_type',
            'target',      
            'weight',
      
        ]);
    }

    
}
