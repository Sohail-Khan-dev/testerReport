<?php

namespace App\Providers;

use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Repositories\Interfaces\UserReportRepositoryInterface;
use App\Repositories\ProjectRepository;
use App\Repositories\UserReportRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            UserReportRepositoryInterface::class,
            UserReportRepository::class
        );
        
        $this->app->bind(
            ProjectRepositoryInterface::class,
            ProjectRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
