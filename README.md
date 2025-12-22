# LaraBlog - Laravel Blog System

A comprehensive blog system built with Laravel 12, featuring advanced functionality like user roles, commenting system, image handling, and admin panel.

## Features

- User authentication and role-based permissions
- Full blog management (posts, categories, tags)
- Advanced commenting system with nested replies
- Image upload and processing
- Search functionality
- Admin panel
- Responsive design with Bootstrap 5

## Installation

1. Clone the repository
2. Run `composer install`
3. Run `npm install`
4. Copy `.env.example` to `.env` and configure database settings
5. Run `php artisan key:generate`
6. Run `php artisan migrate --seed`
7. Run `php artisan storage:link`
8. Run `npm run build` or `npm run dev`

## Seeding Data

Run the following command to seed initial roles and admin user:
```bash
php artisan db:seed