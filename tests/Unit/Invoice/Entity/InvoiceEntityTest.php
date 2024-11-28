<?php

namespace Tests\Unit\Invoice\Entity;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\InvoiceProductLine;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\ValueObjects\Price;
use Modules\Invoices\Domain\ValueObjects\Quantity;
use Modules\Invoices\Infrastructure\Models\InvoiceModel;
use Modules\Invoices\Infrastructure\Models\InvoiceProductLineModel;
use Ramsey\Uuid\Uuid;

class InvoiceEntityTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        $this->setUpFaker();
    }

    public function testInvoiceCanBeCreatedWithProductLines()
    {
        $invoiceId = Uuid::uuid4();

        $productLines = InvoiceProductLineModel::factory()->count(10)->make(['invoice_id' => $invoiceId])->toArray();

        $invoice = new Invoice(
            id: $invoiceId,
            status: StatusEnum::Draft,
            customerName: $this->faker->name(),
            customerEmail: $this->faker->email(),
            productLines: $productLines
        );

        $expectedTotalPrice = 0;

        foreach ($productLines as $line) {
            $expectedTotalPrice += $line['quantity'] * $line['price'];
        }

        $this->assertEquals($expectedTotalPrice, $invoice->getTotalPrice());
        $this->assertCount(count($productLines), $invoice->getProductLines());
    }

    public function testAddProductLineIncreasesTotalPrice()
    {
        $invoice = $this->generateInvoiceData(0);

        $newLine = new InvoiceProductLine(
            id: Uuid::uuid4(),
            invoiceId: $invoice->getId(),
            name: $this->faker->name(),
            quantity: new Quantity($this->faker->numberBetween(1, 10)),
            price: new Price($this->faker->numberBetween(1, 1000))
        );

        $invoice->addProductLine($newLine);
        $expectedTotalPrice = $newLine->getTotalUnitPrice();
        $this->assertEquals($expectedTotalPrice, $invoice->getTotalPrice());
    }

    public function testInvoiceStatusCanBeUpdated()
    {
        $invoice = $this->generateInvoiceData();
        $invoice->setStatus(StatusEnum::Sending);

        $this->assertEquals(StatusEnum::Sending, $invoice->getStatus());
    }


    private function generateInvoiceData(?int $productRows = null): Invoice
    {
        $productRows = $productRows ?? $this->faker->numberBetween(1, 10);

        $invoiceData = InvoiceModel::factory()
            ->make()
            ->toArray();

        $productLines = InvoiceProductLineModel::factory()->count($productRows)->make(['invoice_id' => $invoiceData['id']]);
        $invoiceData['product_lines'] = $productLines;

        return new Invoice(
            id: $invoiceData['id'],
            status: StatusEnum::from($invoiceData['status']),
            customerName: $invoiceData['customer_name'],
            customerEmail: $invoiceData['customer_email'],
            productLines: $invoiceData['product_lines']
        );
    }
}

