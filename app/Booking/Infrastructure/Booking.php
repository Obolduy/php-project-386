<?php

namespace App\Booking\Infrastructure;

use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    protected $fillable = [
        'meeting_type_id',
        'starts_at',
        'ends_at',
        'guest_name',
        'guest_email',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'immutable_datetime',
            'ends_at' => 'immutable_datetime',
        ];
    }

    /** @return BelongsTo<MeetingType, $this> */
    public function meetingType(): BelongsTo
    {
        return $this->belongsTo(MeetingType::class);
    }

    protected static function newFactory(): BookingFactory
    {
        return BookingFactory::new();
    }
}
