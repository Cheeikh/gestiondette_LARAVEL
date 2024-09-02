<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use App\Models\Client; // Ajoutez cette ligne en haut de votre fichier AuthController

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Endpoints liés à l'authentification et à la gestion des utilisateurs"
 * )
 */
class AuthController extends Controller
{

    /**
 * @OA\Get(
 *     path="/api/users",
 *     operationId="getUsersList",
 *     tags={"Auth"},
 *     summary="Lister tous les utilisateurs",
 *     description="Retourne une liste de tous les utilisateurs.",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Liste des utilisateurs",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/User")
 *         )
 *     )
 * )
 */
public function index()
{
    $users = User::all(); // Récupérer tous les utilisateurs
    return response()->json(['users' => $users], 200);
}

    /**
     * @OA\Post(
     *     path="/api/register",
     *     operationId="registerUser",
     *     tags={"Auth"},
     *     summary="Créer un nouvel utilisateur",
     *     description="Crée un utilisateur avec un rôle admin ou vendeur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","prenom","login","email","password","role"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="prenom", type="string", example="John"),
     *             @OA\Property(property="login", type="string", example="johndoe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password"),
     *             @OA\Property(property="role", type="string", enum={"admin", "vendeur"}, example="admin")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur créé avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error"
     *     )
     * )
     */


     public function register(Request $request)
     {
         // Validation des données d'entrée avec des messages personnalisés
         $request->validate([
             'name' => 'required|string|max:255',
             'prenom' => 'required|string|max:255',
             'login' => 'required|string|max:255|unique:users,login',
             'email' => 'required|string|email|max:255|unique:users,email',
             'password' => 'required|string|min:5|confirmed',
             'role' => [
                 'required',
                 Rule::in(['admin', 'vendeur']), // Valider que le rôle est soit 'admin', soit 'vendeur'
             ],
         ], [
             'name.required' => 'Le nom est obligatoire.',
             'prenom.required' => 'Le prénom est obligatoire.',
             'login.required' => 'Le login est obligatoire.',
             'login.unique' => 'Ce login est déjà utilisé.',
             'email.required' => 'L\'email est obligatoire.',
             'email.unique' => 'Cet email est déjà utilisé.',
             'password.required' => 'Le mot de passe est obligatoire.',
             'password.confirmed' => 'Les mots de passe ne correspondent pas.',
             'role.required' => 'Le rôle est obligatoire.',
             'role.in' => 'Le rôle doit être soit admin, soit vendeur.',
         ]);
     
         // Créer un nouvel utilisateur
         $user = User::create([
             'name' => $request->name,
             'prenom' => $request->prenom,
             'login' => $request->login,
             'email' => $request->email,
             'password' => Hash::make($request->password),
             'etat' => 'actif', // L'utilisateur est actif par défaut
         ]);
     
         // Assigner le rôle (admin ou vendeur)
         $user->assignRole($request->role);
     
         // Générer un token d'accès pour l'utilisateur
         $token = $user->createToken('auth_token')->accessToken;
     
         // Retourner la réponse JSON avec l'utilisateur et le token d'accès
         return response()->json([
             'user' => $user,
             'access_token' => $token,
         ], 201);
     }


    /**
     * @OA\Post(
     *     path="/api/login",
     *     operationId="loginUser",
     *     tags={"Auth"},
     *     summary="Connecter un utilisateur",
     *     description="Connecte un utilisateur et retourne un token d'accès",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"login","password"},
     *             @OA\Property(property="login", type="string", example="johndoe"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="access_token", type="string", example="token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error"
     *     )
     * )
     */

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // Utilisation du champ login pour l'authentification
            'password' => 'required|string',
        ]);

        $credentials = $request->only('login', 'password');

        if (!Auth::attempt(['login' => $credentials['login'], 'password' => $credentials['password'], 'etat' => 'actif'])) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect or the account is inactive.'],
            ]);
        }

        $user = User::where('login', $request->login)->firstOrFail();
        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     operationId="logoutUser",
     *     tags={"Auth"},
     *     summary="Déconnecter un utilisateur",
     *     description="Déconnecte l'utilisateur en supprimant son token d'accès",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Déconnexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * @OA\Post(
     *     path="/api/clients/create-account",
     *     operationId="createClientAccount",
     *     tags={"Auth"},
     *     summary="Créer un compte pour un client existant",
     *     description="Crée un compte utilisateur pour un client existant",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"client_id","password"},
     *             @OA\Property(property="client_id", type="integer", example=1),
     *             @OA\Property(property="password", type="string", format="password", example="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Compte client créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="access_token", type="string", example="token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error"
     *     )
     * )
     */
    public function createClientAccount(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $client = Client::findOrFail($request->client_id);

        // Vérifier si un utilisateur existe déjà pour ce client
        if (User::where('email', $client->email)->exists()) {
            return response()->json(['message' => 'User account already exists for this client.'], 422);
        }

        $user = User::create([
            'name' => $client->nom,
            'prenom' => $client->surnom, // Utiliser le surnom du client comme prénom
            'login' => $client->email, // Utilisation de l'email du client comme login
            'email' => $client->email,
            'password' => Hash::make($request->password),
            'etat' => 'actif',
        ]);

        // Assigner le rôle de client
        $user->assignRole('client');

        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
        ], 201);
    }
}

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"id", "name", "prenom", "login", "email"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="prenom", type="string", example="John"),
 *     @OA\Property(property="login", type="string", example="johndoe"),
 *     @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *     @OA\Property(property="etat", type="string", example="actif")
 * )
 */