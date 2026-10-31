<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerBrokerageTerms extends Model
{
    protected $table = 'seller_brokerage_terms_versions';

    protected $fillable = [
        'version',
        'title',
        'introduction',
        'clauses',
    ];

    protected function casts(): array
    {
        return [
            'clauses' => 'array',
        ];
    }

    public static function current(): self
    {
        return static::query()->orderByDesc('id')->firstOrFail();
    }
}
