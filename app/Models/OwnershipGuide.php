<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OwnershipGuide extends Model
{
    protected $fillable = [
        'title',
        'introduction',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function steps(): HasMany
    {
        return $this->hasMany(OwnershipGuideStep::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
