<?php

namespace App\Repositories;

use App\Models\Client;
use Illuminate\Support\Collection;

class ClientsRepository implements ClientsInterfaceRepository
{
    public function createClient(array $data): Client
    {
        return Client::create($data);
    }

    public function getAllClients(): ?Collection
    {
        return Client::all();
    }

    public function getClient(string $id): ?Client
    {
        return Client::find($id);
    }

    public function updateClient(string $id, array $data): ?Client
    {
        $client = Client::find($id);
        if (!$client) {
            return null;
        }
        $client->update($data);

        return $client;
    }

    public function deleteClient(string $id): ?Client
    {
        $client = Client::find($id);
        if (!$client) {
            return null;
        }

        $client->delete();

        return $client;
    }
}
