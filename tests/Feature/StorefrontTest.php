<?php

namespace Tests\Feature;

use App\Models\NewsletterSubscriber;

class StorefrontTest extends StoreTestCase
{
    public function test_public_pages_render(): void
    {
        $c = $this->makeClothes(['M' => 5]);
        $a = $this->makeAccessory(5);

        foreach (
            [
                '/',
                '/clothes',
                '/accessoris',
                '/info',
                '/login',
                '/register',
                '/crew-portal',
                "/clothes/{$c->slug}",
                "/accessoris/{$a->slug}"
            ] as $url
        ) {
            $this->get($url)->assertOk();
        }
    }

    public function test_unknown_product_slug_is_404(): void
    {
        $this->get('/clothes/tidak-ada')->assertNotFound();
    }

    public function test_scheduled_product_page_is_404_and_hidden_from_listing(): void
    {
        $p = $this->makeAccessory(5, 25000, ['name' => 'Rahasia Rilis', 'published_at' => now()->addDays(3)]);

        $this->get("/accessoris/{$p->slug}")->assertNotFound();
        $this->get('/accessoris')->assertDontSee('Rahasia Rilis');
        $this->getJson('/search?q=Rahasia')->assertJson(['results' => []]);
    }

    public function test_price_filter(): void
    {
        $this->makeClothes(['M' => 5], 100000, ['name' => 'Murah']);
        $this->makeClothes(['M' => 5], 500000, ['name' => 'Mahal']);

        $this->get('/clothes?price_min=300000')->assertSee('Mahal')->assertDontSee('Murah');
    }

    public function test_price_filter_garbage_does_not_cause_500(): void
    {
        $this->get('/clothes?price_min[]=1')->assertOk();
        $this->get('/accessoris?price_max=abc')->assertOk();
        $this->get('/clothes?price_min=' . str_repeat('9', 40))->assertOk();
    }

    public function test_search_array_param_does_not_cause_500(): void
    {
        $this->getJson('/search?q[]=x')->assertOk()->assertJson(['results' => []]);
    }

    public function test_search_treats_percent_as_literal(): void
    {
        $this->makeAccessory(5, 25000, ['name' => 'Stiker Biasa']);

        $this->getJson('/search?q=%25%25')->assertJson(['results' => []]);
    }

