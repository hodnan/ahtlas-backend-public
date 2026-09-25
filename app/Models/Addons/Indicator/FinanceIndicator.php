<?php

namespace App\Models\Addons\Indicator;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class FinanceIndicator extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'mis_primary';
    protected $table = 'DB_KPI.dbo.TB_KPI_FINANCEIRO_DM_INDICADORES';
    protected $primaryKey = "ID";

    protected $fillable = [
            'ID',
            'NO_INDICADOR',
            'NO_DESCRICAO',
    ];
    // protected function casts(): array {return [];}
    // public $appends = [];
}
