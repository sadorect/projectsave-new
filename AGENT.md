# AGENT.md - ProjectSave International Ministry Website

## Build/Test Commands
- `composer install` - Install PHP dependencies
- `npm install && npm run build` - Build frontend assets with Vite
- `php artisan serve` - Start development server
- `php artisan test` - Run all tests (PHPUnit)
- `php artisan test --filter TestName` - Run single test
- `vendor/bin/phpunit tests/Feature/SpecificTest.php` - Run specific test file
- `php artisan migrate` - Run database migrations
- `php artisan pint` - Code formatting (Laravel Pint)

## Architecture & Structure
- **Laravel 10** ministry website with MySQL database
- **Key Models**: User, Course, Lesson, Partner, Event, Post, Activity
- **Authentication**: Laravel Sanctum + Breeze, email verification required
- **Database**: MySQL with comprehensive migrations in `database/migrations/`
- **Frontend**: Vite + TailwindCSS + AlpineJS + jQuery/Bootstrap hybrid
- **APIs**: YouTube integration, Google reCAPTCHA, file uploads
- **Features**: LMS system, blog, events, volunteer programs, admin dashboard

## Code Style & Conventions
- **PHP**: PSR-12 standards, use Laravel Pint for formatting
- **Imports**: Group by vendor, then app namespaces
- **Models**: Eloquent relationships, use `$fillable`, cast dates/booleans
- **Controllers**: Extend base Controller, authorize requests, validate input
- **Routes**: RESTful conventions, group by auth/admin middleware
- **Views**: Blade templates with component organization
- **JS**: ES6 modules, Alpine.js for reactivity, avoid jQuery where possible
