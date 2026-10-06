<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Test tidak membutuhkan hasil `npm run build` / Vite dev server (file public/hot ikut ke-zip
        // dan akan membuat @vite menunjuk ke http://[::1]:5173 yang tidak jalan saat test).
        $this->withoutVite();
    }
}