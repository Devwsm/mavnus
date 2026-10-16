<?php

namespace Tests\Feature;

use App\Exports\OrdersExport;
use App\Models\Order;
use Carbon\Carbon;

class OrderInvoiceTest extends StoreTestCase
{
    private function owner(): static
    {
        return $this->actingAsStaff($this->makeStaff('owner'));
    }

    private function orderFor(string $name, array $attrs = []): Order
    {
        return $this->makeOrder($this->makeAccessory(5), null, 1, array_merge(['customer_name' => $name], $attrs));
    }

    private function createdAt(Order $order, string $datetime): Order
    {
        $order->forceFill(['created_at' => Carbon::parse($datetime)])->save();

        return $order;
    }

    // ---------- invoice satu pesanan ----------

    public function test_single_invoice_preview_shows_only_that_order(): void
    {
        $siti = $this->orderFor('Siti Aminah', ['customer_email' => 'siti@contoh.test']);
        $budi = $this->orderFor('Budi Santoso');

        $this->owner()->get(route('dashboard.orders.invoice', $siti))
            ->assertOk()
            ->assertSee($siti->order_number)
            ->assertSee('Siti Aminah')
            ->assertSee('siti@contoh.test')
            ->assertDontSee($budi->order_number)
            ->assertDontSee('Budi Santoso');
    }

    public function test_single_invoice_pdf_downloads_a_pdf(): void
    {
        $order = $this->orderFor('Siti');

        $res = $this->owner()->get(route('dashboard.orders.invoice.pdf', $order))->assertOk();

        $this->assertStringContainsString('pdf', (string) $res->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', $res->getContent());
    }

    public function test_invoice_of_expired_order_is_marked_expired(): void
    {
        $order = $this->orderFor('Siti', ['status' => 'cancelled', 'payment_status' => 'expired']);

        $this->owner()->get(route('dashboard.orders.invoice', $order))->assertOk()->assertSee('Kedaluwarsa');
    }

    public function test_single_invoice_is_limited_to_order_staff_and_owner(): void
    {
        $order = $this->orderFor('Siti');

        $this->get(route('dashboard.orders.invoice', $order))->assertRedirect(route('crew.login'));
        $this->get(route('dashboard.orders.invoice.pdf', $order))->assertRedirect(route('crew.login'));

        $this->actingAsStaff($this->makeStaff('admin_produk'))
            ->get(route('dashboard.orders.invoice', $order))->assertRedirect(route('dashboard'));

        $this->actingAsStaff($this->makeStaff('staff_pesanan', true, 'crew2'))
            ->get(route('dashboard.orders.invoice', $order))->assertOk();
    }

    public function test_order_detail_page_links_to_its_invoice(): void
    {
        $order = $this->orderFor('Siti');

        $this->owner()->get(route('dashboard.orders.show', $order))
            ->assertOk()
            ->assertSee(route('dashboard.orders.invoice', $order), false)
            ->assertSee(route('dashboard.orders.invoice.pdf', $order), false);
    }

    // ---------- export dengan filter tanggal / status ----------

    public function test_bulk_invoice_preview_respects_date_range_and_status(): void
    {
        // Status 'processing' dipakai karena pesanan 'pending' yang sudah lama otomatis
        // dibatalkan oleh middleware pada request pertama, dan itu akan mengubah hasil filter status.
        $januari = $this->createdAt($this->orderFor('Siti', ['status' => 'processing']), '2026-01-10 12:00:00');
        $februari = $this->createdAt($this->orderFor('Budi', ['status' => 'processing']), '2026-02-10 12:00:00');
        $batal = $this->createdAt($this->orderFor('Ani', ['status' => 'cancelled']), '2026-02-11 12:00:00');

        $this->owner()->get(route('export.orders.preview', ['from' => '2026-02-01', 'to' => '2026-02-28']))
            ->assertOk()
            ->assertSee($februari->order_number)
            ->assertSee($batal->order_number)
            ->assertDontSee($januari->order_number);

        $this->owner()->get(route('export.orders.preview', ['from' => '2026-02-01', 'status' => 'cancelled']))
            ->assertOk()
            ->assertSee($batal->order_number)
            ->assertDontSee($februari->order_number);
    }

    public function test_exports_with_no_matching_orders_redirect_with_a_message(): void
    {
        $this->createdAt($this->orderFor('Siti'), '2026-01-10 12:00:00');

        foreach (['export.orders.preview', 'export.orders.pdf', 'export.orders'] as $routeName) {
            $this->owner()->get(route($routeName, ['from' => '2030-01-01']))
                ->assertRedirect(route('dashboard.import-export'))
                ->assertSessionHas('error');
        }
    }

    public function test_excel_export_uses_the_same_filters(): void
    {
        $januari = $this->createdAt($this->orderFor('Siti', ['customer_email' => 'siti@contoh.test']), '2026-01-10 12:00:00');
        $this->createdAt($this->orderFor('Budi'), '2026-02-10 12:00:00');

        $export = new OrdersExport(['to' => '2026-01-31']);
        $rows = $export->collection();

        $this->assertCount(1, $rows);
        $this->assertSame($januari->order_number, $rows->first()->order_number);

        $mapped = $export->map($rows->first());
        $this->assertSame('siti@contoh.test', $mapped[3]);
        $this->assertContains('Email', $export->headings());
    }

    public function test_excel_download_works_with_filters(): void
    {
        $this->orderFor('Siti');

        $res = $this->owner()->get(route('export.orders', ['status' => 'pending']))->assertOk();

        $this->assertStringContainsString('.xlsx', (string) $res->headers->get('content-disposition'));
    }

    public function test_import_export_page_has_date_and_status_filters(): void
    {
        $this->owner()->get(route('dashboard.import-export'))
            ->assertOk()
            ->assertSee('name="from"', false)
            ->assertSee('name="to"', false)
            ->assertSee('name="status"', false);
    }
}