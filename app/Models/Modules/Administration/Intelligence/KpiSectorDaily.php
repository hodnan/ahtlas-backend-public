<?php

namespace App\Models\Modules\Administration\Intelligence;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class KpiSectorDaily extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'modules';
    protected $table = 'kpi_result_sector_dailies';
   // public $incrementing = false;

   // protected $fillable = [];    
   // protected function casts(): array {return [];}
   // public $appends = [];
}
