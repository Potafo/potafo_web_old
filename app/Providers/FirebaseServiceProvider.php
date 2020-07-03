<?php

namespace App\Providers;

use Kreait\Firebase;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Factory as FirebaseFactory;
use Illuminate\Support\ServiceProvider;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Firebase::class, function() {
            return (new FirebaseFactory())
                ->withServiceAccount(ServiceAccount::fromJsonFile(('live-tracking-3a266-firebase-adminsdk-eh7ey-e06f042124.json')))
                ->withDatabaseUri('https://live-tracking-3a266.firebaseio.com')
                ->create();
        });

        $this->app->alias(Firebase::class, 'firebase');
    }
}