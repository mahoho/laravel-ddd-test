<?php

namespace Modules\Invoices\Application\Services;

use Modules\Invoices\Application\Exceptions\SendInvoiceException;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\InvoiceProductLine;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Domain\ValueObjects\Price;
use Modules\Invoices\Domain\ValueObjects\Quantity;
use Modules\Notifications\Api\Dtos\NotifyData;
use Modules\Notifications\Application\Facades\NotificationFacade;
use Ramsey\Uuid\Uuid;

class InvoiceService
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository) {}

    /**
     * @param string $id
     * @return array|null
     */
    public function viewInvoice(string $id): ?array
    {
        $invoice = $this->invoiceRepository->findById($id);

        if (!$invoice) {
            return null;
        }

        return $invoice->toArray();
    }

    /**
     * @param string $customerName
     * @param string $customerEmail
     * @return Invoice
     */
    public function createInvoice(string $customerName, string $customerEmail, array $productLines = []): Invoice
    {
        $invoice = new Invoice(
            id: Uuid::uuid4(),
            status: StatusEnum::Draft,
            customerName: $customerName,
            customerEmail: $customerEmail,
            productLines: $productLines
        );

        $this->invoiceRepository->save($invoice);

        return $invoice;
    }

    /**
     * @param string $id
     * @param string $subject
     * @param string $message
     * @return void
     * @throws SendInvoiceException
     */
    public function sendInvoice(string $id, string $subject, string $message): void
    {
        $invoice = $this->invoiceRepository->findById($id);

        if (!$invoice) {
            throw new SendInvoiceException("Invoice not found");
        }

        if ($invoice->getStatus() !== StatusEnum::Draft) {
            throw new SendInvoiceException("Invoice must be in draft status to be sent");
        }

        if (empty($invoice->getProductLines())) {
            throw new SendInvoiceException("Invoice must contain at least one product line");
        }

        foreach ($invoice->getProductLines() as $productLine) {
            /** @var InvoiceProductLine $productLine */
            if ($productLine->getQuantity() <= 0 || $productLine->getPrice() <= 0) {
                throw new SendInvoiceException("Product lines must have positive quantity and unit price");
            }
        }

        $notificationData = new NotifyData(
            $invoice->getId(),
            $invoice->getCustomerEmail(),
            $subject,
            $message
        );

        NotificationFacade::notify($notificationData);
        $invoice->setStatus(StatusEnum::Sending);

        $this->invoiceRepository->save($invoice);
    }
}
