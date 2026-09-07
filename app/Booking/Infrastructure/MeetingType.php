<?php

namespace App\Booking\Infrastructure;

use Database\Factories\MeetingTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingType extends Model
{
    /** @use HasFactory<MeetingTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration_minutes',
    ];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
        ];
    }

    protected static function newFactory(): MeetingTypeFactory
    {
        return MeetingTypeFactory::new();
    }
}
