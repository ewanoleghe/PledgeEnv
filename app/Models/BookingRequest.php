<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 
        'old_order_id', 
        'service',
        'selected_date',
        'price',
        'price_visual',
        'price_dust',
        'xrf',
        'xrf_reinspection_price',
        'visual_reinspection_price',
        'dust_wipe_reinspection_price',
        'weekendFee',
        'totalWeekendFee',
        'NewSubTotal',
        'totalXrf',
        'baseXrf',
        'credSucharg',
        'totalPay',
        'basePrice',
        'totalBasePrice',
        'includeXrf',
        'InspectionType',
        'dcaMunicode',
        'municipality',
        'county',
        'methodology',
        'totalCost',
        'subtotal',
        'selectedTime',
        'name',
        'email',
        'phone',
        'address',
        'apt',
        'city',
        'state',
        'block',
        'lot',
        'units',
        'builtBefore1978',
        'useSameContactForAll',
        'siteContact',
        'siteContactEmail',
        'siteContactPhone',
        'optionalMessage',
        'agreedToTerms',
        'read_unr',
        'assignTo', 
        'jobStatus',
        'payment_status',
        'discountCode', 
        'discountTotal',
        'discountPercentage',    
        'selectedPaymentMethod', 
        'payment_intent', 
    ];

    /**
     * A booking request can have many unit contacts.
     */
    public function unitContacts()
    {
        return $this->hasMany(UnitContact::class);
    }

    // Define the inverse relationship with the Record model
    public function records()
    {
        return $this->hasMany(Record::class);
    }
}
