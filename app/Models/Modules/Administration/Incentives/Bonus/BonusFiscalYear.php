<?php

namespace App\Models\Modules\Administration\Incentives\Bonus;

use App\Services\Modules\Administration\BonusInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;
use App\Traits\UcFirstTrait;
use App\Traits\PublicIdTrait;
use App\Traits\SetByTrait;
use Carbon\Carbon;

class BonusFiscalYear extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait, UcFirstTrait, PublicIdTrait;

    protected $connection = 'modules';


    protected $fillable = [
        'public_id',
        'year',
        'month_start',
        'month_end',
        'active',
        'created_by',
        'updated_by',
    ];
    protected function casts(): array
    {
        return [
            'active' => 'boolean'
        ];
    }

    public $appends = [
        'label'
    ];



    public function getLabelAttribute($value)
    {

        $start = Carbon::parse($this->attributes['month_start'])->locale('pt_BR')->isoFormat('MMM');
        $end = Carbon::parse($this->attributes['month_end'])->locale('pt_BR')->isoFormat('MMM');
        $year = $this->attributes['year'];
        return "$year - $start a $end ";
    }

    public function getActiveAttribute($value)
    {
        if (isset($this->attributes['active'])) {
            $status = $this->attributes['active'];
            return BonusInterface::YEAR_STATUSES[$status];
        }
        return null;
    }
}
