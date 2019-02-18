<?php

namespace App\Providers;

use App\Models\Article;
use App\Observers\ArticleObserver;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Relations\Relation;

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

        Article::observe(ArticleObserver::class);
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
    }
}
