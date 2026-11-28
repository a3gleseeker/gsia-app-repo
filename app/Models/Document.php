<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Document extends Model
{
    protected $fillable = [
        'name', 'file_location', 'inventory_code', 'document_type', 'description', 'user_id', 'file_size'
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'decimal:2',
        ];
    }

    public function properties(): BelongsToMany{
        return $this->belongsToMany(Property::class, 'property_document');
    }

    public function clients(): BelongsToMany{
        return $this->belongsToMany(Client::class, 'client_document');
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

}
