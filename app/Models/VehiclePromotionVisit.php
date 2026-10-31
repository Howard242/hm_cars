<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehiclePromotionVisit extends Model
{
    protected $fillable = [
        'vehicle_promotion_id',
    ];

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(VehiclePromotion::class, 'vehicle_promotion_id');
    }
}
