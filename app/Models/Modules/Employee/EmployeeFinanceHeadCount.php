<?php

namespace App\Models\Modules\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class EmployeeFinanceHeadCount extends Model
{
    use HasFactory, TimezoneTrait;

   protected $connection = 'modules';
   // protected $table = '';
   // public $incrementing = false;

   // protected $fillable = [];    
   // protected function casts(): array {return [];}
   // public $appends = [];
}
