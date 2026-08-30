<?php

namespace App\Http\Controllers;

use App\Models\product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate sitemap.xml secara dinamis: halaman statis + tiap produk
     * clothes & accessories yang aktif.
     */
    public function index(): Response
    {
        $staticUrls = [['url' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'], ['url' => route('clothes'), 'priority' => '0.9', 'changefreq' => 'daily'], ['url' => route('accessoris'), 'priority' => '0.9', 'changefreq' => 'daily'], ['url' => route('footer'), 'priority' => '0.3', 'changefreq' => 'monthly']];

        $clothesUrls = product::clothesCategory()->active()->select('slug', 'updated_at')->get()->map(
            fn($product) => [
                'url' => route('product_detail.clothes', $product->slug),
                'priority' => '0.8',
                'changefreq' => 'weekly',
                'lastmod' => $product->updated_at?->toAtomString(),
            ],
        );

        $accessoriesUrls = product::accessoriesCategory()->active()->select('slug', 'updated_at')->get()->map(
            fn($product) => [
                'url' => route('product_detail.accessories', $product->slug),
                'priority' => '0.8',
                'changefreq' => 'weekly',
                'lastmod' => $product->updated_at?->toAtomString(),
            ],
        );

        $urls = collect($staticUrls)->concat($clothesUrls)->concat($accessoriesUrls);

        $xml = view('sitemap', compact('urls'))->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
