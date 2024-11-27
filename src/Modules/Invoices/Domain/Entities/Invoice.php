<?php

namespace Modules\Invoices\Domain\Entities;

use Modules\Common\Domain\Entity;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\ValueObjects\Price;
use Modules\Invoices\Domain\ValueObjects\Quantity;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Invoice extends Entity
{
    private array $productLines = [];
    private int $totalPrice = 0;

    public function __construct(
        private readonly UuidInterface $id,
        private StatusEnum $status,
        private readonly string $customerName,
        private readonly string $customerEmail,
        $productLines,
    ) {
        foreach ($productLines as $lineData) {
            $this->productLines[] = new InvoiceProductLine(
                id: Uuid::uuid4(),
                invoiceId: $this->getId(),
                name: $lineData['name'],
                quantity: new Quantity($lineData['quantity']),
                price: new Price($lineData['price'])
            );
        }

        $this->totalPrice = $this->calculateTotalPrice();
    }

    public function getId(): UuidInterface
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
        $this->calculateTotalPrice();
    }

    public function getTotalPrice(): int
    {
        return $this->totalPrice;
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
            'product_lines' => $this->productLines,
            'total_price' => $this->totalPrice
        ];
    }
}
