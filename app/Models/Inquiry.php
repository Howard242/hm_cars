<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    protected $fillable = [
        'vehicle_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'message',
        'status',
        'source',
        'assigned_to_user_id',
        'vehicle_promotion_id',
        'utm_source',
        'utm_medium',
        'utm_campaign',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vehiclePromotion(): BelongsTo
    {
        return $this->belongsTo(VehiclePromotion::class);
    }
    public function assignedStaff(): BelongsTo
    
{
    return $this->belongsTo(User::class, 'assigned_to_user_id');
}

public function followUps(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(InquiryFollowUp::class);
}

}
