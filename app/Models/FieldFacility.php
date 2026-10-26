<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldFacility extends Model
{
    public $timestamps = false;

    protected $table = 'field_facilities';

    protected $fillable = [
        'field_id', 'facilities_id',
    ];
}
