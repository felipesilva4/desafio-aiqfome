<?php

namespace App\DTOs;

class ClientDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->name,
            'email' => $this->email,
        ];
    }
}

