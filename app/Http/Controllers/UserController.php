<?php

namespace App\Http\Controllers;

use App\Facades\UserFacade;
use App\Http\Requests\RegisterUserRequest;
use App\Interfaces\UploadInterface;
use Illuminate\Http\Request;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;

class UserController extends Controller
{
    protected $tokenRepository;
    protected $refreshTokenRepository;
    protected $uploadService;

    public function __construct(
        TokenRepository $tokenRepository,
        RefreshTokenRepository $refreshTokenRepository,
        UploadInterface $uploadService
    ) {
        $this->tokenRepository = $tokenRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->uploadService = $uploadService;
    }

    public function login(Request $request)
    {
        $credentials = $request->only('login', 'password');
        $result = UserFacade::login($credentials);

        if ($result['status'] === 200) {
            return response()->json($result, 200);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }


    public function register(RegisterUserRequest $request)
    {
        $user = UserFacade::registerUser(
            $request->validated(),
            $request->file('photo')
        );

        return response()->json($user, 201);
    }

    public function index()
    {
        $users = UserFacade::getAllUsers();

        return response()->json($users, 200);
    }

    public function getByRole(Request $request)
    {
        $role = $request->query('role');
        $active = $request->query('active');

        $users = $active
            ? UserFacade::getUsersByRoleAndActive($role, $active === 'oui')
            : UserFacade::getUsersByRole($role);

        return response()->json($users, 200);
    }

    public function logout(Request $request)
    {
        UserFacade::logout($request->user()->token());

        return response()->json(null, 200);
    }

    public function refresh(Request $request)
    {
        $request->validate(['refresh_token' => 'required']);

        try {
            $result = UserFacade::refresh($request->input('refresh_token'));
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(null, 400);
        }
    }

    public function user(Request $request)
    {
        $user = UserFacade::getUserData($request->user());

        return response()->json($user, 200);
    }
}
