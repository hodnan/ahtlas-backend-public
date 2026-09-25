<?php


namespace App\Models\Modules\Employee;

use App\Models\Modules\Employee\SectorN1;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectorN1Manager extends Model
{
    use HasFactory;

    protected $connection = 'modules';
    protected $table = 'employee_sectors_n1_managers';
    public $incrementing = false;

    protected $fillable = [
        'sector_n1_id',
        'manager_username',
        'hierarchical_level',
    ];

    public function sector_n1_id()
    {
        return $this->belongsTo(SectorN1::class, 'sector_n1_id', 'id')
        ->select(['name', 'uf', 'id']);
    }

    public function manager_username()
    {
        return $this->belongsTo(Employee::class, 'manager_username', 'username')
        ->select(['name', 'username', 'nickname',  'avatar', 'hierarchical_level', 'position_summary']);
    }
    
}
