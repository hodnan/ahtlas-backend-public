<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class UserAvatar extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'core';
    protected $table = 'user_avatars';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'username',
        'nickname',
        'avatar',
    ];

    public function getAvatarAttribute()
    {
        return is_resource($this->attributes['avatar']) ? stream_get_contents($this->attributes['avatar']) : base64_encode($this->attributes['avatar']);     
    }
     
    // public function getAvatarAttribute($value)
    // {
    //     // Verifica se o valor é um recurso de stream
    //     if (is_resource($value)) {
    //         return stream_get_contents($value);
    //     }
    //     // Retorna o valor como está se não for um recurso de stream
    //     return $value;
    // }
    // protected $casts = [];
    // public $appends = [];
}
