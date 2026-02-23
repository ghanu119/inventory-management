<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\User;
use Database\Seeders\NativeAdminSeeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Native\Desktop\Facades\System;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (class_exists(\Mccarlosen\LaravelMpdf\Facades\LaravelMpdf::class)) {
            class_alias(\Mccarlosen\LaravelMpdf\Facades\LaravelMpdf::class, 'PDF');
        }

        // In the NativePHP desktop app, avoid using a stale route cache that can cause
        // "Route [invoices.edit] not defined" after install. Force loading routes from web.php.
        if (config('nativephp-internal.running', false)) {
            $path = $this->app->getCachedRoutesPath();
            if ($path && file_exists($path)) {
                @unlink($path);
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Skip during artisan commands (build, migrate, etc.)
        if (app()->runningInConsole()) {
            return;
        }

        // On NativePHP desktop: run optimize:clear once per install/update on the destination system.
        if (config('nativephp-internal.running', false)) {
            $this->runOptimizeClearOncePerInstall();
        }

        Artisan::call('migrate', ['--force' => true]);
        // Seed admin user only if users table exists
        try {
            if (Schema::hasTable('users')) {
                $user = User::where('email', 'admin@virexenterprise.app')->first();

                if (!$user) {
                    // Artisan::call('db:seed', [
                    //     '--class' => NativeAdminSeeder::class,
                    //     '--force' => true,
                    // ]);
                }
            }
        } catch (\Throwable $e) {
            // Prevent crash on first boot
            report($e);
        }

        // Share app/company name with layout so header and title feel like the client's business app.
        View::composer('layouts.app', function ($view) {
            $appName = Company::getAppName();
            $view->with('appName', $appName);
            // In the desktop app, keep window title in sync with company name (e.g. after Settings update).
            if (config('nativephp-internal.running', false) && class_exists(\Native\Desktop\Facades\Window::class)) {
                try {
                    \Native\Desktop\Facades\Window::get('main')->title($appName);
                } catch (\Throwable $e) {
                    // Ignore when not in desktop or window not open.
                }
            }
        });
    }

    /**
     * Run optimize:clear once per install (or per app version) in the NativePHP desktop app.
     * Ensures the destination system has a clean cache state after installation or update.
     */
    private function runOptimizeClearOncePerInstall(): void
    {
        $version = config('nativephp.version', '1.0.0');
        $flagFile = storage_path('framework/.nativephp_optimize_cleared');

        $alreadyRun = file_exists($flagFile) && trim((string) @file_get_contents($flagFile)) === $version;
        if ($alreadyRun) {
            return;
        }

        try {
            Artisan::call('optimize:clear');
            $dir = dirname($flagFile);
            if (! is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            @file_put_contents($flagFile, $version);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
