<?php

namespace App\Models\Modules\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectorN1 extends Model
{
    use HasFactory;
    protected $connection = 'modules';
    protected $table = 'employee_sectors_n1';
    public $incrementing = false;


    protected $fillable = [
        'id',
        'name',
        'uf',
        'active',
        'registration_prefix',
    ];

    public $appends = [
        'label',
    ];

    protected $casts = [
        'id' => 'integer',
        'sectors' => 'array',
    ];

    public function getLabelAttribute(): string|bool
    {

        $uf = isset($this->uf) ?  ' - '.  $this->uf : null;


        return str_pad( $this->id, 5, '0', STR_PAD_LEFT)  . ' - ' . $this->name . $uf;
    }


    public function managers()
    {
        return $this->belongsTo(SectorN1Manager::class, 'id', 'sector_n1_id')->select([
            'sector_n1_id',
            'manager_n1_id',
            'manager_n2_id',
            'manager_n3_id',
            'manager_n4_id',
            'manager_n5_id',
            'manager_n6_id',
        ]);
    }

    public function sectorsN2()
    {
        return $this->belongsToMany(SectorN2::class, 'employee_sectors_n1_n2', 'sector_n1_id', 'sector_n2_id')
        ->select([
            'employee_sectors_n2.id',
            'employee_sectors_n2.name',
            'employee_sectors_n2.active'
        ])
        ->orderBy('employee_sectors_n2.id');
    }
}
