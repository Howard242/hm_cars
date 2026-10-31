<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeHeroSlide extends Model
{
    protected $fillable = [
        'eyebrow', 'title', 'highlight', 'description',
        'primary_label', 'primary_url', 'secondary_label', 'secondary_url',
        'image_path', 'sort_order', 'is_published', 'autoplay_seconds',
        'transition_style', 'offer_badge', 'featured_vehicle_id',
    ];

    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'autoplay_seconds' => 'integer', 'is_published' => 'boolean'];
    }

    public function featuredVehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'featured_vehicle_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
