<?php

namespace Modules\Invoices\Application\Listeners;

use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;

class ChangeInvoiceStatusOnSuccessfulDelivery
{
    public function __construct(private InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    public function handle(ResourceDeliveredEvent $event): void
    {
       $invoice = $this->invoiceRepository->findById($event->resourceId);
       if(!$invoice || $invoice->getStatus() !== StatusEnum::Sending) {
           return;
       }

       $invoice->setStatus(StatusEnum::SentToClient);
       $this->invoiceRepository->save($invoice);
    }
}
