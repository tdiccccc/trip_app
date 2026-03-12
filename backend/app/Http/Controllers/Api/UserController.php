<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Packages\Application\UseCases\User\GetUserListUseCase;

final class UserController extends Controller
{
    /**
     * GET /api/users
     *
     * 全ユーザーの一覧を返す。旅行作成時のメンバー追加に使用。
     */
    public function index(GetUserListUseCase $useCase): JsonResponse
    {
        $users = $useCase->execute();

        return response()->json([
            'data' => array_map(fn ($u) => $u->toArray(), $users),
        ]);
    }
}
