<?php

namespace Tests\Feature;

class ExampleTest extends StoreTestCase
{
    /**
     * Halaman beranda harus terbuka. Memakai StoreTestCase (RefreshDatabase) karena
     * middleware TrackVisit menulis ke tabel `visits` di setiap kunjungan.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->get('/')->assertStatus(200);
    }
}