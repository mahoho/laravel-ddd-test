<?php

namespace Modules\Invoices\Domain\Repositories;

use Modules\Invoices\Domain\Entities\Invoice;

interface InvoiceRepositoryInterface
{
    public function findById(string $id): ?Invoice;
    public function findAll(): array;
    public function save(Invoice $invoice): void;
    public function delete(Invoice $invoice): void;
}
