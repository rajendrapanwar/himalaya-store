@extends($admin ? 'layouts.admin' : 'layouts.app')
@section('title', 'Notifications')
@section('robots', 'noindex, nofollow')
@section('content')
<div class="mx-auto max-w-5xl {{ $admin ? '' : 'py-6 sm:py-10' }}">
    <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
        <div><p class="eyebrow">{{ $admin ? 'Operations centre' : 'Your account' }}</p><h2 class="font-display mt-2 text-4xl font-semibold text-brand-950">Notifications</h2><p class="mt-2 text-sm text-stone-500">Order updates, account activity and messages that need your attention.</p></div>
        @if(auth()->user()->unreadNotifications()->exists())<form method="POST" action="{{ route($routePrefix.'.read-all') }}">@csrf @method('PATCH')<button class="button-secondary">Mark all as read</button></form>@endif
    </div>

    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        @forelse($notifications as $notification)
            @php($data = $notification->data)
            <article class="flex gap-4 border-b border-stone-100 p-5 last:border-0 sm:p-6 {{ $notification->read_at ? 'bg-white' : 'bg-brand-50/70' }}">
                <span class="mt-1 grid h-10 w-10 shrink-0 place-items-center rounded-full {{ $notification->read_at ? 'bg-stone-100 text-stone-500' : 'bg-brand-800 text-white' }}" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>
                </span>
                <div class="min-w-0 flex-1"><div class="flex flex-wrap items-start justify-between gap-2"><div><p class="text-xs font-black uppercase tracking-wider text-saffron">{{ str($data['category'] ?? 'account')->headline() }}</p><h3 class="mt-1 text-lg font-black text-stone-950">{{ $data['title'] ?? 'Account update' }}</h3></div><time class="text-xs text-stone-400" datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->diffForHumans() }}</time></div><p class="mt-2 text-sm leading-6 text-stone-600">{{ $data['message'] ?? '' }}</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @if(!$notification->read_at)<form method="POST" action="{{ route($routePrefix.'.update', $notification->id) }}">@csrf @method('PATCH')<input type="hidden" name="open" value="1"><button class="button-primary !px-4 !py-2 text-xs">{{ filled($data['action_url'] ?? null) ? 'Open' : 'Mark as read' }}</button></form>
                        @elseif(filled($data['action_url'] ?? null))<a href="{{ $data['action_url'] }}" class="button-secondary !px-4 !py-2 text-xs">Open</a>@endif
                        <form method="POST" action="{{ route($routePrefix.'.destroy', $notification->id) }}">@csrf @method('DELETE')<button class="rounded-lg px-3 py-2 text-xs font-bold text-stone-500 hover:bg-stone-100 hover:text-red-700">Remove</button></form>
                    </div>
                </div>
            </article>
        @empty
            <div class="p-14 text-center"><span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-brand-50 text-brand-700"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg></span><h3 class="mt-4 text-lg font-black">You are all caught up</h3><p class="mt-1 text-sm text-stone-500">New updates will appear here.</p></div>
        @endforelse
    </div>
    <div class="mt-5">{{ $notifications->links() }}</div>
</div>
@endsection
