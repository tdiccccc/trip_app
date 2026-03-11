<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Packages\Application\UseCases\Auth\GetAuthenticatedUserUseCase;
use Packages\Application\UseCases\Auth\LoginUseCase;

final class AuthController extends Controller
{
    /**
     * POST /api/login
     *
     * SPA Cookie 認証でログインし、ユーザー情報を返す。
     */
    public function login(LoginRequest $request, LoginUseCase $useCase): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['メールアドレスまたはパスワードが正しくありません。'],
            ]);
        }

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $dto = $useCase->execute(
            id: $user->id,
            name: $user->name,
            email: $user->email,
        );

        return response()->json(['data' => $dto->toArray()]);
    }

    /**
     * POST /api/logout
     *
     * セッションを破棄してログアウトする。
     */
    public function logout(Request $request): Response
    {
        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->noContent();
    }

    /**
     * GET /api/user
     *
     * 認証済みユーザーの情報を返す。
     */
    public function me(Request $request, GetAuthenticatedUserUseCase $useCase): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $dto = $useCase->execute(
            id: $user->id,
            name: $user->name,
            email: $user->email,
        );

        return response()->json(['data' => $dto->toArray()]);
    }
}
