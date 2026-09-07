<?php

namespace App\Booking\Infrastructure;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;

class ValidationResponse
{
    public static function from(Validator $validator): JsonResponse
    {
        return self::withFields($validator->errors()->toArray());
    }

    public static function forField(string $field, string $message): JsonResponse
    {
        return self::withFields([$field => [$message]]);
    }

    /** @param  array<string, list<string>>  $fields */
    private static function withFields(array $fields): JsonResponse
    {
        return response()->json([
            'code' => 'validation_failed',
            'message' => 'Данные не прошли проверку.',
            'fields' => $fields,
        ], 422);
    }
}
