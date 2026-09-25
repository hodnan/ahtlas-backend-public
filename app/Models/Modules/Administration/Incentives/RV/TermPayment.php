<?php

namespace App\Models\Modules\Administration\Incentives\RV;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermPayment extends Model
{
    use HasFactory;

    protected $connection = 'modules';
    protected $table = 'rv_terms_payments';
    public $incrementing = false;

    protected $fillable = [
        'event',
        'taxation',
        'created_by',
        'updated_by',
    ];

    public $appends = [
        'label',
    ];

    public function getLabelAttribute()
    {
        return str_pad($this->id , 4 , '0' , STR_PAD_LEFT).' - '.$this->event;
    }
}
