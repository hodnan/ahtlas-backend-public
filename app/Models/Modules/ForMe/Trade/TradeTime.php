<?php

namespace App\Models\Modules\ForMe\Trade;

use App\Models\Modules\Employee\Employee;
use App\Models\Modules\Employee\SectorN1;
use App\Services\Modules\ForMe\Trade\TradetimeInterface;
use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;
use Carbon\Carbon;

class TradeTime extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait;
    
    protected $connection = 'modules';
    protected $table = 'trade_times';

    protected $fillable = [
        'username',
        'sector_n1_id',
        'time_old',
        'time_label',
        'time_start',
        'time_end',
        'notes',
        'status',
        'created_by',
        'updated_by',
    ];

    public $appends = [
        'count_days',
        'status_label',
        'status_color',
        'expires_in',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
            // 'time_old' => 'time',
            // 'time_start' => 'time',
            // 'time_end' => 'time',
        ];
    }

    public function sectorN1()
    {
        return $this->belongsTo(SectorN1::class, 'sector_n1_id', 'id')->select('id', 'name', 'uf', 'active');
    }

    public function user()
    {
        return $this->belongsTo(Employee::class, 'username', 'username')->select('username', 'name', 'active');
    }
 
    public function getStatusLabelAttribute()
    {
        return TradetimeInterface::STATUSLIST[$this->status] ?? 'Desconhecido';
    }

    public function getStatusColorAttribute()
    {
        return TradetimeInterface::STATUSCOLOR[$this->status] ?? 'Desconhecido';
    }

    public function getCountDaysAttribute()
    {
        $today = Carbon::now()->startOfDay();
        $created_at = Carbon::parse($this->created_at)->startOfDay();
        $updated_at = Carbon::parse($this->updated_at)->startOfDay();
        if ($this->status == TradetimeInterface::STATUS_PENDING || $this->status == TradetimeInterface::STATUS_RENOVATED) {
            $days = $created_at->diffInDays($today);
            return intval($days) ;
        } else {
            $days = $created_at->diffInDays( $updated_at);
            return intval($days) ;
        }
    }

    public function getExpiresInAttribute()
    {
        if ($this->status == TradetimeInterface::STATUS_PENDING || $this->status == TradetimeInterface::STATUS_RENOVATED) {
            if ($this->updated_at) {
                $updated_at = Carbon::parse($this->updated_at)->startOfDay()->addDays(45);
                $today = Carbon::now()->startOfDay();

                if ($updated_at->isPast()) {
                    // Se $updated_at é uma data passada, a diferença será negativa
                    $days = $updated_at->diffInDays($today) ;
                } else {
                    // Se $updated_at é uma data futura, a diferença será positiva
                    $days = $updated_at->diffInDays($today) * -1;
                }

                return intval($days) ;
            }
        }
        return '-';
    }
}
