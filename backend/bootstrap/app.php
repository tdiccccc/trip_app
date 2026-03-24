<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        // API専用バックエンドのため、未認証時のリダイレクト先をnullに設定
        // route('login') が存在しないことによる RouteNotFoundException を防止
        $middleware->redirectGuestsTo(fn () => null);
        $middleware->alias([
            'trip.member' => \App\Http\Middleware\EnsureTripMember::class,
            'trip.owner' => \App\Http\Middleware\EnsureTripOwner::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            // API専用バックエンドのため、常にJSON 401を返す
            return response()->json(['message' => 'Unauthenticated.'], 401);
        });
    })->create();
