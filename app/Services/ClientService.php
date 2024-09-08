<?php

namespace App\Services;

use App\Facades\UserFacade;
use App\Interfaces\ClientServiceInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Models\Client;
use Illuminate\Support\Facades\DB;



class ClientService implements ClientServiceInterface
{
    protected ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function registerClient(array $data, $photo = null): Client
    {
        DB::beginTransaction();
        try {
            $client = $this->clientRepository->create($data);

            if (isset($data['user'])) {
                $data['user']['email'] = $data['email']; // Ensure the user email is set
                $user = UserFacade::registerUser($data['user'], $photo);
                $client->user()->associate($user);
                $client->save();
            }

            DB::commit();

            return $client;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }    public function getAllClients(?string $comptes = null, ?string $active = null): array
    {
        return $this->clientRepository->getAll($comptes, $active);
    }

    public function getClientById(int $id): ?Client
    {
        return $this->clientRepository->findById($id);
    }

    public function getClientWithUser(int $id): ?Client
    {
        return $this->clientRepository->findClientWithUser($id);
    }
}
