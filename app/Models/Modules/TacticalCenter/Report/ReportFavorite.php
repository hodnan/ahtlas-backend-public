<?php

namespace App\Models\Modules\TacticalCenter\Report;

use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class ReportFavorite extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait;

   protected $connection = 'modules';

   protected $fillable = [
    'report_id',
    'is_favorite',
    'created_by',
    'updated_by',
   ];    
   // protected function casts(): array {return [];}
   
}
