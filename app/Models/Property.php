<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Property extends Model
{
    protected $fillable = [
        'link',
        'name',
        'city',
        'size',
        'postal',
        'type',
        'price',
        'is_sold',
        'description',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'is_sold'   => 'boolean',
            'size'      => 'decimal:2',
            'price'     => 'decimal:2',
            'latitude'  => 'float',
            'longitude' => 'float',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function views()
    {
        return $this->hasMany(PropertyView::class);
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class)->orderBy('position');
    }
}