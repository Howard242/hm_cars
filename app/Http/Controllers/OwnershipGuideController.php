<?php

namespace App\Http\Controllers;

use App\Models\OwnershipGuide;
use Illuminate\View\View;

class OwnershipGuideController extends Controller
{
    public function show(): View
    {
        $guide = OwnershipGuide::with([
            'steps' => fn ($query) => $query->where('is_published', true),
        ])->where('is_published', true)->firstOrFail();

        return view('ownership-guide.show', [
            'guide' => $guide,
            'preview' => false,
        ]);
    }
}
