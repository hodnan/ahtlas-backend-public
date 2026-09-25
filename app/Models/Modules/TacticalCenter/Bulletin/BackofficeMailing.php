<?php

namespace App\Models\Modules\TacticalCenter\Bulletin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;
use App\Traits\UcFirstTrait;

class BackofficeMailing extends Model
{
    use HasFactory, TimezoneTrait, UcFirstTrait;

    protected $connection = 'modules';
    protected $table = 'bulletin_backoffice_mailings';
    public $incrementing = false;

    protected $fillable = ['id', 'name'];  
    // protected function casts(): array {return [];}
    // public $appends = [];
}
