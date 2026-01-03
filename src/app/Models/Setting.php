<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'open_weekdays',
        'open_time',
        'close_time',
        'slot_minutes',
    ];

    protected $casts = [
        'open_weekdays' => 'array',
    ];

    public static function singleton(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'open_weekdays' => [1, 2, 3, 4, 5],
                'open_time' => '09:00',
                'close_time' => '18:00',
                'slot_minutes' => 60,
            ],
        );
    }
}

