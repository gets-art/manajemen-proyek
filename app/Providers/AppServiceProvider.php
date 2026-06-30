<?php

namespace App\Providers;

use App\Models\Payment;
use App\Models\ProjectBudget;
use App\Observers\PaymentObserver;
use App\Observers\ProjectBudgetObserver;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
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
        Payment::observe(PaymentObserver::class);
        ProjectBudget::observe(ProjectBudgetObserver::class);

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'ar'])
                ->labels([
                    'en' => 'English',
                    'ar' => 'العربية',
                ])
                ->visible(insidePanels: true);
        });
    }
}
