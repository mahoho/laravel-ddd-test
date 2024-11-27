<?php

namespace Modules\Invoices\Domain\Entities;

use Modules\Common\Domain\Entity;
use Modules\Invoices\Domain\Enums\StatusEnum;

class Invoice extends Entity
{

    public function __construct(
        private string $id,
        private StatusEnum $status,
        private string $customerName,
        private string $customerEmail,
        private array $productLines = []
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): StatusEnum
    {
        return $this->status;
    }

    public function setStatus(StatusEnum $status): void
    {
        $this->status = $status;
    }

    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    public function getProductLines(): array
    {
        return $this->productLines;
    }

    public function addProductLine(InvoiceProductLine $productLine): void
    {
        $this->productLines[] = $productLine;
    }

    public function calculateTotalPrice(): int
    {
        return array_sum(array_map(
            fn(InvoiceProductLine $line) => $line->getTotalUnitPrice(),
            $this->productLines
        ));
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail,
            'product_lines' => $this->productLines
        ];
    }
}
