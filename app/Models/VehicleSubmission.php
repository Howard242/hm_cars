<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleSubmission extends Model
{
    public const STATUS_PENDING_REVIEW = 'pending_review';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_MORE_INFORMATION_REQUIRED = 'more_information_required';

    protected $fillable = [
        'tracking_token_hash',
        'request_key_hash',
        'status',
        'seller_name',
        'seller_phone',
        'seller_email',
        'make',
        'model',
        'year',
        'price',
        'location',
        'transmission',
        'fuel_type',
        'mileage_km',
        'description',
        'terms_accepted_at',
        'terms_version',
        'marketing_authorized_at',
        'admin_feedback',
        'seller_response',
        'approved_vehicle_id',
        'reviewed_by_user_id',
        'reviewed_at',
    ];

    protected $hidden = [
        'tracking_token_hash',
        'request_key_hash',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'price' => 'decimal:2',
            'mileage_km' => 'integer',
            'terms_accepted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'marketing_authorized_at' => 'datetime',
        ];
    }

    public function images(): HasMany
    {
        return $this->hasMany(VehicleSubmissionImage::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function approvedVehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'approved_vehicle_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
