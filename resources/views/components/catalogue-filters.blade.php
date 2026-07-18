@props(['categories', 'origins', 'prefix' => 'filter'])
<form method="GET" action="{{ route('products.index') }}" class="space-y-6">
    @if(request()->filled('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
    @if(request()->filled('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

    <div>
        <label for="{{ $prefix }}-category" class="text-xs font-black uppercase tracking-[.14em] text-brand-950">Category</label>
        <select id="{{ $prefix }}-category" name="category" class="field-control mt-3">
            <option value="">All products ({{ $categories->sum('products_count') }})</option>
            @foreach($categories as $category)
                <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }} ({{ $category->products_count }})</option>
            @endforeach
        </select>
    </div>

    <fieldset class="border-t border-stone-200 pt-5"><legend class="text-xs font-black uppercase tracking-[.14em] text-brand-950">Price range</legend><div class="mt-3 grid grid-cols-2 gap-2"><div><label for="{{ $prefix }}-min-price" class="sr-only">Minimum price</label><div class="relative"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-stone-500">₹</span><input id="{{ $prefix }}-min-price" name="min_price" type="number" min="0" step="1" value="{{ request('min_price') }}" placeholder="Min" class="field-control !pl-7"></div></div><div><label for="{{ $prefix }}-max-price" class="sr-only">Maximum price</label><div class="relative"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-stone-500">₹</span><input id="{{ $prefix }}-max-price" name="max_price" type="number" min="0" step="1" value="{{ request('max_price') }}" placeholder="Max" class="field-control !pl-7"></div></div></div></fieldset>

    <fieldset class="border-t border-stone-200 pt-5"><legend class="text-xs font-black uppercase tracking-[.14em] text-brand-950">Availability</legend><div class="mt-3 space-y-2">@foreach([''=>'All availability','in_stock'=>'In stock','out_of_stock'=>'Sold out'] as $value=>$label)<label class="filter-choice"><input type="radio" name="availability" value="{{ $value }}" @checked(request('availability','') === $value)><span>{{ $label }}</span></label>@endforeach</div></fieldset>

    <fieldset class="border-t border-stone-200 pt-5"><legend class="text-xs font-black uppercase tracking-[.14em] text-brand-950">Offers</legend><div class="mt-3 space-y-2"><label class="filter-choice"><input type="checkbox" name="sale" value="1" @checked(request()->boolean('sale'))><span>On sale</span></label><label class="filter-choice"><input type="checkbox" name="featured" value="1" @checked(request()->boolean('featured'))><span>Featured picks</span></label></div></fieldset>

    <div class="border-t border-stone-200 pt-5"><label for="{{ $prefix }}-origin" class="text-xs font-black uppercase tracking-[.14em] text-brand-950">Origin</label><select id="{{ $prefix }}-origin" name="origin" class="field-control mt-3"><option value="">All regions</option>@foreach($origins as $origin)<option value="{{ $origin }}" @selected(request('origin') === $origin)>{{ $origin }}</option>@endforeach</select></div>

    <div class="border-t border-stone-200 pt-5">
        <label for="{{ $prefix }}-weight" class="text-xs font-black uppercase tracking-[.14em] text-brand-950">Pack size</label>
        <select id="{{ $prefix }}-weight" name="weight" class="field-control mt-3">
            @foreach([''=>'Any size','under_250'=>'Under 250 g','250_499'=>'250–499 g','500_999'=>'500–999 g','1000_plus'=>'1 kg and above'] as $value=>$label)
                <option value="{{ $value }}" @selected(request('weight', '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="border-t border-stone-200 pt-5">
        <label for="{{ $prefix }}-rating" class="text-xs font-black uppercase tracking-[.14em] text-brand-950">Customer rating</label>
        <select id="{{ $prefix }}-rating" name="rating" class="field-control mt-3">
            <option value="">Any rating</option>
            @foreach([4,3,2] as $rating)
                <option value="{{ $rating }}" @selected((int) request('rating') === $rating)>★ {{ $rating }} &amp; up</option>
            @endforeach
        </select>
    </div>

    <div class="sticky bottom-0 -mx-1 flex gap-2 border-t border-stone-200 bg-white/95 px-1 pt-4 backdrop-blur"><button class="button-primary flex-1" type="submit">Apply filters</button><a href="{{ route('products.index', array_filter(['category' => request('category')])) }}" class="button-secondary">Reset</a></div>
</form>
