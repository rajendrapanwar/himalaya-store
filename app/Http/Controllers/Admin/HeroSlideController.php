<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class HeroSlideController extends Controller
{
    public function index()
    {
        return view('admin.hero-slides.index', [
            'slides' => HeroSlide::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.hero-slides.form', [
            'slide' => new HeroSlide([
                'is_active' => true,
                'text_theme' => 'light',
                'text_position' => 'left',
                'sort_order' => ((int) HeroSlide::max('sort_order')) + 10,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['image_path'] = $request->file('image')->store('hero-slides', 'public');
        $data['is_active'] = $request->boolean('is_active');
        HeroSlide::create($data);

        return redirect()->route('admin.hero-slides.index')->with('success', 'Homepage slide created.');
    }

    public function edit(HeroSlide $heroSlide)
    {
        return view('admin.hero-slides.form', ['slide' => $heroSlide]);
    }

    public function update(Request $request, HeroSlide $heroSlide)
    {
        $data = $this->validated($request, $heroSlide);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $this->deleteUploadedImage($heroSlide->image_path);
            $data['image_path'] = $request->file('image')->store('hero-slides', 'public');
        }

        $heroSlide->update($data);

        return redirect()->route('admin.hero-slides.index')->with('success', 'Homepage slide updated.');
    }

    public function destroy(HeroSlide $heroSlide)
    {
        $this->deleteUploadedImage($heroSlide->image_path);
        $heroSlide->delete();

        return redirect()->route('admin.hero-slides.index')->with('success', 'Homepage slide deleted.');
    }

    private function validated(Request $request, ?HeroSlide $slide = null): array
    {
        return $request->validate([
            'eyebrow' => ['nullable', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:500'],
            'image' => [$slide ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'image_alt' => ['required', 'string', 'max:180'],
            'primary_label' => ['nullable', 'required_with:primary_url', 'string', 'max:60'],
            'primary_url' => ['nullable', 'required_with:primary_label', 'string', 'max:500', function ($attribute, $value, $fail) {
                if (! $this->isSafeLink($value)) {
                    $fail('The primary link must be a site path beginning with / or a complete URL.');
                }
            }],
            'secondary_label' => ['nullable', 'required_with:secondary_url', 'string', 'max:60'],
            'secondary_url' => ['nullable', 'required_with:secondary_label', 'string', 'max:500', function ($attribute, $value, $fail) {
                if (! $this->isSafeLink($value)) {
                    $fail('The secondary link must be a site path beginning with / or a complete URL.');
                }
            }],
            'text_theme' => ['required', Rule::in(['light', 'dark'])],
            'text_position' => ['required', Rule::in(['left', 'center', 'right'])],
            'sort_order' => ['required', 'integer', 'min:0', 'max:10000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', $request->filled('starts_at') ? 'after:starts_at' : 'after:now'],
        ]);
    }

    private function deleteUploadedImage(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'assets/')) {
            Storage::disk('public')->delete($path);
        }
    }

    private function isSafeLink(string $value): bool
    {
        if (str_starts_with($value, '/') && ! str_starts_with($value, '//')) {
            return true;
        }

        return filter_var($value, FILTER_VALIDATE_URL) !== false
            && in_array(parse_url($value, PHP_URL_SCHEME), ['http', 'https'], true);
    }
}
