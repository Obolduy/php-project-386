<?php

namespace App\Home\Infrastructure;

use Illuminate\Contracts\View\View;

class HomeController
{
    public function index(): View
    {
        return view('home', [
            'durationMinutes' => config('booking.duration_minutes'),
        ]);
    }
}
