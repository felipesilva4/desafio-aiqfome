<?php

namespace App\Repositories;

interface ClientFavoriteProductsRepositoryInterface
{
    public function store(array $data): void;
    public function showFavoriteProducts(string $clientId): array;
    public function checkIfProductIsFavorite(string $clientId, string $productId): bool;
}

