<?php

namespace App\Models\Modules\Administration\Incentives\RV;

use App\Repositories\Modules\RV\Interfaces\TermInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Modules\Administration\Intelligence\Indicator;
use App\Models\Modules\Employee\SectorN1;
use Illuminate\Support\Carbon;

class TermDirective extends Model
{
    use HasFactory;

    protected $connection = 'modules';
    protected $table = 'rv_terms_directives';

    protected $fillable = [
        'term_id',
        'indicator_id',
        'type',
        'range',
        'operation',
        'average',
        'value',
    ];

    public $appends = [
       
    ];
    
    public function indicator()
    {
        return $this->hasMany(Indicator::class, 'id', 'indicator_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id', 'id')
            ->select([
                'id',
                'date_ref',
                'sector_n1_id',
                'sector_n2_id',
                'level',
                'position',
                'owner',
                'status',
                'evaluation'
            ]);
    }

  
}
