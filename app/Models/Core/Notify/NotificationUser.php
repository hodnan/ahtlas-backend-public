<?php

namespace App\Models\Core\Notify;

use App\Traits\SetByTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class NotificationUser extends Model
{
    use HasFactory, TimezoneTrait, SetByTrait;

    protected $connection = 'core';

    protected $fillable = [
        'notification_id',
        'delivered',
        'received',
        'read',
        'meta',
        'created_by',
        'updated_by',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class)->select(
            'id',
            'type',
            'title',
            'notes',
            'route',
            'external_link',
            'img'
        );
    }
}
