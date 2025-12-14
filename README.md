# Project Skeleton (Laravel 12 + Livewire v3 + Filament v3 + Tailwind) - English, LTR & WebView Ready

This repo provides a clean SPA-like skeleton for starting an MVP. Pages work with Livewire v3 and `wire:navigate`, featuring a mobile bottom tab bar. The Filament admin panel is configured for English/LTR, and dates are standard Gregorian.

## Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+ and NPM
- MySQL/PostgreSQL/SQLite (one is sufficient)

## Main Versions & Packages
- Laravel 12
- Livewire v3 (`livewire/livewire`)
- Filament v3 (`filament/filament`)
- Spatie Permission v6 (`spatie/laravel-permission`)
- Breeze v2 (Livewire stack)
- Tailwind + PostCSS + Autoprefixer + `@tailwindcss/forms`

## Quick Setup (Copy-Paste Commands)

1) Clone/Download and install PHP packages:
```bash
composer install
```

2) Setup environment and app key:
```bash
copy .env.example .env  # PowerShell: Copy-Item .env.example .env
php artisan key:generate
```

3) Install frontend dependencies:
```bash
npm install
```

4) Create database and run migrations/seeders:
```bash
php artisan migrate:fresh --seed
```

5) Run development environment (two separate terminals):
```bash
php artisan serve
```
```bash
npm run dev
```

> Default URL: http://127.0.0.1:8000

## Filament v3 Setup (Admin Panel /admin)
1) Create an Admin User (if not seeded):
The seeder creates `admin@example.com` with password `Admin@12345`.

2) Access Control:
Access is restricted to users with the `admin` role via `canAccessPanel` method in `App\Models\User`.

## Roles & Initial Admin
- Spatie Permission migrations are included.
- `database/seeders/RolesAndAdminSeeder.php` creates `admin, editor, student` roles and an initial admin user:
  - Email: `admin@example.com`
  - Password: `Admin@12345`

## Locales, LTR & Fonts
- `config/app.php` is set to `locale=en`, `fallback_locale=en` and `timezone=UTC`.
- Base layout `resources/views/layouts/app.blade.php` uses `<html lang="en" dir="ltr">` and Inter font.

## Livewire v3 SPA-like Feel
- Mobile navigation links use `wire:navigate` for seamless transitions.
- `resources/js/livewire-hooks.js` (if present) or inline scripts listen for navigation events to show a Global Loading Overlay for slow connections.

## Page Structure
- Livewire Pages: `HomePage`, `DomainsPage`, `ResourcesPage`, `ProfilePage`.
- Routes in `routes/web.php`:
  - `/` → `HomePage`
  - `/domains` → `DomainsPage`
  - `/resources` → `ResourcesPage`
  - `/profile` → `ProfilePage` (auth middleware)
- A Bottom Tab Bar is included in the base layout for mobile.

## Basic Auth (Breeze + Livewire)
- Breeze with Livewire stack is installed. Login/Register/Forgot-Password pages are available.
- Registration uses Email + Password only (no mobile field or SMS OTP).

## System Settings
- `system_settings` table and `App\Models\SystemSetting` model.
- Global settings row (`key = global`) stores:
  - Branding: `site_name`, `logo`, `favicon`, `hero_title`, `hero_description`
  - SEO: `seo_title`, `site_description`, `seo_keywords`
  - Static pages content: `terms_content`, `about_content`

## Tailwind/Vite/PostCSS
- `tailwind.config.js` includes paths for purge.
- Production Build:
```bash
npm run build
```

## Admin Panel Access
1) Login with `admin@example.com` / `Admin@12345` at `/admin`.

## Acceptance Criteria
- Pages navigate smoothly with `wire:navigate`.
- Responsive mobile tab bar.
- `/admin` panel works with admin role.
- Standard Gregorian dates.
- Global Loading Overlay for slow navigation.

