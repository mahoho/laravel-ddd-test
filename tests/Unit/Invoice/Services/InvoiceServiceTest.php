<?php

namespace Tests\Unit\Invoice\Services;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Invoices\Application\Exceptions\SendInvoiceException;
use Modules\Invoices\Application\Services\InvoiceService;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\InvoiceProductLine;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Domain\ValueObjects\Price;
use Modules\Invoices\Domain\ValueObjects\Quantity;
use Modules\Notifications\Api\Dtos\NotifyData;
use Modules\Notifications\Api\NotificationFacadeInterface;
use Ramsey\Uuid\Uuid;

class InvoiceServiceTest extends TestCase
{
    use WithFaker;

    private InvoiceRepositoryInterface $invoiceRepository;
    private NotificationFacadeInterface $notificationFacade;
    private InvoiceService $invoiceService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->invoiceRepository = $this->createMock(InvoiceRepositoryInterface::class);
        $this->notificationFacade = $this->createMock(NotificationFacadeInterface::class);
        $this->invoiceService = new InvoiceService($this->invoiceRepository, $this->notificationFacade);
    }

    public function testSendInvoiceThrowsExceptionWhenInvoiceNotFound()
    {
        $this->invoiceRepository->method('findById')->willReturn(null);

        $this->expectException(SendInvoiceException::class);
        $this->expectExceptionMessage("Invoice not found");

        $this->invoiceService->sendInvoice(Uuid::uuid4()->toString(), 'Subject', 'Message');
    }

    public function testSendInvoiceThrowsExceptionWhenStatusIsNotDraft()
    {
        $invoice = $this->createInvoice(StatusEnum::SentToClient, []);

        $this->invoiceRepository->method('findById')->willReturn($invoice);

        $this->expectException(SendInvoiceException::class);
        $this->expectExceptionMessage("Invoice must be in draft status to be sent");

        $this->invoiceService->sendInvoice(Uuid::uuid4()->toString(), 'Subject', 'Message');
    }

    public function testSendInvoiceThrowsExceptionWhenNoProductLinesExist()
    {
        $invoice = $this->createInvoice(StatusEnum::Draft, []);

        $this->invoiceRepository->method('findById')->willReturn($invoice);

        $this->expectException(SendInvoiceException::class);
        $this->expectExceptionMessage("Invoice must contain at least one product line");

        $this->invoiceService->sendInvoice(Uuid::uuid4()->toString(), 'Subject', 'Message');
    }

    public function testSendInvoiceSuccessfullySendsNotificationAndUpdatesStatus()
    {
        $validProductLines = [
            new InvoiceProductLine(
                id: Uuid::uuid4(),
                invoiceId: Uuid::uuid4(),
                name: $this->faker->name(),
                quantity: new Quantity($this->faker->numberBetween(1, 10)),
                price: new Price($this->faker->numberBetween(1, 100))
            ),
        ];

        $invoice = $this->createInvoice(StatusEnum::Draft, $validProductLines);

        $this->invoiceRepository
            ->method('findById')
            ->willReturn($invoice);

        $this->notificationFacade
            ->expects($this->once())
            ->method('notify')
            ->with($this->callback(function (NotifyData $notifyData) use ($invoice) {
                return $notifyData->toEmail === $invoice->getCustomerEmail();
            }));

        $this->invoiceRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Invoice $updatedInvoice) {
                return $updatedInvoice->getStatus() === StatusEnum::Sending;
            }));

        $this->invoiceService->sendInvoice(Uuid::uuid4()->toString(), 'Invoice Subject', 'Invoice Message');
    }

    private function createInvoice(StatusEnum $status, array $productLines): Invoice
    {
        return new Invoice(
            id: Uuid::uuid4(),
            status: $status,
            customerName: $this->faker->name(),
            customerEmail: $this->faker->email(),
            productLines: $productLines
        );
    }
}
