<?php

namespace App\Models\Modules\Administration\Planning;

use App\Models\Modules\Employee\Employee;
use App\Services\Modules\Administration\PlanningFileInterface;
use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class PlanningFileLoad extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait;

    protected $connection = 'modules';
    // protected $table = '';
    // public $incrementing = false;

    protected $fillable = [
        'month_ref',
        'start_at',
        'end_at',
        'type',
        'file',
        'errors',
        'status',
        'created_by',
        'updated_by',
    ];
    protected function casts(): array {return [
        'start_at' => 'datetime:d/m/Y H:i',
        'end_at' => 'datetime:d/m/Y H:i',
        'errors' => 'array',
    ];}
    // public $appends = [];

    public function getTypeAttribute()
    {
        return PlanningFileInterface::TYPES[$this->attributes['type']] ?? 'Desconhecido';
    }

    public function getStatusAttribute()
    {
        return PlanningFileInterface::STATUSES[$this->attributes['status']] ?? 'Desconhecido';
    }

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by','username' )->select(['username', 'name']);
    }

}
