<?php

namespace App\Booking\Infrastructure;

use Illuminate\Contracts\View\View;

class BookingController
{
    public function index(): View
    {
        return view('booking');
    }
}
