@extends('layouts.admin')
@section('title', 'Journal')
@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><p class="text-sm font-bold uppercase tracking-widest text-brand-700">Editorial</p><h2 class="mt-1 text-3xl font-black">Journal articles</h2><p class="mt-2 text-sm text-stone-500">Create, schedule and maintain useful stories for the storefront.</p></div>
        <a href="{{ route('admin.journals.create') }}" class="button-primary">+ New article</a>
    </div>

    <form method="GET" class="grid gap-3 rounded-xl border border-stone-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_190px_auto]">
        <label class="sr-only" for="journalSearch">Search articles</label><input id="journalSearch" name="search" value="{{ request('search') }}" placeholder="Search title, author or topic…" class="field-control">
        <label class="sr-only" for="journalStatus">Publishing status</label><select id="journalStatus" name="status" class="field-control"><option value="">All statuses</option><option value="published" @selected(request('status') === 'published')>Published</option><option value="scheduled" @selected(request('status') === 'scheduled')>Scheduled</option><option value="draft" @selected(request('status') === 'draft')>Drafts</option></select>
        <button class="button-secondary" type="submit">Filter</button>
    </form>

    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto"><table><thead><tr><th>Article</th><th>Topic</th><th>Author</th><th>Status</th><th>Updated</th><th class="text-right">Actions</th></tr></thead><tbody>
        @forelse($posts as $post)
            <tr>
                <td><div class="flex min-w-[300px] items-center gap-3">@if($post->image_url)<img src="{{ $post->image_url }}" alt="" class="h-14 w-20 rounded-lg object-cover">@else<div class="grid h-14 w-20 place-items-center rounded-lg bg-brand-50 text-brand-300">Image</div>@endif<div><p class="font-black text-stone-950">{{ $post->title }}</p><p class="mt-1 text-xs text-stone-500">{{ $post->reading_time }} min read @if($post->is_featured) · Featured @endif</p></div></div></td>
                <td>{{ $post->category ?: 'General' }}</td><td>{{ $post->author_name }}</td>
                <td>@if(!$post->is_published)<span class="rounded-full bg-stone-100 px-2.5 py-1 text-xs font-black text-stone-600">Draft</span>@elseif($post->published_at?->isFuture())<span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-black text-amber-800">Scheduled</span>@else<span class="rounded-full bg-brand-100 px-2.5 py-1 text-xs font-black text-brand-800">Published</span>@endif</td>
                <td class="text-xs text-stone-500">{{ $post->updated_at->format('d M Y') }}</td>
                <td><div class="flex justify-end gap-2">@if($post->is_published && $post->published_at?->isPast())<a href="{{ route('blog.show', $post->slug) }}" target="_blank" rel="noopener" class="button-secondary !min-h-9 !px-3 !py-1.5">View</a>@endif<a href="{{ route('admin.journals.edit', $post) }}" class="button-secondary !min-h-9 !px-3 !py-1.5">Edit</a><form method="POST" action="{{ route('admin.journals.destroy', $post) }}" onsubmit="return confirm('Delete this journal article permanently?')">@csrf @method('DELETE')<button class="min-h-9 rounded-lg border border-red-200 px-3 text-xs font-black text-red-700 hover:bg-red-50">Delete</button></form></div></td>
            </tr>
        @empty<tr><td colspan="6" class="p-12 text-center text-stone-500">No journal articles match these filters.</td></tr>@endforelse
        </tbody></table></div>
    </div>
    {{ $posts->links() }}
</div>
@endsection
