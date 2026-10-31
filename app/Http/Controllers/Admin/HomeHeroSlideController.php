<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeHeroSlide;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeHeroSlideController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureAdmin($request);
        $slides = HomeHeroSlide::orderBy('sort_order')->orderBy('id')->get();
        $vehicles = Vehicle::published()->with('primaryImage')->orderByDesc('id')->get();
        return view('admin.home-slides.index', compact('slides', 'vehicles'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin($request);
        $data = $this->validated($request);
        $data['image_path'] = $this->storeImage($request);
        $data['is_published'] = $request->boolean('is_published');
        HomeHeroSlide::create($data);
        return redirect()->route('admin.home-slides.index')->with('success', 'Homepage slide added.');
    }

    public function update(Request $request, HomeHeroSlide $slide)
    {
        $this->ensureAdmin($request);
        $data = $this->validated($request);
        $newImage = $this->storeImage($request);
        if ($newImage) {
            if ($slide->image_path) Storage::disk('public')->delete($slide->image_path);
            $data['image_path'] = $newImage;
        } elseif ($request->boolean('remove_image')) {
            if ($slide->image_path) Storage::disk('public')->delete($slide->image_path);
            $data['image_path'] = null;
        }
        $data['is_published'] = $request->boolean('is_published');
        $slide->update($data);
        return redirect()->route('admin.home-slides.index')->with('success', 'Homepage slide updated.');
    }

    public function destroy(Request $request, HomeHeroSlide $slide)
    {
        $this->ensureAdmin($request);
        if ($slide->image_path) Storage::disk('public')->delete($slide->image_path);
        $slide->delete();
        return redirect()->route('admin.home-slides.index')->with('success', 'Homepage slide removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'eyebrow' => ['required', 'string', 'max:120'],
            'title' => ['required', 'string', 'max:180'],
            'highlight' => ['nullable', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:500'],
            'primary_label' => ['required', 'string', 'max:80'],
            'primary_url' => ['nullable', 'string', 'max:255'],
            'secondary_label' => ['nullable', 'string', 'max:80'],
            'secondary_url' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:1000000'],
            'autoplay_seconds' => ['required', 'integer', 'min:3', 'max:30'],
            'transition_style' => ['required', 'string', 'in:fade,slide,zoom'],
            'offer_badge' => ['nullable', 'string', 'max:80'],
            'featured_vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }

    private function storeImage(Request $request): ?string
    {
        return $request->hasFile('image')
            ? $request->file('image')->store('home-hero-slides', 'public')
            : null;
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->is_admin, 403);
    }
}
