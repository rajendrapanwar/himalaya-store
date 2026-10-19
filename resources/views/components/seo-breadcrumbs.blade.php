@props(['items'])
<x-structured-data :data="[
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => collect($items)->values()->map(fn ($item, $index) => array_filter([
        '@type' => 'ListItem',
        'position' => $index + 1,
        'name' => $item['name'],
        'item' => $item['url'] ?? null,
    ]))->all(),
]" />
