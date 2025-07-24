<?php

declare(strict_types=1);

namespace App\Providers;

use App\Interfaces\TranslationRepositoryInterface;
use App\Interfaces\WordRepositoryInterface;
use App\Models\Translation;
use App\Models\Word;
use App\Repositories\CacheableWordRepository;
use App\Repositories\TranslationRepository;
use App\Repositories\WordRepository;
use App\Services\CacheService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[\Override]
    public function register(): void
    {
        // Bind CacheService as a singleton
        $this->app->singleton(CacheService::class, fn($app) => new CacheService);

        // Bind WordRepositoryInterface with caching decorator
        $this->app->bind(WordRepositoryInterface::class, function($app) {
            $repository = new WordRepository(
                $app->make(Word::class)
            );

            return new CacheableWordRepository(
                $repository,
                $app->make(CacheService::class)
            );

            return $repository;
        });

        $this->app->bind(TranslationRepositoryInterface::class, function($app) {
            $repository = new TranslationRepository(
                $app->make(Translation::class)
            );

            // return new CacheableTranslationRepository(
            //     $repository,
            //     $app->make(CacheService::class)
            // );

            return $repository;
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
