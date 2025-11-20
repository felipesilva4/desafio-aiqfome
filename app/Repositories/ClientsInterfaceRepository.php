<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use App\Models\Client;

interface ClientsInterfaceRepository
{
    public function createClient(array $data): Client;
    public function getAllClients(): ?Collection;
    public function updateClient(string $id, array $data): ?Client;
    public function deleteClient(string $id): ?Client;
    public function getClient(string $id): ?Client;
}