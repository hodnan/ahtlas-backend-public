<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

trait UuidTrait
{
    public static function bootUuidTrait()
    {
        static::creating(function (Model $model) {
            $model->setUuid();
        });
    }

    protected function setUuid()
    {
        if (in_array('uuid', $this->getFillable())) {
            $this->attributes['uuid'] = Str::uuid()->toString();
        }
    }
}
