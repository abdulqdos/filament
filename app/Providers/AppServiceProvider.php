<?php

namespace App\Providers;

//use Filament\Actions\CreateAction;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
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
        Model::unguard();
//        CreateAction::configureUsing(function ($action) {
//            return $action->slideOver();
//        });
    }
}
