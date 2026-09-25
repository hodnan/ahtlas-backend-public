<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Exception;

trait ReadOnlyTrait
{
    public static function bootReadOnlyTrait()
    {
        static::updating(function (Model $model) {
            throw new Exception('Updates are not allowed for this model.');
        });

        static::saving(function (Model $model) {
            throw new Exception('Saves are not allowed for this model.');
        });

        static::deleting(function (Model $model) {
            throw new Exception('Deletes are not allowed for this model.');
        });
    }

    public function save(array $options = [])
    {
        throw new Exception('Saves are not allowed for this model.');
    }

    public function delete()
    {
        throw new Exception('Deletes are not allowed for this model.');
    }
}
