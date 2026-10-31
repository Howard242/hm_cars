<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleSubmissionImage extends Model
{
    protected $fillable = [
        'image_path',
        'sort_order',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(\App\Models\VehicleSubmission::class, 'vehicle_submission_id');
    }
}