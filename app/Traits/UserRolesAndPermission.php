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
            default => 'Unknown Role',
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
                'view_customers' => 'View Customers',
                'view_drivers' => 'View Drivers',
                'view_trucks' => 'View Trucks',
                'view_atcs' => 'View ATCs',
                'view_transactions' => 'View Transactions',
                'view_truck_movements' => 'View Truck Movements',
                'view_maintenance' => 'View Maintenance',
                'view_reports' => 'View Reports',
                'view_notifications' => 'View Notifications',
            ],
            'operations_manager' => [
                'transaction_management' => 'Transactions Management',
                'assign_atc' => 'Assign ATC',
                'dispatch_trucks' => 'Dispatch Trucks',
                'view_customers' => 'View Customers',
                'edit_customers' => 'Edit Customers',
                'view_drivers' => 'View Drivers',
                'edit_drivers' => 'Edit Drivers',
                'view_trucks' => 'View Trucks',
                'edit_trucks' => 'Edit Trucks',
                'view_atcs' => 'View ATCs',
                'edit_atcs' => 'Edit ATCs',
                'view_transactions' => 'View Transactions',
                'edit_transactions' => 'Edit Transactions',
                'view_truck_movements' => 'View Truck Movements',
                'edit_truck_movements' => 'Edit Truck Movements',
                'view_maintenance' => 'View Maintenance',
                'edit_maintenance' => 'Edit Maintenance',
                'view_reports' => 'View Reports',
                'view_notifications' => 'View Notifications',
            ],
            'staff' => [
                'create_record' => 'Create Record',
                'view_customers' => 'View Customers',
                'create_customers' => 'Create Customers',
                'view_drivers' => 'View Drivers',
                'create_drivers' => 'Create Drivers',
                'view_trucks' => 'View Trucks',
                'create_trucks' => 'Create Trucks',
                'view_atcs' => 'View ATCs',
                'create_atcs' => 'Create ATCs',
                'view_transactions' => 'View Transactions',
                'create_transactions' => 'Create Transactions',
                'view_truck_movements' => 'View Truck Movements',
                'create_truck_movements' => 'Create Truck Movements',
                'view_maintenance' => 'View Maintenance',
                'create_maintenance' => 'Create Maintenance',
                'view_reports' => 'View Reports',
                'view_notifications' => 'View Notifications',
            ],
            default => [],
        };
    }
}
