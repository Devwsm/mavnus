<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Lang;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Menjaga fitur ganti bahasa tetap 100% terpasang: tampilan default Indonesia, pilihan bahasa
 * tersimpan, dan semua teks lewat file lang/ (tidak ada key yang hilang di salah satu bahasa).
 */
class LocaleTest extends StoreTestCase
{
    public function test_default_language_is_indonesian_for_navbar_and_header(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('<html lang="id"', $html);
        $this->assertStringContainsString('Beranda', $html);
        $this->assertStringContainsString('Pakaian', $html);
        $this->assertStringContainsString('Aksesori', $html);
        $this->assertStringContainsString('Merchandise Resmi Whisnu Santika', $html); // alt banner + h1
        $this->assertStringNotContainsString('Open navigation menu', $html);
        $this->assertStringNotContainsString('Official Merchandise', $html);
    }

    public function test_switching_to_english_translates_navbar_and_header_and_is_remembered(): void
    {
        $this->get('/lang/en')->assertRedirect(route('home'));

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('<html lang="en"', $html);
        $this->assertStringContainsString('Open navigation menu', $html);
        $this->assertStringContainsString('Accessories', $html);
        $this->assertStringContainsString('Official Merchandise Whisnu Santika', $html);
        $this->assertStringNotContainsString('Merchandise Resmi', $html);
        $this->assertStringNotContainsString('Buka menu navigasi', $html);

        // Halaman lain ikut bahasa yang sama
        $this->get('/clothes')->assertOk()->assertSee('Original Mavnus clothes');
    }

    public function test_switching_back_to_indonesian_works(): void
    {
        $this->get('/lang/en');
        $this->get('/lang/id');

        $this->get('/')->assertOk()->assertSee('Buka menu navigasi');
    }

    public function test_unsupported_language_is_404(): void
    {
        $this->get('/lang/fr')->assertNotFound();
    }

    public function test_cookie_keeps_language_when_session_is_gone(): void
    {
        $this->withCookie('locale', 'en')->get('/')->assertOk()->assertSee('Open navigation menu');
    }

    public function test_indonesian_and_english_files_have_the_same_keys(): void
    {
        foreach (glob(lang_path('en/*.php')) as $file) {
            $name = basename($file);
            $en = $this->flatten(require $file);
            $id = $this->flatten(require lang_path("id/{$name}"));

            $this->assertSame([], array_values(array_diff(array_keys($en), array_keys($id))), "Key ada di en tapi tidak di id ({$name})");
            $this->assertSame([], array_values(array_diff(array_keys($id), array_keys($en))), "Key ada di id tapi tidak di en ({$name})");
        }
    }

    public function test_every_translation_key_used_in_code_exists_in_both_languages(): void
    {
        $missing = [];

        foreach ($this->sourceFiles() as $path) {
            $code = file_get_contents($path);

            if (! preg_match_all('/(?:\b__|\btrans|@lang)\(\s*[\'"]([A-Za-z_]+\.[A-Za-z0-9_.]*[A-Za-z0-9_])[\'"]/', $code, $m)) {
                continue;
            }

            foreach (array_unique($m[1]) as $key) {
                foreach (['id', 'en'] as $locale) {
                    if (! Lang::has($key, $locale, false)) {
                        $missing[] = "{$key} ({$locale}) <- " . str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
                    }
                }
            }
        }

        $this->assertSame([], $missing, "Key terjemahan belum ada:\n" . implode("\n", $missing));
    }

    /** @return array<string, mixed> */
    private function flatten(array $lines, string $prefix = ''): array
    {
        $out = [];

        foreach ($lines as $key => $value) {
            is_array($value)
                ? $out += $this->flatten($value, "{$prefix}{$key}.")
                : $out["{$prefix}{$key}"] = $value;
        }

        return $out;
    }

    /** @return iterable<string> */
    private function sourceFiles(): iterable
    {
        foreach ([resource_path('views'), app_path()] as $dir) {
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));

            foreach ($it as $file) {
                if ($file->isFile() && str_ends_with($file->getFilename(), '.php')) {
                    yield $file->getPathname();
                }
            }
        }
    }
}