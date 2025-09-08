<?php

namespace App\Traits;

trait UserRolesAndPermission
{
    public static function roles(string $role): string
    {
       return match ($role) {
            'admin' => 'Admin',
            'accountant' => 'Accountant',
            'operations_manager' => 'Operations Manager',
            'staff' => 'Data Entry Staff',
        };
    }

    public static function permissions(string $role): array
    {
        return match ($role) {
            'admin' => ['*'],
            'accountant' => [
                'payment_management' => 'Payment Management',
                'view_balances' => 'View Balances',
                'generate_financial_reports' => 'Generate Financial Reports',
            ],
            'operations_manager' => [
                'transaction_management' => 'Transactions Management',
                'assign_atc' => 'Assign ATC',
                'dispatch_trucks' => 'Dispatch Trucks',
            ],
            'staff' => [
                'create_record' => 'Create Record',
            ],
        };
    }
}
