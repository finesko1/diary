<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try
        {
            $request->validate([
                'login' => 'required|string', // Изменяем с email на login
                'password' => 'required|string'
            ]);

            // Определяем тип введенных данных
            $login = $request->login;

            // Проверяем, является ли ввод email
            if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
                $user = User::where('email', $login)->first();
            } else {
                $user = User::where('username', $login)->first();
            }

            if (!$user)
            {
                throw ValidationException::withMessages(['login' => 'Пользователь не найден']);
            }

            if (!Hash::check($request->password, $user->password))
            {
                throw ValidationException::withMessages(['password' => 'Неверный пароль.']);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user_id' => $user->id,
            ]);
        }
        catch(ValidationException $e)
        {
            return response()->json([
                'errors' => $e->errors()
            ], 400);
        }
    }
}
