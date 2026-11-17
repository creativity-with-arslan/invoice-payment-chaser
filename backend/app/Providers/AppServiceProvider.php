<?php

namespace App\Providers;

use App\Support\CurrentWorkspace;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(CurrentWorkspace::class, function () {
    return new CurrentWorkspace();
});
    }

    public function boot(): void
    {
        //
    }
}