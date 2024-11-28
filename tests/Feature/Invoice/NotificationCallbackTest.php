<?php

namespace Tests\Feature\Invoice;

use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Infrastructure\Models\InvoiceModel;
use Tests\TestCase;

class NotificationCallbackTest extends TestCase
{
    public function testInvoiceStatusChangedOnHookDelivery()
    {

        /** @var InvoiceModel $invoice */
        $invoice = InvoiceModel::factory()->create(['status' => StatusEnum::Sending]);

        $payload = [
            'action' => 'delivered',
            'reference' => $invoice->id
        ];

        $this->get(route('notification.hook', $payload));

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => StatusEnum::SentToClient,
        ]);
    }
}
