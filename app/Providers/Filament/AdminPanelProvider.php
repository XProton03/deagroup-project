<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationItem;
use App\Filament\Resources\UserResource;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\NavigationBuilder;
use App\Filament\Resources\CompanyResource;
use App\Filament\Resources\RegencyResource;
use App\Filament\Resources\VillageResource;
use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\DistrictResource;
use App\Filament\Resources\EmployeeResource;
use App\Filament\Resources\ProvinceResource;
use App\Filament\Resources\QuotationResource;
use App\Filament\Resources\DepartmentResource;
use App\Filament\Resources\JobPositionResource;
use BezhanSalleh\FilamentShield\FilamentShield;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use App\Filament\Resources\EmployementStatusResource;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use BezhanSalleh\FilamentShield\Resources\RoleResource;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->font('Poppins')
            ->favicon(asset('img/icon.png'))
            ->brandName('DEA GROUP')
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Indigo,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            // ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
            //     return $builder
            //         ->items([
            //             NavigationItem::make('Dashboard')
            //                 ->icon('heroicon-o-home')
            //                 ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.pages.dashboard'))
            //                 ->url(fn(): string => Dashboard::getUrl()),
            //             ...EditProfilePage::getNavigationItems(),
            //         ])
            //         ->groups([
            //             NavigationGroup::make('Project Management')
            //                 ->items([
            //                     ...QuotationResource::getNavigationItems(),
            //                 ]),
            //             NavigationGroup::make('Employee')
            //                 ->items([
            //                     ...EmployeeResource::getNavigationItems(),
            //                     ...EmployementStatusResource::getNavigationItems(),
            //                     ...DepartmentResource::getNavigationItems(),
            //                     ...JobPositionResource::getNavigationItems(),
            //                 ]),
            //             NavigationGroup::make('Customer')
            //                 ->items([
            //                     ...CompanyResource::getNavigationItems(),
            //                     ...CustomerResource::getNavigationItems(),
            //                 ]),
            //             NavigationGroup::make('Geolocation')
            //                 ->items([
            //                     ...ProvinceResource::getNavigationItems(),
            //                     ...RegencyResource::getNavigationItems(),
            //                     ...DistrictResource::getNavigationItems(),
            //                     ...VillageResource::getNavigationItems(),
            //                 ]),
            //             NavigationGroup::make('User Management')
            //                 ->items([
            //                     ...UserResource::getNavigationItems(),
            //                     ...RoleResource::getNavigationItems(),
            //                 ]),
            //         ]);
            // })
            ->databaseNotifications()
            ->topNavigation(false)
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->authMiddleware([
                Authenticate::class,
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
            ->plugins([
                FilamentShieldPlugin::make(),
                FilamentEditProfilePlugin::make()
                    ->slug('my-profile')
                    ->setTitle('My Profile')
                    ->setNavigationLabel('My Profile')
                    ->setIcon('heroicon-o-user'),
            ]);
    }
}
