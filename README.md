# 🚀 Larablog - Modern Laravel Blog Platform

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat-square&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-blue?style=flat-square)](LICENSE)
[![PRs Welcome](https://img.shields.io/badge/PRs-Welcome-brightgreen?style=flat-square)](CONTRIBUTING.md)

<p align="center">
  <img src="./larablog pages/larablog banner.png" alt="Larablog Banner" width="100%">
</p>

## 📖 Overview

**Larablog** is a production-ready, enterprise-grade blogging platform built with Laravel 12. This project demonstrates modern software engineering practices with a focus on clean architecture, security, performance optimization, and user experience. Unlike typical tutorial projects, Larablog implements sophisticated patterns including policy-based authorization, event-driven notifications, polymorphic relationships, and comprehensive admin tooling.

> **"This isn't just another blog tutorial—it's a showcase of professional PHP engineering at scale."**

## ✨ Key Features

### 🔐 Advanced Authentication & Authorization
- **Role-Based Access Control** with Spatie Permissions (Admin, Editor, Author, User)
- **Granular Policy Enforcement** - Users can only edit their own posts, editors manage all content
- **User Ban System** with soft-delete capabilities and automatic session termination
- **Secure Password Reset** flow with token expiration

### 📝 Content Management
- **Rich Post Editor** with image uploads, category/tag assignment
- **Draft/Publish Workflow** with scheduled publishing support
- **Polymorphic Comment System** - Supports flat-threaded discussions with approval workflow
- **SEO-Optimized URLs** with automatic slug generation and uniqueness handling

### 👥 User Experience
- **Modern Instagram-like Comment Interface** - Clean, flat-threaded design
- **Author Profiles** with dedicated pages, bios, and social links
- **Real-time Search** with debounce and AJAX suggestions
- **Responsive Design** - Works flawlessly on mobile, tablet, and desktop

### 👨‍💻 Admin Dashboard
- **Comprehensive Content Moderation** - Manage posts, comments, categories, tags
- **User Management** - View profiles, ban/unban users, assign roles
- **Pending Comments Queue** - Approve/reject user comments with notifications
- **Activity Monitoring** - Track content changes and user actions

### ⚡ Performance & Scalability
- **Eager Loading Optimization** - Eliminates N+1 query problems
- **Image Processing Service** - Automatic resizing and optimization
- **Queue-Based Notifications** - Non-blocking email delivery
- **Caching Strategies** - Optimized for high-traffic scenarios

### 🔧 Developer Experience
- **Clean Architecture** - Separation of concerns with services, events, listeners
- **Comprehensive Validation** - Form requests with custom rules
- **Transaction Management** - Atomic operations for data integrity
- **Detailed Logging** - Custom log formatters for debugging

## 🛠 Technology Stack

### Core Technologies
- **Framework**: Laravel 12.x
- **Language**: PHP 8.1+
- **Database**: MySQL 8.0+
- **Frontend**: Blade templates, Bootstrap 5, Vanilla JavaScript

### Key Packages
| Package | Purpose |
|---------|---------|
| `spatie/laravel-permission` | Role-based access control |
| `intervention/image` | Image processing and optimization |
| `laravel/sanctum` | API authentication (future-proofing) |
| `laravel/ui` | Authentication scaffolding |

### Development Tools
- **Testing**: PHPUnit, Pest (optional)
- **Styling**: Bootstrap5, custom CSS architecture
- **Build Tools**: Vite, npm

## 🚀 Installation

### Prerequisites
- PHP 8.1 or higher
- Composer 2.5+
- Node.js 18+ and npm
- MySQL 8.0+ or compatible database
- Git

### Step-by-Step Setup

```bash
# 1. Clone the repository
git clone https://github.com/Shahrokh1383/larablog.git
cd larablog

# 2. Install PHP dependencies
composer install --optimize-autoloader --no-dev

# 3. Install JavaScript dependencies
npm install
npm run build

# 4. Create environment file and generate application key
cp .env.example .env
php artisan key:generate

# 5. Configure database settings in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=larablog
DB_USERNAME=root
DB_PASSWORD=

# 6. Run migrations and seed initial data
php artisan migrate --seed

# 7. Create symbolic link for storage
php artisan storage:link

# 8. Start the development server
php artisan serve
```

### Default Credentials
After seeding the database, you can login with:
- **Email**: admin@larablog.test
- **Password**: password123

> **Note**: Change these credentials immediately after first login!

## ⚙️ Configuration

### Environment Variables
Create a `.env` file based on `.env.example` and configure the following critical settings:

```env
# Application
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=larablog
DB_USERNAME=root
DB_PASSWORD=

# File Storage
FILESYSTEM_DISK=public
IMAGE_QUALITY=85
THUMBNAIL_SIZES={"small":"400x300","medium":"800x600","large":"1200x630"}

# Mail Configuration (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Queue Configuration (for notifications)
QUEUE_CONNECTION=sync
```

### Image Processing Configuration
Customize image processing settings in `config/image.php`:

```php
return [
    'quality' => env('IMAGE_QUALITY', 85),
    'sizes' => [
        'small' => [400, 300],
        'medium' => [800, 600],
        'large' => [1200, 630],
        'social' => [1200, 630]
    ],
    'watermark' => [
        'enabled' => false,
        'path' => resource_path('images/watermark.png'),
        'position' => 'bottom-right',
        'padding' => 10
    ]
];
```

## 🖼️ Screenshots

### Public Interface
<p align="center">
  <img src="./larablog pages/Blog Posts - Laravel Blog Platform.png" alt="Larablog Posts" width="100%">
</p>
*Blog listing page with featured posts and category filtering*

<p align="center">
  <img src="./larablog pages/laravel post - Laravel Blog Platform.png" alt="Larablog PostPage" width="100%">
</p>
*Post detail page with flat-threaded comments and author information*

### Admin Dashboard
<p align="center">
  <img src="./larablog pages/Manage Posts - LaraBlog Admin.png" alt="Larablog Postmanagement" width="100%">
</p>
*Admin dashboard with analytics and quick actions*

<p align="center">
  <img src="./larablog pages/User Management - LaraBlog Admin.png" alt="Larablog Usermanagement" width="100%">
</p>
*User management interface with ban/unban capabilities*

## 🏗️ Architecture Highlights

### Clean Code Principles
- **Single Responsibility Principle**: Each class has one reason to change
- **Dependency Injection**: Services are injected rather than instantiated
- **Policy-Based Authorization**: Centralized access control logic
- **Event-Driven Notifications**: Decoupled notification system

### Performance Optimizations
```php
// Example: Eager loading to prevent N+1 queries
$posts = Post::with([
    'author:id,name,avatar',
    'categories:id,name,slug',
    'tags:id,name,slug',
    'comments' => function($query) {
        $query->where('approved', true)
              ->with('author:id,name,avatar');
    }
])->published()->latest()->paginate(10);
```

### Security Best Practices
- **Mass Assignment Protection**: All models use `$fillable` properties
- **Validation**: All user input validated through Form Requests
- **Authorization**: Every controller action checks permissions
- **CSRF Protection**: Built-in Laravel middleware
- **XSS Prevention**: Blade templating automatically escapes output

### Database Design
<p align="center">
  <img src="./larablog pages/larablog database design.png" alt="Larablog Database" width="100%">
</p>
*Normalized database schema with proper relationships*

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

1. **Fork** the repository
2. Create a **feature branch** (`git checkout -b feature/your-feature`)
3. **Commit** your changes (`git commit -am 'Add some feature'`)
4. **Push** to the branch (`git push origin feature/your-feature`)
5. Create a **Pull Request**

### Code Style Guidelines
- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards
- Use `php-cs-fixer` for automatic formatting
- Write descriptive commit messages
- Update documentation when necessary

## 📜 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

<p align="center">
  <img src="https://via.placeholder.com/800x200/FF2D20/FFFFFF?text=Built+with+❤️+using+Laravel" alt="Built with Laravel" width="100%">
</p>

---
