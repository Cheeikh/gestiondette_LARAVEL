<?php

namespace App\Http\Controllers;

use App\Facades\DetteFacade;
use App\Http\Requests\DetteRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DetteController extends Controller
{
    // Création d'une nouvelle dette
    public function store(DetteRequest $request)
    {
        $dette = DetteFacade::createDette($request->validated());
        return response()->json($dette, Response::HTTP_CREATED);
    }

    // Lister toutes les dettes filtrées par statut (Solde ou NonSolde)
    public function listAll(Request $request)
    {
        $statut = $request->query('statut');
        $dettes = DetteFacade::getDettesByStatut($statut);
        return response()->json($dettes, Response::HTTP_OK);
    }

    // Lister une dette avec son client
    public function show($id)
    {
        $dette = DetteFacade::getDetteWithClient($id);
        return response()->json($dette, Response::HTTP_OK);
    }

    // Lister les articles d'une dette
    public function listArticles($id)
    {
        $detteWithArticles = DetteFacade::getDetteWithArticles($id);
        return response()->json(['data' => $detteWithArticles], Response::HTTP_OK);
    }

    // Ajouter un paiement à une dette
    public function addPaiement(Request $request, $id)
    {
        $validatedData = $request->validate([
            'montant' => 'required|numeric|min:0',
        ]);

        $paiement = DetteFacade::addPaiement($id, $validatedData);

        return response()->json($paiement, Response::HTTP_CREATED);
    }

    public function listPaiements($id)
    {
        $paiements = DetteFacade::getPaiementsByDetteId($id);
        return response()->json($paiements, Response::HTTP_OK);
    }


}
