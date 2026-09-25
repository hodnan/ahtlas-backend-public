<?php

namespace App\Models\Modules\Management\MyTeam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class AiEmployeePowerAction extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'modules';
    protected $table = 'ai_employee_power_actions';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'action'
    ];    

}
