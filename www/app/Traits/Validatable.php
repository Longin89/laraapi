<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


trait Validatable
{
    /**
     * Валидация входящих данных для создания или обновления продукта.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    protected function validateRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|decimal:1|gt:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Все поля обязательны для заполнения и price должна быть больше 0 (с 1 знаком после точки)',
                'errors' => $validator->errors(),
            ], 422);
        };

        return null;
    }

    /**
     * Валидация входящих данных для регистрации пользователя.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|array
     */
    protected function validateRegisterRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Введенные данные некорректны',
                'errors' => $validator->errors(),
            ], 422);
        }

        $fields = $validator->validated();

        $user = User::create($fields);
        $token = $user->createToken($request->name);

        return [
            'user' => $user,
            'token' => $token->plainTextToken,
        ];
    }

    /**
     * Валидация входящих данных для аутентификации пользователя.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|User
     */
    protected function validateLoginRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Неверные email или пароль',
                'errors' => $validator->errors(),
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Неверные email, пароль или юзер не найден',
            ], 401);
        }

        return $user;
    }
}
