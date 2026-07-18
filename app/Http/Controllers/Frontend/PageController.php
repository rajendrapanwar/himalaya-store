<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageReceived;
use App\Models\Bundle;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\JournalPost;
use App\Models\Product;
use App\Services\StorefrontCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function about()
    {
        return view('frontend.pages.about');
    }

    public function contact()
    {
        return view('frontend.pages.contact');
    }

    public function sendContact(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:120', 'email' => 'required|email|max:255', 'phone' => 'nullable|string|max:20', 'subject' => 'required|string|max:120', 'message' => 'required|string|max:3000', 'website' => 'nullable|max:0']);
        unset($data['website']);
        $message = ContactMessage::create($data + ['user_id' => $request->user()?->id]);
        Mail::to(config('services.support_email'))->queue(new ContactMessageReceived($message));

        return back()->with('success', 'Thank you — your message has been received. We will reply shortly.');
    }

    public function blog(Request $request)
    {
        $posts = JournalPost::published()
            ->when($request->filled('topic'), fn ($query) => $query->where('category', $request->string('topic')))
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        $topics = JournalPost::published()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        return view('frontend.pages.blog', compact('posts', 'topics'));
    }

    public function post(string $slug)
    {
        $post = JournalPost::published()->where('slug', $slug)->firstOrFail();
        $relatedPosts = JournalPost::published()->whereKeyNot($post->getKey())
            ->when($post->category, fn ($query) => $query->where('category', $post->category))
            ->latest('published_at')->take(3)->get();

        if ($relatedPosts->count() < 3) {
            $relatedPosts = $relatedPosts->concat(JournalPost::published()
                ->whereKeyNot($post->getKey())
                ->whereNotIn('id', $relatedPosts->pluck('id'))
                ->latest('published_at')->take(3 - $relatedPosts->count())->get());
        }

        return view('frontend.pages.post', compact('post', 'relatedPosts'));
    }

    public function sitemap()
    {
        $entries = Cache::remember('storefront:sitemap:v'.StorefrontCache::version(), now()->addHour(), fn () => [
            'products' => Product::with('images')->where('status', true)->get(['id', 'name', 'slug', 'updated_at']),
            'categories' => Category::where('status', true)->whereHas('products', fn ($query) => $query->where('status', true))->get(['slug', 'image', 'updated_at']),
            'bundles' => Bundle::where('is_active', true)->get(['updated_at']),
            'posts' => JournalPost::published()->latest('updated_at')->get(['title', 'slug', 'featured_image', 'updated_at']),
        ]);

        return response()->view('sitemap', $entries)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function robots()
    {
        $content = implode("\n", [
            'User-agent: *', 'Allow: /', 'Disallow: /admin/', 'Disallow: /cart',
            'Disallow: /checkout', 'Disallow: /account', 'Disallow: /my-orders',
            'Disallow: /wishlist', 'Sitemap: '.route('sitemap'), '',
        ]);

        return response($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
