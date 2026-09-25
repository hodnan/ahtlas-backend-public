<?php

namespace App\Models\Modules\Management\MyTeam;


use App\Models\Modules\Employee\Employee;
use App\Models\Modules\Employee\SectorN1;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class AiEmployeePower extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'modules';
    protected $table = 'ai_employee_powers';

    protected $fillable = [
        'month_ref',
        'username',
        'sector_n1_id',
        'manager_n1_id',
        'manager_n2_id',
        'manager_n3_id',
        'manager_n4_id',
        'manager_n5_id',
        'hierarchical_level',
        'm0',
        'm1',
        'm2',
        'action_id',
    ];
    protected function casts(): array
    {
        return [
            'm0' => 'integer',
            'm1' => 'integer',
            'm2' => 'integer',
            'hierarchical_level' => 'integer',
            'action_id' => 'integer',
            'month_ref' => 'datetime:Y-m',
        ];
    }

    public $appends = [
        'type',
    ];

    public function getTypeAttribute()
    {
        return $this->attributes['hierarchical_level'] == 0 ? 'Agente' : 'Supervisor';
    }

    public function action()
    {
        return $this->belongsTo(AiEmployeePowerAction::class, 'action_id', 'id')
            ->select(['id', 'action']);
    }

    public function username()
    {
        return $this->belongsTo(Employee::class, 'username', 'username')
            ->select(['name', 'username']);
    }

    public function sectorN1()
    {
        return $this->belongsTo(SectorN1::class, 'sector_n1_id', 'id')
            ->select(['id', 'name']);
    }


    public function managers()
    {
        return $this->belongsTo(Employee::class, 'username', 'username')
            ->select(['username','sector_n1_id','manager_n1_id', 'manager_n2_id', 'manager_n3_id', 'manager_n4_id','manager_n5_id']);
    }
}
