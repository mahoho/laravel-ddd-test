<?php

namespace Modules\Invoices\Domain\Entities;

use Modules\Common\Domain\Entity;
use Modules\Invoices\Domain\ValueObjects\Price;
use Modules\Invoices\Domain\ValueObjects\Quantity;

class InvoiceProductLine extends Entity
{
    public function __construct(
        private readonly string   $id,
        private readonly string   $invoiceId,
        private readonly string   $name,
        private readonly Quantity $quantity,
        private readonly Price    $price
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQuantity(): int
    {
        return $this->quantity->getValue();
    }

    public function getPrice(): int
    {
        return $this->price->getValue();
    }

    public function getTotalUnitPrice(): int
    {
        return $this->quantity->getValue() * $this->price->getValue();
    }

    public function toArray(): array
    {
        return [
            'invoice_id' => $this->invoiceId,
            'name'       => $this->name,
            'price'      => $this->price,
            'quantity'   => $this->quantity,
        ];
    }

}
