<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class UserAdmin extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'core';
   protected $table = 'user_admins';
   // public $incrementing = false;

   protected $fillable = [
    'username',
    'expiration_at',
   ];    
   // protected function casts(): array {return [];}
   // public $appends = [];
}
