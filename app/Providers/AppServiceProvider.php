<?php

namespace App\Providers;

use App\Models\FeeComponent;
use App\Models\Transaction;
use App\Observers\FeeComponentObserver;
use App\Observers\TransactionObserver;
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
        FeeComponent::observe(FeeComponentObserver::class);
        Transaction::observe(TransactionObserver::class);
    }
}
