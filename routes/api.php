<?php

use App\Booking\Infrastructure\MeetingTypesController;
use Illuminate\Support\Facades\Route;

Route::get('api/meeting-types', [MeetingTypesController::class, 'list'])->name('meeting-types.list');
