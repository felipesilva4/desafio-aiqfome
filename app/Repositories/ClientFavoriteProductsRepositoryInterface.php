<?php

namespace App\Repositories;

interface ClientFavoriteProductsRepositoryInterface
{
    public function store(array $data): void;
    public function showFavoriteProducts(string $clientId): array;
    public function checkIfProductIsFavorite(string $clientId, string $productId): bool;
    public function deleteByClientId(string $clientId): void;
    public function deleteByClientIdAndProductId(string $clientId, string $productId): bool;
    public function getByClientIdAndProductId(string $clientId, string $productId);
}

