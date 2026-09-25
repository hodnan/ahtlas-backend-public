<?php

namespace App\Models\Modules\Administration\Incentives\Bonus;

use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class BonusPanelBlock extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait;

    protected $connection = 'modules';

    protected $fillable = [
        "panel_id",
        "block_id",
        "fiscal_year_id",
        "order",
        "weight",
        "grade",
        "grade_weight",
        "created_by",
        "updated_by",
    ];

    public function block()
    {
        return $this->belongsTo(BonusBlock::class, 'block_id', 'public_id');
    }
}
