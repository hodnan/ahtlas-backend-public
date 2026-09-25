<?php

namespace App\Models\Modules\TacticalCenter\Bulletin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class BackofficeEnvironment extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'modules';
    protected $table = 'bulletin_backoffice_environments';
    public $incrementing = false;

    protected $fillable = ['id', 'name'];      
    // protected function casts(): array {return [];}
    // public $appends = [];
}
