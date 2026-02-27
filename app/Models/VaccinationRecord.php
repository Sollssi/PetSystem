<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaccinationRecord extends Model
{
    protected $fillable = [
        'pet_id',
        'vaccine_name',
        'application_date',
        'next_due_date',
        'veterinarian',
        'notes',
    ];

    protected $casts = [
        'application_date' => 'date',
        'next_due_date' => 'date',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
