<?php

namespace App\Models\Modules\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectorN2 extends Model
{
    use HasFactory;
   
    protected $connection = 'modules';
    protected $table = 'employee_sectors_n2';
    public $incrementing = false;


    protected $fillable = [
        'id',
        'name',
        'active'
    ];

    protected $casts = [
        'id' => 'integer'
    ];

    public $appends = [
        'label',
    ];

    public function getLabelAttribute(): string|bool
    {
        if( $this->id ==0){
            return 'Vazio';
        }

        return $this->id . ' - ' . $this->name;
    }
}
