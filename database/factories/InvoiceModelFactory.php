<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Infrastructure\Models\InvoiceModel;
use Ramsey\Uuid\Uuid;
use Str;

class InvoiceModelFactory extends Factory
{
    protected $model = InvoiceModel::class;

    public function definition(): array
    {
        $faker = \Faker\Factory::create();

        return [
            'id'             => Uuid::uuid4(),
            'status'         => StatusEnum::Draft,
            'customer_name'  => $faker->name(),
            'customer_email' => $faker->unique()->safeEmail(),
        ];
    }
}
