<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'venue_id', 'sport_id', 'nama_lapangan', 'harga_perjam', 
        'meta_harga', 'status_lapangan', 'alasan_penolakan'
    ];

    protected function casts(): array
    {
        return [
            'meta_harga'  => 'array',
            'submitted_at' => 'datetime',
            'reviewed_at'  => 'datetime',
        ];
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function vanue()
    {
        return $this->venue();
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    public function sport()
    {
        return $this->belongsTo(MasterSport::class, 'sport_id');
    }

    public function photos()
    {
        return $this->hasMany(FieldPhoto::class, 'field_id');
    }

    public function schedules()
    {
        return $this->hasMany(FieldSchedule::class, 'field_id');
    }
    
    // Relasi Many-to-Many ke MasterFacilities melalui field_facilities
    public function facilities()
    {
        return $this->belongsToMany(MasterFacility::class, 'field_facilities', 'field_id', 'facilities_id');
    }
}