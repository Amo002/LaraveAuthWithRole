<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PragmaRX\Google2FAQRCode\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle;
use BaconQrCode\Writer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Google2FA::class, function ($app) {
            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);

            $google2fa = new Google2FA();
            $google2fa->setQrCodeService($writer);

            return $google2fa;
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
