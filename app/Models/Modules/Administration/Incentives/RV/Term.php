<?php

namespace App\Models\Modules\Administration\Incentives\RV;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Modules\Employee\Employee;
use App\Models\Modules\Employee\SectorN1;
use App\Models\Modules\Employee\SectorN2;
use App\Traits\TimezoneTrait;
use App\Models\Modules\Employee\EmployeeSnapshot;
use App\Services\Modules\Administration\TermInterface;
use App\Traits\SetByTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class Term extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait;

    protected $connection = 'modules';
    protected $table = 'rv_terms';

    protected $fillable = [
        'month_ref',
        'owner',
        'sector_n1_id',
        'sector_n2_id',
        'campaign',
        'payment_id',
        'position',
        'level',
        'roof',
        'apprentice',
        'budgeted',
        'mock',
        'delta',
        'term_name',
        'term',
        'version',
        'due_date_at',
        'notes',
        'basket',
        'accelerator',
        'deflator',
        'elimination',
        'status',
        'evaluation',
        'approved_by',
        'approved_at',
        'approved_meta',
        'created_by',
        'created_meta',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'sector_n1_id' => 'integer',
            'sector_n2_id' => 'integer',
            'is_agent_based' => 'boolean',
            'payment_id' => 'integer',
            'apprentice' => 'boolean',
            'evaluation' => 'boolean',
            'basket' => 'array',
            'accelerator' => 'array',
            'deflator' => 'array',
            'elimination' => 'array',
            'created_meta' => 'array',
            'approved_meta' => 'array',
            'meta' => 'array',
            'month_ref' => 'datetime:Y-m',
            'due_date_at' => 'datetime:d/m/Y',
            'approved_at' => 'datetime:d/m/Y H:i',
            'created_at' => 'datetime:d/m/y H:i',
            'updated_at' => 'datetime:d/m/y H:i',
        ];
    }

    public $appends = [
        // 'employee',
        // 'status_label',
        'evaluation_interval',
    ];

    public function getEvaluationIntervalAttribute($value)
    {
        $dateStart = Carbon::parse( $this->attributes['month_ref'])->startOfMonth();
        $dateEnd = $dateStart->endOfMonth()->format('d/m/Y');
        return $dateStart->format('01/m/Y') . ' até ' .  $dateEnd ;
    }


    public function getEvaluationAttribute($value)
    {
        $dateStart = Carbon::parse($this->attributes['month_ref'])->startOfMonth();
        $dateEnd = $dateStart->endOfMonth()->format('d/m/Y');
        return $dateStart->format('01/m/Y') . ' até ' .  $dateEnd;
    }

    public function getStatusAttribute($value)
    {
        if (isset($this->attributes['status'])) {
         
            $status = $this->attributes['status'];
            return TermInterface::STATUSES[$status];
        }
        return null;
    }
   
    public function getLevelAttribute($value)
    {
        if (isset($this->attributes['level'])) {
         
            $status = $this->attributes['level'];
            return TermInterface::LEVELS[$status];
        }
        return null;
    }
    public function getPositionAttribute($value)
    {
        if (isset($this->attributes['position'])) {
         
            $status = $this->attributes['position'];
            return TermInterface::POSITIONS[$status];
        }
        return null;
    }

    public function getTermAttribute()
    {
        return is_resource($this->attributes['term']) ? stream_get_contents($this->attributes['term']) : base64_encode($this->attributes['term']);
    }

    public function sectorN1()
    {
        return $this->belongsTo(SectorN1::class, 'sector_n1_id', 'id')->select('id', 'name', 'uf', 'active');
    }

    public function sectorN2()
    {
        return $this->belongsTo(SectorN2::class, 'sector_n2_id', 'id')->select('id', 'name', 'active');
    }

    public function payment()
    {
        return $this->belongsTo(TermPayment::class, 'payment_id', 'id')->select('id', 'event');
    }

    public function owner()
    {
        return $this->belongsTo(Employee::class, 'owner', 'username')->select('username', 'name');
    }

    public function signeds()
    {
        return $this->hasMany(TermSignature::class, 'term_id', 'id')->select('username', 'accept', 'term_id');
    }

    public function getEmployeeAttribute()
    {
        $level = ['VET' => 1, 'NOV' => 0, 'AMB' => '%'];
        $position = ['AGE' => 'Agente', 'SUP' => 'Supervisor', 'COO' => 'Coordenador'];

        return EmployeeSnapshot::where('status', 1)
            ->where('sector_n1_id', $this->sector_n1_id)
            ->where('sector_n2_id', $this->sector_n2_id)
            ->where('is_veteran', 'like', $level[$this->level])
            ->where('position_summary', 'like', $position[$this->position])

            ->get(['username', 'name', 'position_summary']);
    }
}
