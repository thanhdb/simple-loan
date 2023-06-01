<?php

namespace App\Providers;

use App\Repositories\Loan\LoanRepository;
use App\Repositories\Loan\LoanRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\User\UserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
        $this->app->singleton(LoanRepositoryInterface::class, LoanRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
