<?php

namespace amazeeio\LagoonLogs;

use Illuminate\Support\ServiceProvider;

class LagoonLogsServiceProvider extends ServiceProvider
{
    /**
     * Register our package services and configuration.
     */
    public function register(): void
    {
        // Merge our package config with existing config to allow overrides.
        $this->mergeConfigFrom(
            __DIR__.'/../config/logging.php',
            'logging.channels.lagoon'
        );
    }
}
