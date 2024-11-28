<?php

namespace Tests\Unit\Invoice\ValueObjects;

use Illuminate\Foundation\Testing\TestCase;
use Modules\Invoices\Domain\ValueObjects\Quantity;

class QuantityTest extends TestCase
{
    public function testWillThrowWithNegativeValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Quantity(-10);
    }

    public function testWillThrowWithZeroValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Quantity(0);
    }
}
