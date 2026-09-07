<?php

use App\Booking\Infrastructure\BookingController;
use App\Home\Infrastructure\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/booking', [BookingController::class, 'index'])->name('booking');
