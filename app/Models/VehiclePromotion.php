<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehiclePromotion extends Model
{
    protected $fillable = [
        'vehicle_id',
        'user_id',
        'platform',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function promoter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(VehiclePromotionVisit::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }
}
