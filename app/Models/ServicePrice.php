<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePrice extends Model
{
    use HasFactory;

    // If you don't want to specify table name, Laravel will assume the plural of the model name
    protected $table = 'service_prices';

    // Define which fields can be mass-assigned
    protected $fillable = [
        'service_name',
        'price',
        'price_visual',
        'price_dust',  
        'xrf',
        'xrf_reinspection_price',
        'visual_reinspection_price', 
        'dust_wipe_reinspection_price',
        'weekendFee',
        'description',
        'bookMax',
    ];
}
