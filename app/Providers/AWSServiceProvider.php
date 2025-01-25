<?php

namespace App\Providers;

use Aws\Sdk as AwsSdk;
use Illuminate\Support\ServiceProvider;

class AWSServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AwsSdk::class, function ($app) {
            return new AwsSdk([
                'credentials' => [
                    'key' => auth()->user()?->aws_access_key_id,
                    'secret' => auth()->user()?->aws_secret_access_key,
                ],
                'region' => auth()->user()?->aws_region,
                'version' => 'latest',
            ]);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
