@props(['categories', 'origins', 'prefix' => 'filter'])

@php
    $chevron = '<svg class="filter-chevron" viewBox="0 0 20 20" fill="none" stroke="currentColor" aria-hidden="true"><path d="m6 8 4 4 4-4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
@endphp

<form method="GET" action="{{ route('products.index') }}" class="catalogue-filter-form space-y-3.5">
    @if(request()->filled('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
    @if(request()->filled('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

    <details class="filter-group" open>
        <summary><span>Category</span>{!! $chevron !!}</summary>
        <div class="filter-group-body">
            <label for="{{ $prefix }}-category" class="field-label">Choose a collection</label>
            <select id="{{ $prefix }}-category" name="category" class="field-control catalogue-select">
                <option value="">All products ({{ $categories->sum('products_count') }})</option>
                @foreach($categories as $category)<option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }} ({{ $category->products_count }})</option>@endforeach
            </select>
        </div>
    </details>

    <details class="filter-group" open>
        <summary><span>Price range</span>{!! $chevron !!}</summary>
        <div class="filter-group-body grid grid-cols-2 gap-2.5">
            <div><label for="{{ $prefix }}-min-price" class="field-label">Minimum</label><div class="relative"><span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-brand-700">₹</span><input id="{{ $prefix }}-min-price" name="min_price" type="number" min="0" step="1" value="{{ request('min_price') }}" placeholder="0" class="field-control !pl-7"></div></div>
            <div><label for="{{ $prefix }}-max-price" class="field-label">Maximum</label><div class="relative"><span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-brand-700">₹</span><input id="{{ $prefix }}-max-price" name="max_price" type="number" min="0" step="1" value="{{ request('max_price') }}" placeholder="Any" class="field-control !pl-7"></div></div>
        </div>
    </details>

    <details class="filter-group" open>
        <summary><span>Availability & offers</span>{!! $chevron !!}</summary>
        <div class="filter-group-body space-y-3">
            <div><label for="{{ $prefix }}-availability" class="field-label">Stock status</label><select id="{{ $prefix }}-availability" name="availability" class="field-control catalogue-select"><option value="">All availability</option><option value="in_stock" @selected(request('availability') === 'in_stock')>In stock</option><option value="out_of_stock" @selected(request('availability') === 'out_of_stock')>Sold out</option></select></div>
            <div class="grid grid-cols-2 gap-2.5"><label class="filter-toggle"><input type="checkbox" name="sale" value="1" @checked(request()->boolean('sale'))><span>On sale</span></label><label class="filter-toggle"><input type="checkbox" name="featured" value="1" @checked(request()->boolean('featured'))><span>Featured</span></label></div>
        </div>
    </details>

    <details class="filter-group" open>
        <summary><span>Product details</span>{!! $chevron !!}</summary>
        <div class="filter-group-body space-y-3">
            <div><label for="{{ $prefix }}-origin" class="field-label">Origin</label><select id="{{ $prefix }}-origin" name="origin" class="field-control catalogue-select"><option value="">All regions</option>@foreach($origins as $origin)<option value="{{ $origin }}" @selected(request('origin') === $origin)>{{ $origin }}</option>@endforeach</select></div>
            <div><label for="{{ $prefix }}-weight" class="field-label">Pack size</label><select id="{{ $prefix }}-weight" name="weight" class="field-control catalogue-select">@foreach([''=>'Any size','under_250'=>'Under 250 g','250_499'=>'250–499 g','500_999'=>'500–999 g','1000_plus'=>'1 kg and above'] as $value=>$label)<option value="{{ $value }}" @selected(request('weight', '') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div><label for="{{ $prefix }}-rating" class="field-label">Customer rating</label><select id="{{ $prefix }}-rating" name="rating" class="field-control catalogue-select"><option value="">Any rating</option>@foreach([4,3,2] as $rating)<option value="{{ $rating }}" @selected((int) request('rating') === $rating)>★ {{ $rating }} & up</option>@endforeach</select></div>
        </div>
    </details>

    <div class="filter-actions sticky bottom-0 -mx-1 flex gap-2 px-1 pt-3"><button class="button-primary flex-1" type="submit"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="1.8" d="M4 5h16M7 12h10m-7 7h4"/></svg>Show results</button><a href="{{ route('products.index') }}" class="button-secondary">Reset</a></div>
</form>
