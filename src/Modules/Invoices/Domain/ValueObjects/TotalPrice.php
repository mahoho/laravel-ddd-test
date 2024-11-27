<?php

namespace Modules\Invoices\Domain\ValueObjects;

use Modules\Common\Domain\ValueObject;

class TotalPrice extends ValueObject
{
    private int $value;

    public function __construct(Quantity $quantity, Price $unitPrice)
    {
        $this->value = $quantity->getValue() * $unitPrice->getValue();
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(ValueObject $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function jsonSerialize(): int
    {
        return $this->value;
    }
}

