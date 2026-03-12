<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\EloquentBoardPostRepository;
use App\Repositories\EloquentExpenseCategoryRepository;
use App\Repositories\EloquentExpenseRepository;
use App\Repositories\EloquentItineraryRepository;
use App\Repositories\EloquentPackingItemRepository;
use App\Repositories\EloquentPhotoRepository;
use App\Repositories\EloquentReactionRepository;
use App\Repositories\EloquentSpotMemoRepository;
use App\Repositories\EloquentSpotRepository;
use App\Repositories\EloquentTripMemberRepository;
use App\Repositories\EloquentTripRepository;
use App\Repositories\EloquentTripSummaryRepository;
use App\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;
use Packages\Domain\Repositories\BoardPostRepositoryInterface;
use Packages\Domain\Repositories\ExpenseCategoryRepositoryInterface;
use Packages\Domain\Repositories\ExpenseRepositoryInterface;
use Packages\Domain\Repositories\ItineraryRepositoryInterface;
use Packages\Domain\Repositories\PackingItemRepositoryInterface;
use Packages\Domain\Repositories\PhotoRepositoryInterface;
use Packages\Domain\Repositories\ReactionRepositoryInterface;
use Packages\Domain\Repositories\SpotMemoRepositoryInterface;
use Packages\Domain\Repositories\SpotRepositoryInterface;
use Packages\Domain\Repositories\TripMemberRepositoryInterface;
use Packages\Domain\Repositories\TripRepositoryInterface;
use Packages\Domain\Repositories\TripSummaryRepositoryInterface;
use Packages\Domain\Repositories\UserRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TripRepositoryInterface::class, EloquentTripRepository::class);
        $this->app->bind(TripMemberRepositoryInterface::class, EloquentTripMemberRepository::class);
        $this->app->bind(SpotRepositoryInterface::class, EloquentSpotRepository::class);
        $this->app->bind(SpotMemoRepositoryInterface::class, EloquentSpotMemoRepository::class);
        $this->app->bind(ItineraryRepositoryInterface::class, EloquentItineraryRepository::class);
        $this->app->bind(PhotoRepositoryInterface::class, EloquentPhotoRepository::class);
        $this->app->bind(BoardPostRepositoryInterface::class, EloquentBoardPostRepository::class);
        $this->app->bind(ReactionRepositoryInterface::class, EloquentReactionRepository::class);
        $this->app->bind(PackingItemRepositoryInterface::class, EloquentPackingItemRepository::class);
        $this->app->bind(ExpenseRepositoryInterface::class, EloquentExpenseRepository::class);
        $this->app->bind(ExpenseCategoryRepositoryInterface::class, EloquentExpenseCategoryRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(TripSummaryRepositoryInterface::class, EloquentTripSummaryRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
