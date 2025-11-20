<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientFavorite;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFavoriteFactory extends Factory
{
    protected $model = ClientFavorite::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'product_id' => fake()->numberBetween(1, 10000),
        ];
    }
}

