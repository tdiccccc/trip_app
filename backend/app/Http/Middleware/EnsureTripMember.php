<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Packages\Domain\Repositories\TripMemberRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

final class EnsureTripMember
{
    public function __construct(
        private readonly TripMemberRepositoryInterface $tripMemberRepository,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $tripId = (int) $request->route('tripId');

        /** @var \App\Models\User $user */
        $user = $request->user();

        $member = $this->tripMemberRepository->findByTripIdAndUserId($tripId, $user->id);

        if ($member === null) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return $next($request);
    }
}
