<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OwnershipGuideStep extends Model
{
    protected $fillable = [
        'title',
        'body',
        'image_path',
        'sort_order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function guide(): BelongsTo
    {
        return $this->belongsTo(OwnershipGuide::class, 'ownership_guide_id');
    }
}
