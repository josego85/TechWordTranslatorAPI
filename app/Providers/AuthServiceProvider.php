<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Translation;
use App\Models\Word;
use App\Policies\TranslationPolicy;
use App\Policies\WordPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Word::class => WordPolicy::class,
        Translation::class => TranslationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
