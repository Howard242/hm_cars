<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'make', 'model', 'year', 'price', 'location', 'transmission',
        'fuel_type', 'mileage_km', 'description', 'slug', 'status',
        'seller_name', 'seller_phone', 'marketing_authorized_at',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'price' => 'decimal:2',
            'mileage_km' => 'integer',
            'marketing_authorized_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }
    
public function images(): HasMany
{
    return $this->hasMany(VehicleImage::class)
        ->orderBy('sort_order')
        ->orderBy('id');
}

public function primaryImage()
{
    return $this->hasOne(VehicleImage::class)
        ->where('is_primary', true);
}

public function promotions(): HasMany
{
    return $this->hasMany(VehiclePromotion::class);
}

}
