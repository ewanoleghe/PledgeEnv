<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;

    // Fields that can be mass-assigned
    protected $fillable = [
        'certificate_number',
        'booking_id',
        'order_id',
        'pass_fail',  // pass or failed
        'unit_number',
        'comments',
        'selected_date',
        'selected_time',
        'address',
        'municipality',
        'city',
        'county',
        'block',
        'lot',
        'service',
        'methodology',
        'includeXrf',
        'optional_message',
        'customer_name',
        'customer_email',
        'customer_phone',
        'property_owner_name',
        'property_owner_phone',
        'property_owner_email',
        'signature',  // assuming we're storing the path to the uploaded file
        'license_number',
        'inspector_name',
        'date_issued',
        'designation',
        'admin_review',
        'report_status',
        'floor_plan',
        'lab_report',
        'chain_custody',
        'xrf_report',
        'xrf_csv',
        'xrf_pass_fail',
    ];

    // Automatically cast 'photo_data' as an array
    protected $casts = [
        'photo_data' => 'array',
    ];

    // Define the relationship to the BookingRequest model
    public function bookingRequest()
    {
        return $this->belongsTo(BookingRequest::class, 'booking_id');
    }

    public function photos()
    {
        // return $this->hasMany(RecordPhoto::class, 'record_id');

        return $this->hasMany(RecordPhoto::class);
    }

    // Retrieve Inspectors records from  model
    public function inspector()
    {
        return $this->belongsTo(Inspector::class, 'inspector_name', 'name');
    }

}
