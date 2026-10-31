<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OwnershipGuide;
use App\Models\OwnershipGuideStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OwnershipGuideController extends Controller
{
    public function edit(Request $request): View
    {
        $this->ensureAdmin($request);
        $guide = OwnershipGuide::with('steps')->firstOrFail();

        return view('admin.ownership-guide.edit', compact('guide'));
    }

    public function preview(Request $request): View
    {
        $this->ensureAdmin($request);
        $guide = OwnershipGuide::with('steps')->firstOrFail();

        return view('ownership-guide.show', [
            'guide' => $guide,
            'preview' => true,
        ]);
    }

    public function update(Request $request)
    {
        $this->ensureAdmin($request);
        $guide = OwnershipGuide::firstOrFail();
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'introduction' => ['required', 'string', 'max:5000'],
        ]);

        $guide->update([
            ...$validated,
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()
            ->route('admin.ownership-guide.edit')
            ->with('success', 'Ownership guide settings saved.');
    }

    public function storeStep(Request $request)
    {
        $this->ensureAdmin($request);
        $guide = OwnershipGuide::firstOrFail();
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'body' => ['required', 'string', 'max:10000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:1000000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $imagePath = $request->file('image')?->store('ownership-guide', 'public');

        if ($request->hasFile('image') && $imagePath === false) {
            throw new \RuntimeException('Unable to save the ownership guide image.');
        }

        try {
            $guide->steps()->create([
                'title' => $validated['title'],
                'body' => $validated['body'],
                'sort_order' => $validated['sort_order'],
                'is_published' => $request->boolean('is_published'),
                'image_path' => $imagePath,
            ]);
        } catch (\Throwable $exception) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.ownership-guide.edit')
            ->with('success', 'Guide step added.');
    }

    public function updateStep(Request $request, OwnershipGuideStep $step)
    {
        $this->ensureAdmin($request);
        abort_unless($step->ownership_guide_id === OwnershipGuide::firstOrFail()->id, 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'body' => ['required', 'string', 'max:10000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:1000000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        $oldImagePath = $step->image_path;
        $newImagePath = $request->file('image')?->store('ownership-guide', 'public');

        if ($request->hasFile('image') && $newImagePath === false) {
            throw new \RuntimeException('Unable to save the ownership guide image.');
        }

        $imagePath = $newImagePath ?: (
            $request->boolean('remove_image') ? null : $oldImagePath
        );

        try {
            $step->update([
                'title' => $validated['title'],
                'body' => $validated['body'],
                'sort_order' => $validated['sort_order'],
                'is_published' => $request->boolean('is_published'),
                'image_path' => $imagePath,
            ]);
        } catch (\Throwable $exception) {
            if ($newImagePath) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }

        if ($oldImagePath && $imagePath !== $oldImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return redirect()
            ->route('admin.ownership-guide.edit')
            ->with('success', 'Guide step updated.');
    }

    public function destroyStep(Request $request, OwnershipGuideStep $step)
    {
        $this->ensureAdmin($request);
        abort_unless($step->ownership_guide_id === OwnershipGuide::firstOrFail()->id, 404);
        $imagePath = $step->image_path;
        $step->delete();

        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()
            ->route('admin.ownership-guide.edit')
            ->with('success', 'Guide step deleted.');
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->is_admin, 403);
    }
}
