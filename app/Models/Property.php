<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Inquiry;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'description',
        'latitude',
        'longitude',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status'   => 'string',
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

    public function inquiries(){
        return $this->hasMany(Inquiry::class);
    }

    public function documents(): BelongsToMany{
        return $this->belongsToMany(Document::class, 'property_document'); 
    }   
}