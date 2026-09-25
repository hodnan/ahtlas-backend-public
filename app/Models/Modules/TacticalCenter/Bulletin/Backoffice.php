<?php

namespace App\Models\Modules\TacticalCenter\Bulletin;

use App\Models\Modules\Employee\Employee;
use App\Models\Modules\Employee\SectorN1;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;


class Backoffice extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'modules';
    protected $table = 'bulletin_backoffices';

    protected $fillable = [
        'month_ref',
        'date_ref',
        'username',
        'sector_n1_id',
        'manager_n1_id',
        'manager_n2_id',
        'manager_n3_id',
        'manager_n4_id',
        'manager_n5_id',
        'working_hours',
        'protocol',
        'environment_id',
        'mailing_id',
        'queue_id',
        'unique',
        'finished',
        'time_productive',
        'time_handle',
        'is_simultaneous',
        'time_start',
        'time_end',
        'status_id',
    ];
    // protected function casts(): array {return [];}
    // public $appends = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'username', 'username')
        // ->where('hierarchical_level', 2)
        ->select('username', 'name', 'position_summary', 'hierarchical_level', 'active', 'working_hours' );
    }

    public function environment()
    {
        return $this->belongsTo(BackofficeEnvironment::class, 'environment_id', 'id')->select('id', 'name');
    }

    public function mailing()
    {
        return $this->belongsTo(BackofficeMailing::class, 'mailing_id', 'id')->select('id', 'name');
    }

    public function queue()
    {
        return $this->belongsTo(BackofficeQueue::class, 'queue_id', 'id')->select('id', 'name');
    }

    public function sectorN1()
    {
        return $this->belongsTo(SectorN1::class, 'sector_n1_id', 'id')->select('id', 'name');
    }

    public function managerN1()
    {
        return $this->belongsTo(Employee::class, 'manager_n1_id', 'username')
        // ->where('hierarchical_level', 1)
        ->select('username', 'name', 'position_summary', 'hierarchical_level', 'active' );
    }
   
    public function managerN2()
    {
        return $this->belongsTo(Employee::class, 'manager_n2_id', 'username')
        // ->where('hierarchical_level', 2)
        ->select('username', 'name', 'position_summary', 'hierarchical_level', 'active' );
    }

    public function managerN3()
    {
        return $this->belongsTo(Employee::class, 'manager_n3_id', 'username')
        // ->where('hierarchical_level', 3)
        ->select('username', 'name', 'position_summary', 'hierarchical_level', 'active' );
    }

    public function managerN4()
    {
        return $this->belongsTo(Employee::class, 'manager_n4_id', 'username')
        // ->where('hierarchical_level', 4)
        ->select('username', 'name', 'position_summary', 'hierarchical_level', 'active' );
    }

    public function managerN5()
    {
        return $this->belongsTo(Employee::class, 'manager_n5_id', 'username')
        // ->where('hierarchical_level', 5)
        ->select('username', 'name', 'position_summary', 'hierarchical_level', 'active' );
    }

    public function status()
    {
        return $this->belongsTo(BackofficeStatus::class, 'status_id', 'id')
        // ->where('hierarchical_level', 5)
        ->select('id', 'name', 'color');
    }

  

}
