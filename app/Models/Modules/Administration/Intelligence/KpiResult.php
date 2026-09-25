<?php

namespace App\Models\Modules\Administration\Intelligence;

use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class KpiResult extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait;

   protected $connection = 'modules';
   protected $table = 'kpi_results';
   public $incrementing = false;

   protected $fillable = [
    'date_ref',
    'sector_n1_id',
    'indicator_id',
    'username',
    'factor_0',
    'factor_1',
    'factor_2',
    'result',
    'created_at',
    'updated_at',
   ];    

   protected function casts(): array
   {
       return [         
           'date_ref' => 'datetime:Y-m-d',
       ];
   }
   // public $appends = [];
}
