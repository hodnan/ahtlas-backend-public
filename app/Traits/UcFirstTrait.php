<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait UcFirstTrait
{
    public static function bootUcFirstTrait()
    {
        static::creating(function (Model $model) {
            $model->setName();
            $model->setTitle();
        });

        static::updating(function (Model $model) {
            $model->setName();
            $model->setTitle();
        });

        static::saving(function (Model $model) {
            $model->setName();
            $model->setTitle();
        });
    }

    protected function setName()
    {
        if (in_array('name', $this->getFillable())) {
            $this->attributes['name'] = ucfirstException($this->attributes['name']);
        }
    }

    protected function setTitle()
    {
        if (in_array('title', $this->getFillable())) {
            $this->attributes['title'] = ucfirstException($this->attributes['title']);
        }
    }
}
