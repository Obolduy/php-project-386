<?php

namespace App\Providers;

use App\Booking\Domain\BookingWindow;
use App\Booking\Domain\WorkingHours;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(WorkingHours::class, fn (): WorkingHours => new WorkingHours(
            config('owner.timezone'),
            config('owner.working_hours.start'),
            config('owner.working_hours.end'),
        ));

        $this->app->bind(BookingWindow::class, fn (): BookingWindow => new BookingWindow(
            config('owner.timezone'),
            (int) config('owner.booking_window_days'),
        ));
    }

    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }
}
