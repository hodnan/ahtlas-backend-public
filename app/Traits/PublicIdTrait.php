<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait PublicIdTrait
{
    public static function bootPublicIdTrait()
    {
        static::creating(function (Model $model) {
            $model->setUuid();
        });

    }

    protected function setUuid()
    {
        if (in_array('public_id', $this->getFillable())) {
            $this->attributes['public_id'] = publicId();
        }
    }
}
