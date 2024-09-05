<?php

namespace App\Http\Controllers;

use App\Interfaces\UserServiceInterface;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;
use Carbon\Carbon;
use App\Uploads\UploadInterface;



class UserController extends Controller
{
    protected $userService;
    protected $tokenRepository;
    protected $refreshTokenRepository;
    protected $uploadService;


    public function __construct(UserServiceInterface $userService,
    TokenRepository $tokenRepository,
    RefreshTokenRepository $refreshTokenRepository, UploadInterface $uploadService)
    {
        $this->userService = $userService;
        $this->tokenRepository = $tokenRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->uploadService = $uploadService;
    }


    public function login(Request $request)
    {
        $credentials = $request->only('login', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $token->save();

            // Créer un refresh token
            $refreshToken = $this->refreshTokenRepository->create([
                'id' => \Str::random(40),
                'access_token_id' => $token->id,
                'revoked' => false,
                'expires_at' => Carbon::now()->addDays(30),
            ]);

            return response()->json([
                'status' => 200,
                'data' => [
                    'id' => $user->id,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'login' => $user->login,
                    'role_id' => $user->role_id,
                    'role_name' => $user->role->name,
                    'active' => $user->active,
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString(),
                    'refresh_token' => $refreshToken->id,
                ],
                'message' => 'User logged in successfully'
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid credentials'
            ], 401);
        }
    }

    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required'
        ]);

        $refreshToken = $this->refreshTokenRepository->find($request->refresh_token);

        if (!$refreshToken) {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid refresh token'
            ], 400);
        }

        $user = $refreshToken->accessToken->user;
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();

        // Révoquer l'ancien refresh token
        $this->refreshTokenRepository->revokeRefreshTokensByAccessTokenId($refreshToken->access_token_id);

        // Créer un nouveau refresh token
        $newRefreshToken = $this->refreshTokenRepository->create([
            'id' => \Str::random(40),
            'access_token_id' => $token->id,
            'revoked' => false,
            'expires_at' => Carbon::now()->addDays(30),
        ]);

        return response()->json([
            'status' => 200,
            'data' => [
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString(),
                'refresh_token' => $newRefreshToken->id,
            ],
            'message' => 'Token refreshed successfully'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        
        // Révoquer également le refresh token associé
        $this->refreshTokenRepository->revokeRefreshTokensByAccessTokenId($request->user()->token()->id);

        return response()->json([
            'status' => 200,
            'message' => 'Successfully logged out'
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'status' => 200,
            'data' => [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'login' => $user->login,
                'role_id' => $user->role_id,
                'role_name' => $user->role->name,
                'active' => $user->active,
            ],
            'message' => 'User data retrieved successfully'
        ]);
    }

    public function register(RegisterUserRequest $request)
{
    // Upload de la photo si présente, sinon définir un avatar par défaut
    $photoUrl = $request->hasFile('photo')
        ? $this->uploadService->upload($request->file('photo'))
        : 'https://url-to-default-avatar'; // Lien vers l'avatar par défaut

    // Enregistrer l'utilisateur avec la photo
    $userData = array_merge($request->validated(), ['photo' => $photoUrl]);
    $user = $this->userService->registerUser($userData);

    return response()->json([
        'status' => 201,
        'data' => $user,
        'message' => 'User registered successfully'
    ], 201);
}


    public function index()
    {
        $users = $this->userService->getAllUsers();

        return response()->json([
            'status' => 200,
            'data' => $users,
            'message' => 'List of users'
        ], 200);
    }

    public function getByRole(Request $request)
    {
        $role = $request->query('role');
        $active = $request->query('active');

        $users = $active ? 
            $this->userService->getUsersByRoleAndActive($role, $active === 'oui') :
            $this->userService->getUsersByRole($role);

        return response()->json([
            'status' => 200,
            'data' => $users,
            'message' => 'List of users by role'
        ], 200);
    }
}
