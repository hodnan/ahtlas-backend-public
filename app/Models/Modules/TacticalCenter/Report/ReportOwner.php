<?php

namespace App\Models\Modules\TacticalCenter\Report;

use App\Models\Modules\Employee\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class ReportOwner extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'modules';
   protected $table = 'report_owners';
   // public $incrementing = false;

   protected $fillable = [
    'report_id',	'username',	'created_at',	'updated_at'
   ];    
   // protected function casts(): array {return [];}
   // public $appends = [];

   public function user()
   {
       return $this->belongsTo(Employee::class, 'username', 'username')->select('username', 'name', 'position_summary',  'active');
   }
}
