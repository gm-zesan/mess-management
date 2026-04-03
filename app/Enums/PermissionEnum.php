<?php

namespace App\Enums;

enum PermissionEnum: string
{
    // Dashboard
    case DASHBOARD_VIEW = 'dashboard.view';

    // Meals
    case MEALS_VIEW = 'meals.view';
    case MEALS_CREATE = 'meals.create';
    case MEALS_EDIT = 'meals.edit';
    case MEALS_DELETE = 'meals.delete';

    // Members/Users
    case MEMBERS_VIEW = 'members.view';
    case MEMBERS_CREATE = 'members.create';
    case MEMBERS_EDIT = 'members.edit';
    case MEMBERS_DELETE = 'members.delete';
    case MEMBERS_MANAGE_ROLES = 'members.manage-roles';

    // Expenses
    case EXPENSES_VIEW = 'expenses.view';
    case EXPENSES_CREATE = 'expenses.create';
    case EXPENSES_EDIT = 'expenses.edit';
    case EXPENSES_DELETE = 'expenses.delete';

    // Deposits
    case DEPOSITS_VIEW = 'deposits.view';
    case DEPOSITS_CREATE = 'deposits.create';
    case DEPOSITS_EDIT = 'deposits.edit';
    case DEPOSITS_DELETE = 'deposits.delete';

    // Months
    case MONTHS_VIEW = 'months.view';
    case MONTHS_CREATE = 'months.create';
    case MONTHS_EDIT = 'months.edit';
    case MONTHS_DELETE = 'months.delete';
    case MONTHS_CLOSE = 'months.close';

    // Reports
    case REPORTS_VIEW = 'reports.view';

    public function label(): string
    {
        return match ($this) {
            self::DASHBOARD_VIEW => 'View Dashboard',
            self::MEALS_VIEW => 'View Meals',
            self::MEALS_CREATE => 'Create Meals',
            self::MEALS_EDIT => 'Edit Meals',
            self::MEALS_DELETE => 'Delete Meals',
            self::MEMBERS_VIEW => 'View Members',
            self::MEMBERS_CREATE => 'Create Members',
            self::MEMBERS_EDIT => 'Edit Members',
            self::MEMBERS_DELETE => 'Delete Members',
            self::MEMBERS_MANAGE_ROLES => 'Manage Member Roles',
            self::EXPENSES_VIEW => 'View Expenses',
            self::EXPENSES_CREATE => 'Create Expenses',
            self::EXPENSES_EDIT => 'Edit Expenses',
            self::EXPENSES_DELETE => 'Delete Expenses',
            self::DEPOSITS_VIEW => 'View Deposits',
            self::DEPOSITS_CREATE => 'Create Deposits',
            self::DEPOSITS_EDIT => 'Edit Deposits',
            self::DEPOSITS_DELETE => 'Delete Deposits',
            self::MONTHS_VIEW => 'View Months',
            self::MONTHS_CREATE => 'Create Months',
            self::MONTHS_EDIT => 'Edit Months',
            self::MONTHS_DELETE => 'Delete Months',
            self::MONTHS_CLOSE => 'Close Month',
            self::REPORTS_VIEW => 'View Reports',
        };
    }
}
