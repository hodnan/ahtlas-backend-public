<?php

namespace App\Models\Modules\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSnapshot extends Model
{
    use HasFactory;
    protected $connection = 'modules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'month_ref',
        'username',
        'name',
        'uf',
        'position',
        'position_summary',
        'sector_n1_id',
        'sector_n2_id',
        'manager_n1_id',
        'manager_n2_id',
        'manager_n3_id',
        'manager_n4_id',
        'manager_n5_id',
        'manager_n6_id',
        'hierarchical_level',
        'type',
        'staff',
        'admission',
        'dismissal',
        'is_veteran',
        'start_time',
        'working_hours',
        'status',
        'status_real',
        'is_closed'
    ];


    protected $casts = [
        'month_ref' => 'datetime:Y-m',        
    ];
}
