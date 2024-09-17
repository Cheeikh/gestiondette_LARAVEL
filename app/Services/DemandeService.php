<?php

namespace App\Services;

use App\Interfaces\DemandeServiceInterface;
use App\Jobs\SendDemandeNotificationJob;
use App\Models\Article;
use App\Models\Client;
use App\Models\Demande;
use App\Models\Dette;
use App\Notifications\DemandeAnnuleeNotification;
use App\Notifications\DemandePartiellementDisponibleNotification;
use App\Notifications\DemandeValideeNotification;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DemandeService implements DemandeServiceInterface
{
    public function createDemande(array $data, $user): Demande
    {
        $totalAmount = 0;
        $articlesData = [];

        foreach ($data['articles'] as $articleInput) {

            $article = Article::findOrFail($articleInput['id']);

            $price = $articleInput['price'];
            $quantity = $articleInput['quantity'];

            if ($price < 0) {
                throw new Exception('Le prix ne peut pas être négatif.');
            }

            $totalAmount += $price * $quantity;

            $articlesData[$article->id] = [
                'quantity' => $quantity,
                'price' => $price,
            ];
        }

        $demande = Demande::create([
            'client_id' => $user->client->id,
            'total_amount' => $totalAmount,
            'description' => $data['description'] ?? null,
        ]);

        $demande->articles()->attach($articlesData);

        SendDemandeNotificationJob::dispatch($demande);

        return $demande;
    }

    public function getDemandes($user, $filters = [])
    {
        $roleActions = [
            2 => function() use ($filters) {
                $query = Demande::query();
                if (isset($filters['status'])) {
                    $status = $filters['status'];
                    if (!in_array($status, ['en_cours', 'annuler', 'valider'])) {
                        throw new \InvalidArgumentException('Invalid status parameter');
                    }
                    $query->where('status', $status);
                }
                return $query->get();
            },
            3 => function() use ($user, $filters) {
                $query = $user->client->demandes();
                if (isset($filters['status'])) {
                    $query->where('status', $filters['status']);
                }
                return $query->get();
            },
        ];

        if (!isset($roleActions[$user->role_id])) {
            throw new Exception('Unauthorized');
        }

        return $roleActions[$user->role_id]();
    }

    public function sendRelance(Demande $demande, $user)
    {
        if ($demande->client_id !== $user->client->id || $demande->status !== 'annuler' || $demande->updated_at->diffInDays(now()) < 2) {
            throw new Exception('Action non autorisée ou relance non disponible');
        }

        $demande->status = 'en_cours';
        $demande->save();

        SendDemandeNotificationJob::dispatch($demande);

        return $demande;
    }

    public function checkDisponibilite(Demande $demande, $user)
    {
        if ($user->role_id === 3 && $demande->client_id !== $user->client->id) {
            throw new Exception('Unauthorized');
        }

        $articlesDemande = $demande->articles()->withPivot('quantity')->get();

        $articlesDisponibles = [];
        $demandeSatisfiable = true;

        foreach ($articlesDemande as $article) {
            $requestedQuantity = $article->pivot->quantity;
            $stockDisponible = $article->qteStock - $article->quantite_seuil;

            $availableQuantity = max(0, min($stockDisponible, $requestedQuantity));

            $status = ($availableQuantity == 0) ? 'indisponible' :
                (($availableQuantity == $requestedQuantity) ? 'disponible' : 'partiellement disponible');

            $articlesDisponibles[] = [
                'article_id' => $article->id,
                'libelle' => $article->libelle,
                'requested_quantity' => $requestedQuantity,
                'available_quantity' => $availableQuantity,
                'status' => $status,
            ];

            $demandeSatisfiable = $demandeSatisfiable && ($status === 'disponible');
        }

        if (!$demandeSatisfiable) {
            $notification = new DemandePartiellementDisponibleNotification($demande, $articlesDisponibles);
            $demande->client->user->notify($notification);
        }

        return [
            'demande_id' => $demande->id,
            'demande_satisfiable' => $demandeSatisfiable,
            'articles' => $articlesDisponibles,
        ];
    }

    public function updateDemandeStatus(Demande $demande, array $data, $user)
    {
        if ($user->role_id !== 2) {
            throw new Exception('Unauthorized');
        }
        $client = $demande->client;
        if (!$client) {
            throw new Exception("Client introuvable pour la demande ID {$demande->id}");
        }
        $statusActions = [
            'valider' => function() use ($demande, $data, $client) {
                $demande->status = 'valider';
                $demande->save();
                $this->creerDettePourDemande($demande);
                $notification = new DemandeValideeNotification($demande, $data['motif'] ?? null);
                $notification->toSms();
                $demande->client->user->notify($notification);
            },
            'annuler' => function() use ($demande, $data) {
                $demande->status = 'annuler';
                $demande->save();
                $notification = new DemandeAnnuleeNotification($demande, $data['motif'] ?? null);
                $notification->toSms();
                $demande->client->user->notify($notification);
            },
        ];

        if (!isset($statusActions[$data['status']])) {
            throw new \InvalidArgumentException('Invalid status');
        }

        DB::beginTransaction();

        try {
            $statusActions[$data['status']]();
            DB::commit();
            return $demande;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de la mise à jour du statut de la demande ID {$demande->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function getClientNotifications($user)
    {
        if ($user->role_id !== 3) {
            throw new Exception('Unauthorized');
        }

        return $user->notifications()
            ->whereIn('type', [
                DemandeValideeNotification::class,
                DemandeAnnuleeNotification::class,
                DemandePartiellementDisponibleNotification::class,
            ])
            ->get();
    }

    public function creerDettePourDemande(Demande $demande)
    {
        $articlesIndisponibles = $demande->articles->filter(function($article) {
            $requestedQuantity = $article->pivot->quantity;
            $stockDisponible = $article->qteStock - $article->quantite_seuil;
            return $stockDisponible < $requestedQuantity;
        });

        if ($articlesIndisponibles->isNotEmpty()) {
            $nomsArticles = $articlesIndisponibles->pluck('libelle')->join(', ');
            throw new Exception("Stock insuffisant pour les articles suivants : {$nomsArticles}");
        }

        $montant = str_replace(',', '', $demande->total_amount); // Retirer les virgules pour PostgreSQL

        $dette = Dette::create([
            'client_id' => $demande->client_id,
            'montant' => $montant,
            'date' => now(),
            'date_echeance' => now()->addDays(30),
        ]);

        $articlesData = $demande->articles->mapWithKeys(function ($article) {
            return [
                $article->id => [
                    'qte_vente' => $article->pivot->quantity,
                    'prix_vente' => $article->pivot->price,
                ],
            ];
        });

        $dette->articles()->attach($articlesData);

        $demande->articles->each(function ($article) {
            $article->qteStock -= $article->pivot->quantity;
            $article->save();
        });

        $demande->dette_id = $dette->id;
        $demande->save();
    }



}
