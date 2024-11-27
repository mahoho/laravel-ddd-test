<?php

namespace Modules\Invoices\Infrastructure\Repositories;

use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\InvoiceProductLine;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Infrastructure\Models\InvoiceModel;
use Ramsey\Uuid\Uuid;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function findById(string $id): ?Invoice
    {
        $invoiceModel = InvoiceModel::with('product_lines')->find($id);

        if (!$invoiceModel) {
            return null;
        }

        return $this->toEntity($invoiceModel);
    }

    public function findAll(): array
    {
        return InvoiceModel::with('product_lines')->get()
            ->map(fn($model) => $this->toEntity($model))
            ->toArray();
    }

    public function save(Invoice $invoice): void
    {
        /** @var InvoiceModel $invoiceModel */
        $invoiceModel = InvoiceModel::updateOrCreate(
            ['id' => $invoice->getId()],
            [
                'status'         => $invoice->getStatus()->value,
                'customer_name'  => $invoice->getCustomerName(),
                'customer_email' => $invoice->getCustomerEmail(),
            ]
        );

        foreach ($invoice->getProductLines() as $productLine) {
            /** @var InvoiceProductLine $productLine */
            $invoiceModel->product_lines()->updateOrCreate(
                ['id' => $productLine->getId()],
                [
                    'invoice_id' => $invoice->getId(),
                    'name'       => $productLine->getName(),
                    'quantity'   => $productLine->getQuantity(),
                    'price'      => $productLine->getPrice(),
                ]
            );
        }
    }

    public function delete(Invoice $invoice): void
    {
        InvoiceModel::destroy($invoice->getId());
    }

    private function toEntity(InvoiceModel $model): Invoice
    {
        $productLines = $model->product_lines->toArray();

        return new Invoice(
            id: Uuid::fromString($model->id),
            status: $model->status,
            customerName: $model->customer_name,
            customerEmail: $model->customer_email,
            productLines: $productLines
        );
    }
}
