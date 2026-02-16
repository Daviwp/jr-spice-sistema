<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
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

        // Dynamic SMTP Configuration
        try {
            if (config('app.env') !== 'testing' && \Schema::hasTable('settings')) {
                $settings = \App\Models\Setting::whereIn('key', [
                    'mail_host', 'mail_port', 'mail_username', 'mail_password',
                    'mail_encryption', 'mail_from_address', 'mail_from_name'
                ])->get()->pluck('value', 'key');

                if (isset($settings['mail_host']) && $settings['mail_host']) {
                    config([
                        'mail.default' => 'smtp',
                        'mail.mailers.smtp.host' => $settings['mail_host'],
                        'mail.mailers.smtp.port' => $settings['mail_port'] ?? 587,
                        'mail.mailers.smtp.username' => $settings['mail_username'] ?? null,
                        'mail.mailers.smtp.password' => $settings['mail_password'] ?? null,
                        'mail.mailers.smtp.encryption' => $settings['mail_encryption'] ?? 'tls',
                        'mail.from.address' => $settings['mail_from_address'] ?? config('mail.from.address'),
                        'mail.from.name' => $settings['mail_from_name'] ?? config('mail.from.name'),
                    ]);

                    // Reset Mailer instances so they pick up new config
                    $this->app->forgetInstance('mail.manager');
                    $this->app->forgetInstance('mailer');
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Could not load dynamic SMTP settings: ' . $e->getMessage());
        }
    }
}
