<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
// use RectorLaravel\Set\LaravelLevelSetList;
use Rector\Set\ValueObject\SetList;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/bootstrap',
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/resources',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->withPhpVersion(80400) // ✅ PHP 8.4
    ->withPhpSets()
    ->withSets([
        //     LaravelLevelSetList::UP_TO_LARAVEL_110,
        //     LaravelLevelSetList::UP_TO_LARAVEL_120,

        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_COLLECTION,

        SetList::CODE_QUALITY,
        //     SetList::DEAD_CODE,
    ]);
