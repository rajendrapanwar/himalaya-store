<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JournalPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class JournalPostController extends Controller
{
    public function index(Request $request)
    {
        $posts = JournalPost::query()
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($nested) => $nested
                ->where('title', 'like', '%'.$request->string('search')->trim().'%')
                ->orWhere('author_name', 'like', '%'.$request->string('search')->trim().'%')
                ->orWhere('category', 'like', '%'.$request->string('search')->trim().'%')))
            ->when($request->status === 'published', fn ($query) => $query->published())
            ->when($request->status === 'draft', fn ($query) => $query->where('is_published', false))
            ->when($request->status === 'scheduled', fn ($query) => $query->where('is_published', true)->where('published_at', '>', now()))
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.journals.index', compact('posts'));
    }

    public function create(Request $request)
    {
        return view('admin.journals.form', [
            'post' => new JournalPost([
                'author_name' => $request->user()->name,
                'published_at' => now(),
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title']);
        $data['is_published'] = $request->boolean('is_published');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['published_at'] = $data['is_published'] ? ($data['published_at'] ?: now()) : $data['published_at'];
        $data['featured_image'] = $request->file('featured_image')?->store('journal', 'public');

        JournalPost::create($data);

        return redirect()->route('admin.journals.index')->with('success', 'Journal article created.');
    }

    public function edit(JournalPost $journal)
    {
        return view('admin.journals.form', ['post' => $journal]);
    }

    public function update(Request $request, JournalPost $journal)
    {
        $data = $this->validated($request, $journal);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title'], $journal);
        $data['is_published'] = $request->boolean('is_published');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['published_at'] = $data['is_published'] ? ($data['published_at'] ?: now()) : $data['published_at'];

        if ($request->hasFile('featured_image')) {
            $this->deleteUploadedImage($journal->featured_image);
            $data['featured_image'] = $request->file('featured_image')->store('journal', 'public');
        }

        $journal->update($data);

        return redirect()->route('admin.journals.index')->with('success', 'Journal article updated.');
    }

    public function destroy(JournalPost $journal)
    {
        $this->deleteUploadedImage($journal->featured_image);
        $journal->delete();

        return redirect()->route('admin.journals.index')->with('success', 'Journal article deleted.');
    }

    private function validated(Request $request, ?JournalPost $post = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', Rule::unique('journal_posts', 'slug')->ignore($post)],
            'category' => ['nullable', 'string', 'max:80'],
            'author_name' => ['required', 'string', 'max:120'],
            'excerpt' => ['required', 'string', 'min:40', 'max:500'],
            'content' => ['required', 'string', 'min:200'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'image_alt' => ['nullable', 'string', 'max:180'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:170'],
            'published_at' => ['nullable', 'date'],
        ]);
    }

    private function uniqueSlug(string $value, ?JournalPost $except = null): string
    {
        $base = Str::slug($value) ?: 'journal-article';
        $slug = $base;
        $counter = 2;

        while (JournalPost::where('slug', $slug)->when($except, fn ($query) => $query->whereKeyNot($except->getKey()))->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }

    private function deleteUploadedImage(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'assets/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
