<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_request_id',
        'site_contact',
        'site_contact_email',
        'site_contact_phone',
        'unit_number',  // Added unit_number field
    ];

    /**
     * Get the booking request that owns the unit contact.
     */
    public function bookingRequest()
    {
        return $this->belongsTo(BookingRequest::class);
    }
}


