<?php

namespace App\Booking\Application;

enum BookingRefusal
{
    case SlotTaken;
    case NotABookableSlot;
}
