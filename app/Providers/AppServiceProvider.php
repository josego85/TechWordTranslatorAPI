<?php

namespace App\Providers;

use App\Interfaces\WordRepositoryInterface;
use App\Models\Word;
use App\Repositories\WordRepository;
use App\Repositories\CacheableWordRepository;
use App\Services\CacheService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind CacheService as a singleton
        $this->app->singleton(CacheService::class, function ($app) {
            return new CacheService();
        });

        // Bind WordRepositoryInterface with caching decorator
        $this->app->bind(WordRepositoryInterface::class, function ($app) {
            $repository = new WordRepository(
                $app->make(Word::class)
            );
            
            return new CacheableWordRepository(
                $repository,
                $app->make(CacheService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
