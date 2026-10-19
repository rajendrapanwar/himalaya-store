{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/">
<channel>
<title>Pahadi Roots Journal</title>
<link>{{ route('blog') }}</link>
<description>Practical guides and stories about Himalayan ingredients, sourcing and mountain food traditions.</description>
<language>{{ config('seo.language') }}</language>
<atom:link href="{{ route('blog.feed') }}" rel="self" type="application/rss+xml" />
@foreach($posts as $post)
<item>
<title>{{ $post->title }}</title>
<link>{{ route('blog.show', $post->slug) }}</link>
<guid isPermaLink="true">{{ route('blog.show', $post->slug) }}</guid>
<pubDate>{{ $post->published_at->toRssString() }}</pubDate>
<author>{{ config('seo.organization.email') }} ({{ $post->author_name }})</author>
@if($post->category)<category>{{ $post->category }}</category>@endif
<description>{{ $post->excerpt }}</description>
</item>
@endforeach
</channel>
</rss>
