<?php

namespace App\Models\Addons;

use App\Traits\ReadOnlyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class AiEmployeePower extends Model
{
    use HasFactory, TimezoneTrait, ReadOnlyTrait;

   protected $connection = 'mis_primary';
   protected $table = 'DB_CORPORATIVO.dbo.TB_CORP_ROBBYSON_RETORNO_GPS_DM_ACAO';
   // public $incrementing = false;

   // protected $fillable = [];    
   // protected function casts(): array {return [];}
   // public $appends = [];
}
