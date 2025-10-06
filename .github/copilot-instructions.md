# Copilot Instructions for qly_hthao Laravel Project

## Project Overview
This is a **Laravel 9 application** running on XAMPP (Windows development environment). The project name suggests it's a Vietnamese management system ("qly" = quản lý = management, "hthao" likely refers to the domain/organization).

## Architecture & Key Components

### Development Environment
- **XAMPP Setup**: Project runs at `c:\xampp\htdocs\qly_hthao\qlyhoithao\`
- **Database**: MySQL via XAMPP (`DB_HOST=127.0.0.1`, `DB_DATABASE=laravel`)
- **Frontend**: Vite + Laravel Mix for asset compilation
- **Authentication**: Laravel Sanctum for API tokens

### Essential Commands (Windows PowerShell)
```powershell
# Start development server
php artisan serve

# Asset compilation
npm run dev          # Development with hot reload
npm run build        # Production build

# Database operations
php artisan migrate
php artisan db:seed
php artisan migrate:fresh --seed

# Clear caches (common debug step)
php artisan config:clear; php artisan cache:clear; php artisan view:clear
```

## Project Structure Patterns

### Controllers
- Base controller: `app/Http/Controllers/Controller.php` uses standard Laravel traits
- Follow Laravel resource controller conventions: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`
- API routes go in `routes/api.php` with `auth:sanctum` middleware for protected endpoints

### Models & Database
- Models in `app/Models/` namespace with Eloquent conventions
- User model uses `HasApiTokens`, `HasFactory`, `Notifiable` traits
- Migrations follow Laravel timestamp naming: `YYYY_MM_DD_HHMMSS_description`
- Use mass assignment protection (`$fillable`) and hidden attributes (`$hidden`)

### Frontend Assets
- Entry points: `resources/css/app.css`, `resources/js/app.js`  
- Blade templates in `resources/views/`
- Vite configuration supports hot reload and asset versioning
- Use `@vite(['resources/css/app.css', 'resources/js/app.js'])` in Blade templates

### Testing
- Feature tests in `tests/Feature/` for HTTP endpoints
- Unit tests in `tests/Unit/` for isolated logic
- Use `RefreshDatabase` trait for database tests
- Run tests: `php artisan test` or `vendor/bin/phpunit`

## Configuration Specifics

### Environment Variables
- Default MySQL config for XAMPP: host `127.0.0.1`, port `3306`, no password
- Mail configured for Mailpit (`MAIL_HOST=mailpit`, `MAIL_PORT=1025`)
- File-based sessions and cache (suitable for single-server XAMPP setup)
- Debug mode enabled (`APP_DEBUG=true`) for development

### Middleware & Security
- CSRF protection enabled for web routes
- Sanctum for API authentication
- Rate limiting configured in `app/Http/Kernel.php`
- CORS configured in `config/cors.php`

## Vietnamese Context Considerations
Given the project name pattern, expect:
- Vietnamese language content in views and validation messages
- Date/time formatting for Vietnamese locale
- Potential integration with Vietnamese business processes
- Consider UTF-8 encoding for Vietnamese characters in database and responses

## Development Workflow
1. **Database First**: Create migrations before models
2. **API Routes**: Use `auth:sanctum` middleware for protected endpoints
3. **Validation**: Use Form Requests for complex validation logic
4. **Resources**: Use API Resources for consistent JSON responses
5. **Testing**: Write feature tests for all controller actions

## Common Issues & Solutions
- **XAMPP MySQL**: Ensure MySQL service is running in XAMPP Control Panel
- **Permissions**: Check `storage/` and `bootstrap/cache/` are writable
- **Composer**: Use `composer install --no-dev` for production
- **Assets**: Run `npm install` then `npm run build` for deployment
- **Environment**: Copy `.env.example` to `.env` and generate `APP_KEY` with `php artisan key:generate`

