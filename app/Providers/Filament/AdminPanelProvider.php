<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
//            ->viteTheme('resources/css/filament/admin/theme.css')
            ->default()
            ->colors([
                'primary' => "#007AFF",
                'secondary' => "#000000",
                'success' => "#008000",
                'danger' => "#FF0000",
                'warning' => "#FFFF00",
                'info' => "#008000",
//                'gray' => "#000000",
                'light' => "#FFFF00",
            ])
//            ->brandLogo(fn () => view('filament.components.brand'))
            ->favicon(asset('images/logo-1.png'))
            ->brandLogo(asset('images/logo-1.png'))
            ->brandLogoHeight('3rem')
//            ->topNavigation() // This enables the top bar
            //            ->brandLogo(asset('images/logo-1.png'))
//            ->brandName('Gudget Guru')// here only the logo shows and this is shown if remove or commented the brandLogo
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
//                Pages\Dashboard::class,
            ])
//            ->renderHook('panels::header.start', fn() => view('filament.admin.brand-nav'))
            ->renderHook(
                'panels::header.start',
                fn() => view('filament.admin.brand-nav')
            )
            ->renderHook(
                'panels::sidebar.header',
                fn() => view('filament.sidebar-content')
            )
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
//                Widgets\AccountWidget::class,
//                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('admin')
            ->collapsedSidebarWidth('50')
            ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop()
            ->login();
    }
}
