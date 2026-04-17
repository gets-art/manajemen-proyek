# Filament Construction System

A construction project management system built with **Laravel 12** and **Filament v4**. It centralizes the day-to-day operations of a construction business — clients, workers, suppliers, expenses, and payments — behind a clean admin panel with role-based access and rich reporting.

## Features

- Project management for clients, workers, and suppliers
- Expense and payment tracking
- Role-based access control (admin / staff / viewer)
- 4 dedicated report pages with PDF & Excel export
- Filament v4 admin panel with custom widgets and resources
- Built on Laravel 12 with MySQL

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Admin Panel:** Filament v4
- **Database:** MySQL
- **Auth & Permissions:** Spatie Laravel Permission
- **Exports:** Laravel Excel, DomPDF

## Requirements

- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Node.js & npm (for asset compilation)

## Installation

```bash
# 1. Clone the repository
git clone https://github.com/MostafaMosaad3/filament-construction-system.git
cd filament-construction-system

# 2. Install dependencies
composer install
npm install && npm run build

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Set your database credentials in .env, then run migrations
php artisan migrate --seed

# 5. Create an admin user for the Filament panel
php artisan make:filament-user

# 6. Serve the application
php artisan serve
```

Visit `http://localhost:8000/admin` to access the Filament admin panel.

## Reports

The system includes four report pages with PDF and Excel export, covering:

- Project status and progress
- Expenses breakdown
- Payments and outstanding balances
- Worker / supplier activity

## License

This project is released under the MIT License.

---

Built by [Mostafa Mosaad](https://github.com/MostafaMosaad3)
