<?php

namespace App\DTOs;

class ClientWithFavoritesDTO
{
    public function __construct(
        public readonly ClientDTO $client,
        public readonly array $favoriteProducts,
    ) {
    }

    public function toArray(): array
    {
        $products = [];

        foreach ($this->favoriteProducts as $product) {
            $productArray = is_array($product) ? $product : (array) $product;
            $products[] = (new FavoriteProductDTO($productArray))->toArray();
        }

        return [
            'cliente' => $this->client->toArray(),
            'produtos_favoritos' => $products,
        ];
    }
}

