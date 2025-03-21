<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    use HasFactory;

    // The table associated with the model.
    protected $table = 'discount_codes';

    // The attributes that are mass assignable.
    protected $fillable = [
        'code',
        'discount_percentage',
        'is_active',
        'expiration_date',
        'usage_limit',
        'used_count',
        'inspector_name', // Add inspector_name to the fillable array
    ];

    // Optionally, you can add some scopes or methods
    public function isValid()
    {
        // Check if the code is active and not expired
        return $this->is_active && ($this->expiration_date === null || $this->expiration_date >= now());
    }

    public function canBeUsed()
    {
        // Check if the usage limit has been reached
        return $this->usage_limit === null || $this->used_count < $this->usage_limit;
    }
}
