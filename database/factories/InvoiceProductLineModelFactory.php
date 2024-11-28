<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Invoices\Infrastructure\Models\InvoiceProductLineModel;
use Ramsey\Uuid\Uuid;

class InvoiceProductLineModelFactory extends Factory
{
    protected $model = InvoiceProductLineModel::class;

    public function definition(): array
    {
        $faker = \Faker\Factory::create();

        return [
            'id'         => Uuid::uuid4(),
            'invoice_id' => Uuid::uuid4(),
            'name'       => $faker->name(),
            'price'      => $faker->numberBetween(1, 1000),
            'quantity'   => $faker->numberBetween(1, 10),
        ];
    }
}
