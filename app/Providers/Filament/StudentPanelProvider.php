<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class StudentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('student')
            ->path('student')
            ->login()
            ->colors([
                'primary' => Color::Teal,
            ])
             ->brandLogo('/logo.png')
            ->brandLogoHeight('4rem')
            ->sidebarWidth('16rem')
            ->renderHook(
                'panels::head.end',
                fn (): string => \Illuminate\Support\Facades\Blade::render("@vite('resources/css/app.css')"),
            )
            ->renderHook(
                'panels::sidebar.nav.start',
                function (): string {
                    $user = auth()->user();
                    if (!$user) return '';
                    
                    $admission = $user->admission;
                    if (!$admission) return '';

                    $photoUrl = $admission->student_photo 
                        ? route('admin.student-photo', ['path' => $admission->student_photo])
                        : null;
                    
                    $initials = strtoupper(substr($user->name, 0, 2));
                    $admissionNo = $admission->admission_no ?? 'N/A';
                    $courseName = $admission->enrollments()->first()?->course?->course_name ?? 'Student';
                    
                    $avatarHtml = $photoUrl
                        ? "<img src='{$photoUrl}' class='w-16 h-16 rounded-full object-cover border-2 border-primary-500 shadow-md' />"
                        : "<div class='w-16 h-16 rounded-full bg-gradient-to-tr from-primary-500 to-amber-300 flex items-center justify-center text-white text-xl font-bold shadow-md border-2 border-primary-500/10'>{$initials}</div>";

                    return "
                        <div class='flex flex-col items-center justify-center p-6 border-b border-slate-800 text-center'>
                            <div class='relative mb-3'>
                                {$avatarHtml}
                            </div>
                            <h3 class='text-sm font-semibold text-white'>{$user->name}</h3>
                            <p class='text-xs text-gray-400 mt-0.5'>{$courseName}</p>
                            <p class='text-[10px] bg-primary-950/30 text-primary-400 font-mono px-2 py-0.5 rounded-full mt-2 border border-primary-900/50'>
                                {$admissionNo}
                            </p>
                        </div>
                    ";
                }
            )
            ->discoverResources(in: app_path('Filament/Student/Resources'), for: 'App\Filament\Student\Resources')
            ->discoverPages(in: app_path('Filament/Student/Pages'), for: 'App\Filament\Student\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Student/Widgets'), for: 'App\Filament\Student\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
