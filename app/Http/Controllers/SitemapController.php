<?php

namespace App\Http\Controllers;

use App\Models\product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * robots.txt dibuat dinamis supaya baris Sitemap berisi URL ABSOLUT. Spesifikasinya
     * mewajibkan URL penuh (https://domain/sitemap.xml); versi file statis dengan
     * "/sitemap.xml" ditandai tidak valid oleh Lighthouse & diabaikan sebagian crawler.
     * File public/robots.txt harus dihapus supaya route ini yang melayani.
     */
    public function robots(): Response
    {
        $body = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /dashboard',
            'Disallow: /crew-portal',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /account',
            'Disallow: /cart',
            'Disallow: /order',
            'Disallow: /search',
            'Disallow: /shipping',
            '',
            'Sitemap: ' . route('sitemap'),
            '',
        ]);

        return response($body, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    /**
     * Generate sitemap.xml secara dinamis: halaman statis + tiap produk
     * (clothes & accessories, keduanya punya halaman detail publik).
     */
    public function index(): Response
    {
        $staticUrls = [
            ['url' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => route('clothes'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('accessoris'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('footer'), 'priority' => '0.3', 'changefreq' => 'monthly'],
        ];

        $productUrls = product::query()
            ->whereIn('category', ['clothes', 'accessories'])
            ->active()
            ->select('slug', 'category', 'updated_at')
            ->get()
            ->map(fn($product) => [
                'url'        => route($product->category === 'clothes' ? 'product_detail.clothes' : 'product_detail.accessories', $product->slug),
                'priority'   => '0.8',
                'changefreq' => 'weekly',
                'lastmod'    => $product->updated_at?->toAtomString(),
            ]);

        $urls = collect($staticUrls)->concat($productUrls);

        $xml = view('sitemap', compact('urls'))->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}