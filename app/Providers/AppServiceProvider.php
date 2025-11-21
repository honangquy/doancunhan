<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\HoiThao;
use App\Observers\HoiThaoObserver;
use App\View\Composers\AdminStatsComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Auto-create CHAIR role when conference is created/updated
        HoiThao::observe(HoiThaoObserver::class);

        // Share admin statistics with admin layout
        View::composer('layouts.admin', AdminStatsComposer::class);
    }
}
