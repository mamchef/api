<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function ($user = null) {
            // Skip authentication in local environment
            if (app()->environment('local')) {
              //  return true;
            }

            // Production: require HTTP Basic Auth
            $username = request()->getUser();
            $password = request()->getPassword();

            $expectedUsername = env('HORIZON_USERNAME');
            $expectedPassword = env('HORIZON_PASSWORD');

            // If credentials don't match, send 401 with WWW-Authenticate header
            if ($username !== $expectedUsername || $password !== $expectedPassword) {
                abort(401, 'Unauthorized', [
                    'WWW-Authenticate' => 'Basic realm="Horizon"'
                ]);
            }

            return true;
        });
    }
}
