<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Collection;
use App\Models\Collector;
use App\Models\Poster;
use App\Observers\ArticleObserver;
use App\Observers\CollectionObserver;
use App\Observers\CollectorObserver;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Relations\Relation;
use Laravel\Horizon\Horizon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale('zh');
        Schema::defaultStringLength(191);
        CarbonInterval::setLocale('zh');
        Carbon::useMonthsOverflow(false);

        Relation::morphMap([
            'category' => 'App\Models\PosterCategory',
            'brand' => 'App\Models\Brand',
        ]);

        Horizon::auth(function () {
           return Auth::guard('admin')->check();
        });

        Article::observe(ArticleObserver::class);
        Poster::observe(PosterObserver::class);
        Collection::observe(CollectionObserver::class);
        Collector::observe(CollectorObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        \API::error(function (\Illuminate\Auth\Access\AuthorizationException $exception) {
            abort(403, $exception->getMessage());
        });
    }
}
