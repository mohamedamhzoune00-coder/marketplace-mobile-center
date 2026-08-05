# Marketplace Mobile Center

Marketplace API built with Laravel 8 for a mobile shopping center in Meknes.

## Features

- Authentication (Laravel Sanctum)
- Boutiques Management
- Products Management
- Categories
- Product Images
- Purchase Requests
- Product Reports
- Boutique Working Hours
- Audit Logs

## Tech Stack

- Laravel 8
- PHP 8.0
- MySQL
- Laravel Sanctum
- REST API

## Installation

```bash
git clone https://github.com/mohamedamhzoune00-coder/marketplace-mobile-center.git

cd marketplace-mobile-center

composer install

cp .env.example .env

php artisan key:generate

php artisan migrate

php artisan db:seed

php artisan serve
```

## API

### Public

- Register
- Login
- View Boutiques
- View Categories
- View Products
- Send Purchase Request
- Report Product

### Protected

- Manage Boutiques
- Manage Products
- Manage Images
- Manage Working Hours
- Manage Purchase Requests
- Manage Reports
- View Audit Logs

## Project Structure

```
app/
├── Models
├── Http/
│   ├── Controllers
│   └── Middleware
├── Policies
├── Providers
```

## Author

**Mohamed Amhzoune**

- GitHub: https://github.com/mohamedamhzoune00-coder
- LinkedIn: https://linkedin.com/in/mohamed-amhzoune-dev

## License

This project is for educational purposes.