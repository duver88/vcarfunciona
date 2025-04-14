<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyInfo extends Model
{
    protected $fillable = [
        'vcard_id',
        'blood_type',
        'primary_contact_name',
        'primary_contact_number',
        'medical_conditions',
        'emergency_contacts', // este campo será un array JSON
    ];

    protected $casts = [
        'emergency_contacts' => 'array',
    ];

    public function vcard()
    {
        return $this->belongsTo(Vcard::class);
    }
}
