<?php

namespace App\Models\Modules\Administration\Incentives\RV;

use App\Models\Modules\Administration\Intelligence\Indicator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Modules\Employee\Employee;

class TermEvaluation extends Model
{
    use HasFactory;

    protected $connection = 'modules';
    protected $table = 'rv_terms_evaluations';

    protected $fillable = [
        'month_ref',
        'term_id',
        'sector_n1_id',
        'sector_n2_id',
        'indicator_id',
        'position',
        'username', 
        'owner',
        'directive_type',
        'directive_range',
        'directive_operation',
        'directive_target', // Valor da meta 
        'directive_target_type', // determina se será usado média ou soma como meta para o indicador 
        'directive_value',
        'result_target',
        'result_value',
        'result_max_date',
        'evaluation_value',
        'evaluation_details',
    ];

    protected $casts = [        
        'evaluation_details' => 'array',        
        'created_at' => 'datetime:d/m/y H:i',
        'updated_at' => 'datetime:d/m/y H:i',
    ];

    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id', 'id')
            ->select([
                'id',
                'date_ref',
                'sector_n1_id',
                'sector_n2_id',
                'term_name',
                'level',
                'position',
                'owner',
                'status',
                'evaluation'
            ]);
    }

    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator_id', 'id')->select(['id','name']);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'username', 'username')->select(['username','name']);
    }

    
}
