<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Property;

class Inquiry extends Model
{
    protected $fillable = [
        'property_id',
        'property_name', 
        'name',
        'message',
        'phone',
        'email',
        'status',
    ];

    public function property(){
        return $this->belongsTo(Property::class);
    }
}
