<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class PropertyView extends Model
{
    protected $fillable = [
    'property_id', 'user_id', 'visitor_id', 'ip_address',
    'country', 'region', 'city',
    'device_type', 'platform', 'browser',
    ];

    public function views(){
        return $this->hasMany(PropertyView::class);
    }
}
