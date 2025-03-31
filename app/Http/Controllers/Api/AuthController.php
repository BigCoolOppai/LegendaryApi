<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name'  => $validatedData['last_name'],
            'patronymic' => $validatedData['patronymic'], // Убедитесь, что ключ совпадает с ключом в $request->validated()
            'email'      => $validatedData['email'],
            'password'   => Hash::make($validatedData['password']), // <-- Хешируем пароль!
            'birth_date' => $validatedData['birth_date'],
        ]);
        return response()->json([
            'data' => [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'code' => 201,
                'message' => 'Пользователь создан',
            ]
            ], 201);
    }

    public function login(Request $request): JsonResponse 
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }
        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'birth_date' => $user->birth_date,
                    'email' => $user->email,
                ],
                'token' => $token,
            ]
            ], 200);
    }

    public function logout(Request $request):JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return response()->json(null, 204);
        // Альтернатива: Удалить ВСЕ токены пользователя (разлогинит на всех устройствах)
        // $user->tokens()->delete();
        // return response()->json(['message' => 'Successfully logged out from all devices'], 200); // Статус 200 OK в этом случае
    }
}
