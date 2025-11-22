<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientFavoriteProducts;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFavoriteProductsFactory extends Factory
{
    protected $model = ClientFavoriteProducts::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'product_id' => fake()->numberBetween(1, 10000),
        ];
    }
}

