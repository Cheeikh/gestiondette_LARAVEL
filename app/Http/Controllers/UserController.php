<?php

namespace App\Http\Controllers;

use App\Facades\UserFacade;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Http\Request;


class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('login', 'password');
        $result = UserFacade::login($credentials);

        if ($result['status'] === 200) {
            return response()->json($result, 200);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function getUnreadNotifications(Request $request)
    {
        $user = $request->user();

        $notifications = $user->unreadNotifications()->get();

        return response()->json($notifications);
    }

    // Get read notifications for the user
    public function getReadNotifications(Request $request)
    {
        $user = $request->user();

        $notifications = $user->readNotifications()->get();

        return response()->json($notifications);
    }

    public function register(RegisterUserRequest $request)
    {
        $user = UserFacade::registerUser(
            $request->validated(),
            $request->file('photo')
        );

        return response()->json($user, 201);
    }

    public function index(Request $request)
    {
        $role = $request->query('role');
        $active = $request->query('active');

        $activeBool = null;
        if ($active === 'oui') {
            $activeBool = true;
        } elseif ($active === 'non') {
            $activeBool = false;
        }

        if (!$role && is_null($activeBool)) {
            $users = UserFacade::getAllUsers();
        } else {

            $users = UserFacade::getUsersByFilters($role, $activeBool);
        }

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
