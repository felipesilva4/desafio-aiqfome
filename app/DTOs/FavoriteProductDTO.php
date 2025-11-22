<?php

namespace App\DTOs;

class FavoriteProductDTO
{
    public function __construct(
        public readonly array $product,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->product['id'] ?? null,
            'titulo' => $this->product['title'] ?? null,
            'imagem' => $this->product['image'] ?? null,
            'preco' => $this->product['price'] ?? null,
            'descricao' => $this->product['description'] ?? null,
        ];
    }
}

