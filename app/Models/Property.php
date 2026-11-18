<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    protected function casts(): array{
        return [
            'is_sold' => 'boolean',
            'size' => 'decimal:2',
            'price' => 'decimal:2',
        ];
    }

}
