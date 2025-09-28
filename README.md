# RBC Management System

## Introduction

RBC Management System is a comprehensive daily transaction and fleet management platform built with Laravel and Livewire. This system provides robust tools for managing customers, drivers, trucks, ATCs, and daily transactions.

## Features

-   **Customer Management**: Add, edit, and manage customer information
-   **Driver Management**: Track driver details, photos, and contact information
-   **Truck Management**: Manage truck records, registration, and maintenance
-   **ATC Management**: Handle ATC (Authority to Carry) records and allocations
-   **Daily Transactions**: Record and track daily customer transactions
-   **Payment Tracking**: Manage customer payments and outstanding balances
-   **Truck Movements**: Track daily truck movements and dispatches
-   **Maintenance Records**: Log truck maintenance and associated costs
-   **Comprehensive Reports**: Generate various reports for business insights
-   **User Management**: Role-based access control and user invitations

## Technology Stack

This application is built using:

-   **Laravel 12**: PHP framework for the backend
-   **Livewire 3**: Dynamic frontend components
-   **Laravel Volt**: Single-file Livewire components
-   **Flux UI**: Modern component library
-   **Tailwind CSS**: Utility-first CSS framework
-   **Select2**: Enhanced dropdown components
-   **Chart.js**: Data visualization
-   **PDF Export**: Generate reports in PDF format
-   **Excel Export**: Export data to Excel spreadsheets

## Installation

1. Clone the repository
2. Install dependencies: `composer install`
3. Copy environment file: `cp .env.example .env`
4. Generate application key: `php artisan key:generate`
5. Run database migrations: `php artisan migrate`
6. Seed the database: `php artisan db:seed`
7. Start the development server: `php artisan serve`

## Usage

Access the application through your web browser and log in with your credentials. The system provides an intuitive interface for managing all aspects of your business operations.

## License

The RBC Management System is proprietary software. All rights reserved.
