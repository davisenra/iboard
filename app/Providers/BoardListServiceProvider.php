<?php

namespace App\Providers;

use App\Models\Board;
use Illuminate\Support\Facades;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

class BoardListServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Facades\View::composer('*', function (View $view) {
            $view->with('boards', Board::getBoardsRoutesList());
        });
    }
}
