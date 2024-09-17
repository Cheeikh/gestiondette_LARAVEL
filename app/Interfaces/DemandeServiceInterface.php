<?php

namespace App\Interfaces;


use App\Models\Demande;

interface DemandeServiceInterface
{
    public function createDemande(array $data, $user): Demande;

    public function getDemandes($user, $filters = []);

    public function sendRelance(Demande $demande, $user);

    public function checkDisponibilite(Demande $demande, $user);

    public function updateDemandeStatus(Demande $demande, array $data, $user);

    public function getClientNotifications($user);
}
