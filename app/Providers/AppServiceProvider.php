<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Force HTTPS when behind a proxy (e.g. ngrok) to prevent Mixed Content
        if (!app()->runningInConsole()) {
            $request = $this->app['request'];
            $forwardedProto = $request->server->get('HTTP_X_FORWARDED_PROTO');
            $isNgrok = str_contains($request->getHost() ?? '', 'ngrok');

            if ($forwardedProto === 'https' || $isNgrok) {
                // Ensure asset() and url() generate https links
                URL::forceScheme('https');

                $host = $request->headers->get('X-Forwarded-Host') ?: $request->getHost();
                if (!empty($host)) {
                    URL::forceRootUrl('https://' . $host);
                }
            }
        }
    }
}
