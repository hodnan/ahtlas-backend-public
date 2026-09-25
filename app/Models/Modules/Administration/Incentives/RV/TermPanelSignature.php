<?php

namespace App\Models\Modules\Administration\Incentives\RV;

use App\Services\Modules\Administration\TermInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;
use Carbon\Carbon;

class TermPanelSignature extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'modules';
    protected $table = 'rv_terms_panel_signatures';
    // public $incrementing = false;

    protected $fillable = [
        'month_ref',
        'term_file',
        'username',
        'uuid',
        'hostname',
        'ip',
        'available_at',
        'accepted_at',
        'accept',
    ];

    protected $casts = [
        'accept_meta' => 'array',
        'month_ref' => 'datetime:Y/m',
        'available_at' => 'datetime:d/m/Y H:i',
        'accepted_at' => 'datetime:d/m/Y H:i',
    ];

    public $appends = [
        'term'
    ];

    public function getTermAttribute()
    {
        $month = Carbon::parse($this->attributes['month_ref']);

        $name = str_replace('.PDF', '', $this->attributes['term_file']);
        $term = '/Painel/Termos/' . $month->copy()->format('Y/m/') . $this->attributes['term_file'];
        return [
            'term_name' => $name,
            'term_file' => $term,
        ];
    }

    public function getAcceptAttribute($value)
    {
        $accept = $this->attributes['accept'];

        return TermInterface::SIGNED[$accept] ?? 'Desconhecido';
    }
}
