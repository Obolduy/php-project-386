<?php

namespace App\Booking\Infrastructure\Resources;

use Carbon\CarbonInterface;

class Utc
{
    public static function format(CarbonInterface $moment): string
    {
        return $moment->utc()->format('Y-m-d\TH:i:s\Z');
    }
}
