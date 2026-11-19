<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    protected $fillable = [
        'visitor_id', 'user_id', 'property_id', 'path', 'route_name', 'referrer',
        'ip_address', 'country', 'region', 'city',
        'device_type', 'platform', 'browser',
    ];
}   
