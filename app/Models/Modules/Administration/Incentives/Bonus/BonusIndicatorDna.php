<?php

namespace App\Models\Modules\Administration\Incentives\Bonus;

use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;
use App\Traits\UcFirstTrait;

class BonusIndicatorDna extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait, UcFirstTrait;

   protected $connection = 'module';
   // protected $table = '';
   // public $incrementing = false;

   // protected $fillable = [];    
   // protected function casts(): array {return [];}
   // public $appends = [];
}
