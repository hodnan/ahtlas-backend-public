<?php

namespace App\Models\Modules\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectorN1N2 extends Model
{
    use HasFactory;
   
    protected $connection = 'modules';
    protected $table = 'employee_sectors_n1_n2';
    public $incrementing = false;


    protected $fillable = [
        'id',
        'sector_n1_id',
        'sector_n2_id',
    ];

    protected $casts = [
        'id' => 'integer'
    ];

    public function sectorN1()
    {
        return $this->belongsTo(SectorN1::class, 'sector_n1_id', 'id');
        
    }
    public function sectorN2()
    {
        return $this->belongsTo(SectorN2::class, 'sector_n2_id', 'id');
        
    }
}
