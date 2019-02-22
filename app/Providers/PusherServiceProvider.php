<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Pusher\Pusher;

class PusherServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Pusher::class, function ($app) {
            $options = array(
                'cluster' => 'eu',
                'useTLS' => true
            );

            $authKey = env('PUSHER_AUTH_KEY');
            $secretKey = env('PUSHER_SECRET_KEY');
            $appId = env('PUSHER_APP_ID');

            return new Pusher($authKey,$secretKey,$appId, $options);
        });
    }
}
