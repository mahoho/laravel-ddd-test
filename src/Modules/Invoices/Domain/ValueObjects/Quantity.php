<?php

namespace Modules\Invoices\Domain\ValueObjects;

use Modules\Common\Domain\ValueObject;

class Quantity extends ValueObject
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException("Quantity must be a positive integer.");
        }
        $this->value = $value;
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

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
