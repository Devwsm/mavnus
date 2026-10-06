{{-- Deklarasi XML (tag pembuka "kurung-tanya-xml") sengaja dirakit lewat string: kalau short_open_tag
    aktif di server, menulisnya langsung dibaca PHP sebagai kode dan halaman ini error. --}}
{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($urls as $item)
        <url>
            <loc>{{ $item['url'] }}</loc>
            @if (!empty($item['lastmod']))
                <lastmod>{{ $item['lastmod'] }}</lastmod>
            @endif
            <changefreq>{{ $item['changefreq'] }}</changefreq>
            <priority>{{ $item['priority'] }}</priority>
        </url>
    @endforeach
</urlset>
