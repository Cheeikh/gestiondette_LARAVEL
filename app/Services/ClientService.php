<?php

namespace App\Services;

use App\Interfaces\ClientServiceInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Models\Client;
use App\Services\UserService; // Importation du service utilisateur
use Illuminate\Support\Facades\DB;
use Exception;

class ClientService implements ClientServiceInterface
{
    protected $clientRepository;
    protected $userService;

    public function __construct(ClientRepositoryInterface $clientRepository, UserService $userService)
    {
        $this->clientRepository = $clientRepository;
        $this->userService = $userService; // Injection du service utilisateur
    }

    public function registerClient(array $data): Client
    {
        DB::beginTransaction();
        try {
            // Vérifier si un utilisateur est inclus dans les données du client
            if (isset($data['user'])) {
                // Créer l'utilisateur associé au client
                $user = $this->createUserForClient($data['user']);
                $data['user_id'] = $user->id;
            }

            // Enregistrement du client
            $client = $this->clientRepository->create($data);

            DB::commit();
            return $client;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getAllClients(?string $comptes = null, ?string $active = null): array
    {
        $hasUser = $comptes === 'oui' ? true : ($comptes === 'non' ? false : null);
        $isActive = $active === 'oui' ? true : ($active === 'non' ? false : null);

        return $this->clientRepository->getAll($hasUser, $isActive);
    }

    public function getClientById(int $id): ?Client
    {
        return $this->clientRepository->findById($id);
    }

    public function getClientWithUser(int $id): ?Client
    {
        return $this->clientRepository->findClientWithUser($id);
    }

    protected function createUserForClient(array $userData)
    {
        // Utilisation du service utilisateur pour créer un utilisateur
        return $this->userService->registerUser($userData);
    }
}
