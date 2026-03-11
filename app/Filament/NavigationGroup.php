<?php

namespace App\Filament;

use Filament\Support\Contracts\HasLabel;

enum NavigationGroup: string implements HasLabel
{
    case ProjectManagement = 'project-management';
    case HrManagement = 'hr-management';
    case Procurement = 'procurement';
    case Reports = 'reports';
    case Finance = 'finance';
    case Catalog = 'catalog';
    case CMS = 'cms';
    case Settings = 'settings';

    public function getLabel(): string
    {
        return match ($this) {
            self::ProjectManagement => __('app.nav_groups.project_management'),
            self::HrManagement => __('app.nav_groups.hr_management'),
            self::Procurement => __('app.nav_groups.procurement'),
            self::Reports => __('app.nav_groups.reports'),
            self::Finance => __('app.nav_groups.finance'),
            self::Catalog => __('app.nav_groups.catalog'),
            self::CMS => __('app.nav_groups.cms'),
            self::Settings => __('app.nav_groups.settings'),
        };
    }
}
