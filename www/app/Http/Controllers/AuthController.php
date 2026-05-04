<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\Validatable;

class AuthController extends Controller
{
    use Validatable;


    /**
     * Регистрация нового пользователя.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function register(Request $request)
    {

        $validated = $this->validateRegisterRequest($request);
        if ($validated) {
            return $validated;
        }
    }

    /**
     * Авторизация пользователя и выдача токена.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function login(Request $request)
    {
        $validated = $this->validateLoginRequest($request);

        if ($validated instanceof JsonResponse) {
            return $validated;
        }

        $token = $validated->createToken($validated->email);

        return [
            'status' => true,
            'message' => 'Вход выполнен успешно',
            'token' => $token->plainTextToken,
        ];
    }

    /**
     * Выход пользователя и удаление всех токенов.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return [
            'message' => 'Вы успешно вышли из системы и удалили токены',
        ];
    }
}
