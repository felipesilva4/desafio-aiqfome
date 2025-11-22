<?php

namespace App\Repositories;

use App\Models\ClientFavoriteProducts;

class ClientFavoriteProductsRepository implements ClientFavoriteProductsRepositoryInterface
{
    public function store(array $data): void
    {
        ClientFavoriteProducts::create($data);
    }

    public function showFavoriteProducts(string $clientId): array
    {
        return ClientFavoriteProducts::where('client_id', $clientId)->get()->toArray();
    }

    public function checkIfProductIsFavorite(string $clientId, string $productId): bool
    {
        return ClientFavoriteProducts::where('client_id', $clientId)->where('product_id', $productId)->exists();
    }

    public function deleteByClientId(string $clientId): void
    {
        ClientFavoriteProducts::where('client_id', $clientId)->delete();
    }

    public function deleteByClientIdAndProductId(string $clientId, string $productId): bool
    {
        return ClientFavoriteProducts::where('client_id', $clientId)
            ->where('product_id', $productId)
            ->delete() > 0;
    }

    public function getByClientIdAndProductId(string $clientId, string $productId)
    {
        return ClientFavoriteProducts::where('client_id', $clientId)
            ->where('product_id', $productId)
            ->first();
    }
}

