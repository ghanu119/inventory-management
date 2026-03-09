<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\User;
use Native\Desktop\Facades\Menu;
use Native\Desktop\Facades\Window;
use Native\Desktop\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        $appTitle = Company::getAppName();

        // Always open at / so the entry route can show install wizard or redirect to login/dashboard.
        // Use company name as window title so the desktop app feels like the client's business app.
        Window::open()->url(url('/'))->maximized()->title($appTitle);

        if (config('nativephp-internal.running')) {
            $this->registerNativeMenu();
        }
    }

    /**
     * Register the native application menu (Navigate, Help, File, Edit, View, Window).
     */
    protected function registerNativeMenu(): void
    {
        $navigateMenu = Menu::make(
            Menu::link(route('dashboard'), __('Go to Dashboard')),
            Menu::link(route('finance.index'), __('Finance')),
            Menu::separator(),
            Menu::link(route('force-logout'), __('Force Logout'))
        )->label(__('Navigate'));

        $helpMenu = Menu::make(
            Menu::link(route('help.scanner-setup'), __('Setup scanner using mobile (webcam)')),
            Menu::separator(),
            Menu::link(route('help.clear-cache'), __('Clear cache'))
        )->label(__('Help'));

        $dataMenu = Menu::make(
            Menu::link(route('invoices.truncate'), __('Truncate invoices & revert stock')),
            Menu::link(route('customers.truncate'), __('Truncate customers'))
        )->label(__('Data'));

        Menu::create(
            Menu::file(),
            Menu::edit(),
            Menu::view(),
            Menu::window(),
            $navigateMenu,
            $dataMenu,
            $helpMenu
        );
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
        ];
    }
}
