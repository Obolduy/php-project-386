<?php

namespace App\Booking\Infrastructure;

use Illuminate\Http\JsonResponse;

class NotFoundResponse
{
    public static function meetingType(): JsonResponse
    {
        return response()->json([
            'code' => 'not_found',
            'message' => 'Тип встречи не найден.',
        ], 404);
    }
}
