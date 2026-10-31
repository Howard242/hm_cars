<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerBrokerageTerms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SellerBrokerageTermsController extends Controller
{
    public function edit(Request $request)
    {
        $this->ensureAdmin($request);

        $terms = SellerBrokerageTerms::current();
        $versions = SellerBrokerageTerms::query()->orderByDesc('id')->get();

        return view('admin.seller-terms.edit', compact('terms', 'versions'));
    }

    public function update(Request $request)
    {
        $this->ensureAdmin($request);
        $current = SellerBrokerageTerms::current();

        $validated = $request->validate([
            'current_version' => ['required', 'string', Rule::in([$current->version])],
            'title' => ['required', 'string', 'max:180'],
            'introduction' => ['required', 'string', 'max:2000'],
            'clauses' => ['required', 'array', 'min:1', 'max:20'],
            'clauses.*.title' => ['required', 'string', 'max:180'],
            'clauses.*.body' => ['required', 'string', 'max:5000'],
        ]);

        $newTerms = DB::transaction(function () use ($validated): SellerBrokerageTerms {
            $latest = SellerBrokerageTerms::query()
                ->orderByDesc('id')
                ->lockForUpdate()
                ->firstOrFail();

            abort_if($latest->version !== $validated['current_version'], 409);

            [$major, $minor] = array_pad(
                array_map('intval', explode('.', $latest->version, 2)),
                2,
                0
            );

            return SellerBrokerageTerms::create([
                'version' => $major.'.'.($minor + 1),
                'title' => $validated['title'],
                'introduction' => $validated['introduction'],
                'clauses' => array_values($validated['clauses']),
            ]);
        });

        return redirect()
            ->route('admin.seller-terms.edit')
            ->with('success', 'Seller agreement saved as version '.$newTerms->version.'.');
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->is_admin, 403);
    }
}
