<?php

namespace Tests\Feature\Invoice;

use App;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Infrastructure\Models\InvoiceModel;
use Modules\Invoices\Infrastructure\Models\InvoiceProductLineModel;
use Modules\Notifications\Api\NotificationFacadeInterface;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{

    public function testViewInvoiceReturnsInvoiceData()
    {
        /** @var InvoiceModel $invoice */
        $invoice = InvoiceModel::factory()->create();

        $response = $this->getJson(route('invoices.view', ['invoice_id' => $invoice->id]));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $invoice->id,
                'customer_name' => $invoice->customer_name,
                'customer_email' => $invoice->customer_email,
            ]);
    }

    public function testViewInvoiceReturnsNotFoundForInvalidId()
    {
        $response = $this->getJson(route('invoices.view', ['invoice_id' => 'invalid-id']));

        $response->assertStatus(404);
    }

    public function testCreateInvoiceSuccessfully()
    {
        $payload = [
            'customer_name' => 'John Doe',
            'customer_email' => 'john.doe@example.com',
            'product_lines' => [
                ['name' => 'Product A', 'quantity' => 2, 'price' => 100],
                ['name' => 'Product B', 'quantity' => 1, 'price' => 200],
            ],
        ];

        $response = $this->postJson(route('invoices.create'), $payload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'customer_name' => 'John Doe',
                'customer_email' => 'john.doe@example.com',
            ]);

        $this->assertDatabaseHas('invoices', ['customer_name' => 'John Doe']);
        $this->assertDatabaseHas('invoice_product_lines', ['name' => 'Product A']);
    }

    public function testCreateInvoiceValidationFails()
    {
        $payload = [
            'customer_name' => '',
            'customer_email' => 'invalid-email',
        ];

        $response = $this->postJson(route('invoices.create'), $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customer_name', 'customer_email']);
    }

    public function testSendInvoiceSuccessfully()
    {
        $driverMock = $this->createMock(NotificationFacadeInterface::class);
        $driverMock->expects($this->once())->method('notify');

        App::instance(NotificationFacadeInterface::class, $driverMock);

        /** @var InvoiceModel $invoice */
        $invoice = InvoiceModel::factory()->has(
            InvoiceProductLineModel::factory()->count(1),
            'product_lines'
        )->create(['status' => StatusEnum::Draft]);

        $payload = [
            'id' => $invoice->id,
            'subject' => 'Invoice Subject',
            'message' => 'Invoice Message',
        ];

        $response = $this->postJson(route('invoices.send'), $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => StatusEnum::Sending,
        ]);
    }

    public function testSendInvoiceFailsForInvalidStatus()
    {
        /** @var InvoiceModel $invoice */
        $invoice = InvoiceModel::factory()->create(['status' => StatusEnum::SentToClient]);

        $payload = [
            'id' => $invoice->id,
            'subject' => 'Invoice Subject',
            'message' => 'Invoice Message',
        ];

        $response = $this->postJson(route('invoices.send'), $payload);

        $response->assertStatus(400)
            ->assertJsonFragment(['message' => 'Invoice must be in draft status to be sent']);
    }

    public function testSendInvoiceFailsForMissingProductLines()
    {
        /** @var InvoiceModel $invoice */
        $invoice = InvoiceModel::factory()->create(['status' => StatusEnum::Draft]);

        $payload = [
            'id' => $invoice->id,
            'subject' => 'Invoice Subject',
            'message' => 'Invoice Message',
        ];

        $response = $this->postJson(route('invoices.send'), $payload);

        $response->assertStatus(400)
            ->assertJsonFragment(['message' => 'Invoice must contain at least one product line']);
    }
}
