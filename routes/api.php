<?php

use App\Booking\Infrastructure\AdminBookingsController;
use App\Booking\Infrastructure\AdminMeetingTypesController;
use App\Booking\Infrastructure\BookingsController;
use App\Booking\Infrastructure\MeetingTypesController;
use Illuminate\Support\Facades\Route;

Route::get('api/admin/bookings', [AdminBookingsController::class, 'list'])->name('admin-bookings.list');
Route::get('api/meeting-types', [MeetingTypesController::class, 'list'])->name('meeting-types.list');
Route::get('api/meeting-types/{id}/slots', [MeetingTypesController::class, 'slots'])->name('meeting-types.slots');
Route::post('api/admin/meeting-types', [AdminMeetingTypesController::class, 'create'])->name('admin-meeting-types.create');
Route::post('api/bookings', [BookingsController::class, 'create'])->name('bookings.create');
