<?php

namespace App\Services;

use App\Exceptions\ProductAlreadyFavoritedException;
use App\Repositories\ClientFavoriteProductsRepositoryInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class ValidateProductsService
{
    private string $apiProductsUrl;
    private string $getProductsUrl;
    private int $cacheTime;

    public function __construct(
        private readonly Client $client,
        private readonly ClientFavoriteProductsRepositoryInterface $productsRepository
    ) {
        $this->apiProductsUrl = config('services.api_products.url');
        $this->getProductsUrl = $this->apiProductsUrl . 'products';
        $this->cacheTime = config('services.api_products.cache_time');
    }

    public function validateProducts(string $productId, string $clientId): bool
    {
        //retorna essa exception caso o produto já esteja favoritado
        if ($this->productsRepository->checkIfProductIsFavorite($clientId, $productId)) {
            throw new ProductAlreadyFavoritedException();
        }

        $response = $this->client->get($this->getProductsUrl . '/' . $productId);
        $productData = $response->getBody()->getContents();

        if (empty($productData)) {
            return false;
        }

        // salva o produto no cache, para economizar chamadas na api
        // deixei um tempo de 10 minutos, mas editavel no env
        // em produção e uso massivo usaria o redis, mas para o desafio apenas deixei o cache do laravel mesmo
        Cache::remember('product_' . $productId, $this->cacheTime, function () use ($productData) {
            return json_decode($productData, true);
        });

        // aqui só salva
        $productData = json_decode($productData, true);
        $this->productsRepository->store([
            'client_id' => $clientId,
            'product_id' => $productId,
        ]);

        return true;
    }

    public function showFavoriteProducts(string $clientId): array
    {
        $productsIds = $this->productsRepository->showFavoriteProducts($clientId);

        if (empty($productsIds)) {
            return [];
        }

        //verifica se o produto está no cache, para economizar chamadas na api
        $cachedProducts = [];

        foreach ($productsIds as $productData) {
            $productId = is_array($productData) ? $productData['product_id'] : $productData->product_id;
            
            $cachedProduct = Cache::get('product_' . $productId);
            
            // Se estiver, adc ao vetor, e da um continue para o próximo produto
            if ($cachedProduct) {
                $cachedProducts[] = $cachedProduct;
                continue;
            }

            // Se não estiver, chama a api e salva no cache
            $response = $this->client->get($this->getProductsUrl . '/' . $productId);
            $productData = $response->getBody()->getContents();

            if (empty($productData)) {
                continue;
            }

            $productData = json_decode($productData, true);
            Cache::remember('product_' . $productId, $this->cacheTime, function () use ($productData) {
                return $productData;
            });

            $cachedProducts[] = $productData;
        }

        return $cachedProducts;
    }

    public function getFavoriteProduct(string $clientId, string $productId)
    {
        return $this->productsRepository->getByClientIdAndProductId($clientId, $productId);
    }

    public function deleteFavoriteProduct(string $clientId, string $productId): bool
    {
        return $this->productsRepository->deleteByClientIdAndProductId($clientId, $productId);
    }
}

