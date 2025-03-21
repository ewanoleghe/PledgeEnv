<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'record_id', 
        'photo_path', 
        'description',
        'room',
        'component',
        'hazard',
        'substrate',
        'value'
    ];

    // Define relationship with Record
    public function record()
    {
        return $this->belongsTo(Record::class);
    }
}
