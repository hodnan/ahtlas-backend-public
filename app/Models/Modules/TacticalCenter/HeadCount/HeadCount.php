<?php

namespace App\Models\Modules\TacticalCenter\HeadCount;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class HeadCount extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'modules';
    // protected $table = '';
    // public $incrementing = false;

    protected $fillable = [
        'month_ref',
        'date_ref',
        'sector_n1_id',
        'manager_n5_id',
        'manager_n4_id',
        'manager_n3_id',
        'hc_dim',
        'vacation_dim',
        'training_dim',
        'training_initial_dim',
        'training_migration_dim',
        'total_dim',
        'hc_real',
        'vacation_real',
        'training_real',
        'training_initial_real',
        'training_migration_real',
        'training_recycling_real',
        'training_return_leave_real',
        'away_real',
        'to_total_real',
        'to_active_real',
        'to_operation_real',
        'to_training_real',
        'total_real',
        'total_active_real',
        'budgeted_total',
        'hd_client_dim',
        'hc_dif',
        'vacation_dif',
        'training_dif',
        'total_dif',
        'training_delivery_this_month',
        'training_delivery_next_month',
        'version',
    ];
    protected function casts(): array
    {
        return [

            'month_ref' => 'datetime:Y-m-d',
            'date_ref' => 'datetime:Y-m-d',
            'sector_n1_id' => 'integer',
            'manager_n5_id' => 'integer',
            'manager_n4_id' => 'integer',
            'manager_n3_id' => 'integer',
            'hc_dim' => 'integer',
            'vacation_dim' => 'integer',
            'training_dim' => 'integer',
            'training_initial_dim' => 'integer',
            'training_migration_dim' => 'integer',
            'total_dim' => 'integer',
            'hc_real' => 'integer',
            'vacation_real' => 'integer',
            'training_real' => 'integer',
            'training_initial_real' => 'integer',
            'training_migration_real' => 'integer',
            'training_recycling_real' => 'integer',
            'training_return_leave_real' => 'integer',
            'away_real' => 'integer',
            'total_real' => 'integer',
            'total_active_real' => 'integer',
            'budgeted_total' => 'integer',
            'hd_client_dim' => 'integer',
            'hc_dif' => 'integer',
            'vacation_dif' => 'integer',
            'training_dif' => 'integer',
            'version' => 'integer',
        ];
    }
    // public $appends = [];

    
}
