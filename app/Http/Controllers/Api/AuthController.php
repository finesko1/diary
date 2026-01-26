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
                'email' => 'required|string|email',
                'password' => 'required|string'
            ]);

            $user = User::where('email', $request->email)->first();
            if (!$user)
            {
                throw ValidationException::withMessages(['email' => 'Email не существует']);
            }
            if (!Hash::check($request->password, $user->password))
            {
                {
                    throw ValidationException::withMessages(['password' =>'Неверный пароль.']);
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
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
