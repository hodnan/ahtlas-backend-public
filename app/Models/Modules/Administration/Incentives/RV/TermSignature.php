<?php

namespace App\Models\Modules\Administration\Incentives\RV;

use App\Models\Core\User;
use App\Models\Modules\Employee\Employee;
use App\Services\Modules\Administration\TermInterface;
use App\Traits\SetByTrait;
use App\Traits\TimezoneTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TermSignature extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait;

    protected $connection = 'modules';
    protected $table = 'rv_terms_signatures';
    // public $incrementing = false;

    protected $fillable = [
        'uuid',
        'term_id',
        'month_ref',
        'username',
        'term_file',
        'accept',
        'accept_meta',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'accept_meta' => 'array',
        'read_meta' => 'array',
        'evaluation_details' => 'array',
        'month_ref' => 'datetime:Y-m',
        'created_at' => 'datetime:d/m/Y H:i',
        'updated_at' => 'datetime:d/m/Y H:i',
        'available_at' => 'datetime:d/m/Y H:i',
        'accepted_at' => 'datetime:d/m/Y H:i',
    ];

    public function getTermFileAttribute()
    {
        return is_resource($this->attributes['term_file']) ? stream_get_contents($this->attributes['term_file']) : base64_encode($this->attributes['term_file']);
    }

    public function getAcceptAttribute($value)
    {
        $accept = $this->attributes['accept'];

        return TermInterface::SIGNED[$accept] ?? 'Desconhecido';
    }

    public function scopeMe($query)
    {
        if (Auth::check()) {
            return $query->where('username', Auth::user()->username);
        }

        return $query->where('username', '0000000');
    }

    public function user()
    {
        return $this->belongsTo(Employee::class, 'username', 'username')->select(
            'username',
            'name',
            'position',
            'position_summary',
            'sector_n1_id',
            'sector_n2_id',
            'manager_n1_id',
            'manager_n2_id',
            'manager_n3_id',
            'manager_n4_id',
            'manager_n5_id',
            'hierarchical_level',
            'status'
        );
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id', 'id')->select('id', 'month_ref', 'term_name', 'term', 'approved_at',
        'sector_n1_id',
        'sector_n2_id',
        'evaluation',
          'status');
    }

    

}
