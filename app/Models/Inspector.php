<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;

class Inspector extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'team_code',
        'signature',  // assuming we're storing the path to the uploaded file
        'license_number',
        'license_copy',  // assuming we're storing the path to the uploaded file
        'agreement',
        'adminApprove',
    ];

    // If you would like to hide sensitive data when outputting the model, you can use:
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function discountCodes()
{
    return $this->hasMany(DiscountCode::class, 'inspector_name', 'name');
}

public function bookingRequests()
{
    return $this->hasMany(BookingRequest::class, 'assignTo', 'name');
}


}
