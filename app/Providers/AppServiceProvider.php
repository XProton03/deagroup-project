<?php

namespace App\Providers;

use App\Models\Task;
use App\Models\JobCost;
use App\Observers\TaskObserver;
use App\Observers\JobCostObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Task::observe(TaskObserver::class);
        JobCost::observe(JobCostObserver::class);
    }
}