    public function test_product_name_is_html_escaped_on_the_page(): void
    {
        $p = $this->makeAccessory(5, 25000, ['name' => '<script>alert(1)</script>']);

        $this->get("/accessoris/{$p->slug}")->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_layout_defines_escape_helper_before_page_scripts(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertStringContainsString('function escapeHtml', $html);
        $this->assertLessThan(strpos($html, 'function openCart'), strpos($html, 'function escapeHtml'));
    }

    // ---------- newsletter ----------

    public function test_newsletter_subscribe_stores_lowercased_email(): void
    {
        $this->postJson('/newsletter', ['email' => 'Fan@Example.COM'])->assertOk()->assertJsonStructure(['message']);

        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'fan@example.com']);
    }

    public function test_newsletter_duplicate_gets_same_response_and_no_second_row(): void
    {
        $first = $this->postJson('/newsletter', ['email' => 'a@example.com']);
        $second = $this->postJson('/newsletter', ['email' => 'a@example.com']);

        $second->assertOk();
        $this->assertSame($first->json('message'), $second->json('message'));
        $this->assertSame(1, NewsletterSubscriber::count());
    }

    public function test_newsletter_validates_email(): void
    {
        $this->postJson('/newsletter', ['email' => 'bukan-email'])->assertStatus(422)->assertJsonValidationErrors('email');
        $this->postJson('/newsletter', [])->assertStatus(422);
    }

    public function test_newsletter_honeypot_is_silently_ignored(): void
    {
        $this->postJson('/newsletter', ['email' => 'bot@example.com', 'website' => 'http://spam.example'])->assertOk();

        $this->assertDatabaseCount('newsletter_subscribers', 0);
    }

    public function test_newsletter_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/newsletter', ['email' => "u{$i}@example.com"])->assertOk();
        }

        $this->postJson('/newsletter', ['email' => 'u6@example.com'])->assertStatus(429);
    }

    // ---------- SEO ----------

    public function test_robots_txt_has_absolute_sitemap_url(): void
    {
        $res = $this->get('/robots.txt')->assertOk();

        $this->assertStringContainsString('Sitemap: http', $res->getContent());
        $this->assertStringContainsString('Disallow: /dashboard', $res->getContent());
        $this->assertStringContainsString('text/plain', $res->headers->get('Content-Type'));
    }

    public function test_sitemap_lists_clothes_and_accessories_but_not_scheduled(): void
    {
        $c = $this->makeClothes(['M' => 5]);
        $a = $this->makeAccessory(5);
        $hidden = $this->makeAccessory(5, 1000, ['published_at' => now()->addDay()]);

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString("/clothes/{$c->slug}", $xml);
        $this->assertStringContainsString("/accessoris/{$a->slug}", $xml);
        $this->assertStringNotContainsString($hidden->slug, $xml);
    }

    // ---------- header keamanan ----------

    public function test_security_headers_are_sent(): void
    {
        $res = $this->get('/')->assertOk();

        $res->assertHeader('X-Content-Type-Options', 'nosniff');
        $res->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $res->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $res->assertHeader('Cross-Origin-Opener-Policy');
        $this->assertStringContainsString("object-src 'none'", $res->headers->get('Content-Security-Policy'));
        $this->assertStringContainsString("frame-ancestors 'self'", $res->headers->get('Content-Security-Policy'));
    }

    public function test_hsts_only_over_https(): void
    {
        $this->get('/')->assertHeaderMissing('Strict-Transport-Security');
        $this->get('https://localhost/')->assertHeader('Strict-Transport-Security');
    }

    public function test_security_headers_also_on_json_and_404(): void
    {
        $this->getJson('/cart')->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->get('/halaman-tidak-ada')->assertNotFound()->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    // ---------- regresi: popup error di layout selain layout utama ----------

    public function test_error_popup_helper_exists_on_pages_with_other_layouts(): void
    {
        // login/daftar/crew-portal memakai bare-layout yang tidak punya escapeHtml bawaan.
        // Tanpa fallback di alerts.blade.php, popup error gagal login tidak akan muncul.
        $this->from('/login')->post('/login', ['email' => 'x@example.com', 'password' => 'salah-salah']);

        foreach (['/login', '/register', '/crew-portal'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertStringContainsString('window.escapeHtml', $html, "escapeHtml tidak tersedia di {$url}");
        }
    }

    // ---------- gambar teroptimasi punya fallback ----------

    public function test_img_helper_falls_back_when_webp_missing_or_empty(): void
    {
        $dir = public_path('aset/_test');
        @mkdir($dir, 0777, true);
        file_put_contents("{$dir}/kosong.webp", '');
        file_put_contents("{$dir}/asli.png", 'x');
        file_put_contents("{$dir}/ada.webp", 'x');

        try {
            $this->assertStringEndsWith('asli.png', \App\Support\Img::url('aset/_test/tidakada.webp', 'aset/_test/asli.png'));
            $this->assertStringEndsWith('asli.png', \App\Support\Img::url('aset/_test/kosong.webp', 'aset/_test/asli.png'));
            $this->assertStringEndsWith('ada.webp', \App\Support\Img::url('aset/_test/ada.webp', 'aset/_test/asli.png'));
        } finally {
            array_map('unlink', glob("{$dir}/*"));
            rmdir($dir);
        }
    }

    public function test_robots_txt_route_wins_over_static_file_is_absolute(): void
    {
        $this->assertStringContainsString('Sitemap: http', $this->get('/robots.txt')->getContent());
    }
}