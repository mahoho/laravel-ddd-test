<?php

namespace Tests\Unit\Invoice\ValueObjects;

use Illuminate\Foundation\Testing\TestCase;
use Modules\Invoices\Domain\ValueObjects\Price;

class PriceTest extends TestCase
{
    public function testWillThrowWithNegativeValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Price(-10);
    }

    public function testWillThrowWithZeroValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Price(0);
    }
}
