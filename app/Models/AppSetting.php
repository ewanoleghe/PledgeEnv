<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;

    // Specify the table associated with the model
    protected $table = 'app_settings';

    // Allow mass assignment for these fields
    protected $fillable = [
        'app_name',
        'company_address',
        'company_city',
        'company_state',
        'company_zip',
        'company_address_ny',
        'company_city_ny',
        'company_state_ny',
        'company_zip_ny',
        'company_email',
        'company_phone',
        'company_zelle',
        'company_njdca',
        'company_owner',
        'stripe_public_key',
        'stripe_secret_key',
        'vite_stripe_key',
        'stripe_webhook_secret',
    ];
}
