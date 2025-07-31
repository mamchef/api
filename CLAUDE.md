# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### Primary Development
- `composer run dev` - Start full development environment (server, queue, logs, vite)
- `php artisan serve` - Start Laravel development server
- `php artisan queue:listen --tries=1` - Start queue worker
- `php artisan pail --timeout=0` - Start log viewer
- `npm run dev` - Start Vite development server

### Testing
- `composer run test` - Run PHPUnit tests (clears config first)
- `php artisan test` - Run tests directly

### Database
- `php artisan migrate` - Run database migrations
- `php artisan db:seed` - Run database seeders

### Asset Building
- `npm run build` - Build assets for production
- `npm run dev` - Build assets for development

### Queue Management
- `php artisan horizon` - Start Laravel Horizon for queue monitoring
- `php artisan queue:work` - Process queue jobs

### Docker Development
- `docker-compose up -d` - Start containerized development environment
  - App: http://localhost:9091
  - WebSocket (Reverb): http://localhost:9094
  - Database: localhost:9092
  - Redis: localhost:9093

## Architecture Overview

### Application Structure
This is a Laravel-based food delivery platform with dual-sided marketplace functionality:

**Core Entities:**
- **Users**: End customers who place orders
- **Chefs**: Food providers who manage chef stores and fulfill orders
- **ChefStores**: Chef-owned restaurants/kitchens
- **Foods**: Menu items with customizable options
- **Orders**: Customer purchases with status tracking
- **Tickets**: Support system for both users and chefs

### API Architecture
- **V1 REST API** with three main route groups:
  - `/api/chef/*` - Chef-side functionality (auth required via `chef-auth` middleware)
  - `/api/user/*` - User-side functionality (auth required via `user-auth` middleware)
  - `/api/public/*` - Public endpoints (cities, countries, app version)

### Authentication System
- **Laravel Sanctum** for API token authentication
- **Dual token system**: separate tokens for users (`user-token`) and chefs (`chef-token`)
- **OTP verification** for registration and login
- **Social login** support (Google, Facebook)

### Key Services Architecture
- **Service Layer Pattern**: Business logic in dedicated service classes with interfaces
- **DTO Pattern**: Data Transfer Objects for type-safe data handling
- **Repository Pattern**: Implicit through Eloquent models
- **Observer Pattern**: OrderObserver for order status changes

### Real-time Features
- **Laravel Reverb** for WebSocket connections
- **FCM Push Notifications** for mobile apps
- **Broadcasting** for real-time order updates
- **Laravel Horizon** for queue monitoring

### Payment Integration
- **Stripe** payment gateway with webhook handling
- **User credit system** with transaction tracking
- **Multiple payment methods** support

### File Storage
- **Private storage** for chef documents and ticket attachments
- **Public storage** for food images and chef store profiles
- **Organized by entity type** (chef/{id}/, tickets/{id}/)

## Code Patterns

### Request Handling
- Form requests extend `BaseFormRequest`
- DTOs for data transformation between layers
- API Resources for response formatting
- Middleware for authentication and response normalization

### Model Relationships
- Polymorphic relationships for notifications and FCM tokens
- Soft deletes on User model
- Enum casting for status fields
- UUID fields alongside auto-increment IDs

### Service Interfaces
All major services have interfaces in `app/Services/Interfaces/`:
- UserAuthServiceInterface, ChefAuthServiceInterface
- OrderServiceInterface, NotificationServiceInterface
- PaymentServiceInterface with gateway pattern

### Admin Panel
- **Filament** admin panel configured in `app/Providers/Filament/AdminPanelProvider.php`
- Admin model with separate authentication

## Database
- **MariaDB 10.6** as primary database
- **Redis** for caching and sessions
- **Migration-driven schema** with comprehensive foreign key relationships
- **Seeders** for tags and initial data

## Key Integrations
- **DocuSign** for chef contract signing
- **Firebase** for push notifications
- **Google API** for social authentication
- **Slack** notifications for OTP and system alerts