<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait SetByTrait
{
    public static function bootSetByTrait()
    {
        static::creating(function (Model $model) {
            $model->setCreatedBy();
            $model->setUpdatedBy();
        });

        static::updating(function (Model $model) {
            $model->setUpdatedBy();
        });

        static::saving(function (Model $model) {
            $model->setUpdatedBy();
        });

        static::deleting(function (Model $model) {
            $model->setDeletedBy();
        });
    }

    protected function setCreatedBy()
    {
        if (in_array('created_by', $this->getFillable()) ) {
            $this->created_by =  Auth::user() ?  Auth::user()->username : 'cs000000';
        }
    }

    protected function setUpdatedBy()
    {
        if (in_array('updated_by', $this->getFillable()) ) {
            $this->updated_by = Auth::user() ?  Auth::user()->username : 'cs000000';
        }
    }

    protected function setDeletedBy()
    {
        if (in_array('deleted_by', $this->getFillable())) {
            $this->deleted_by = Auth::user() ?  Auth::user()->username : 'cs000000';
        }
    }
}
