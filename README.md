# Laravel Visit Tracker

Easily track page and model visits with morph visitor model.

## Installation

```bash
composer require shankar/laravel-visit-tracker
```

Publish config and migrations:
```bash
php artisan vendor:publish --tag=visit-tracker-config
php artisan vendor:publish --tag=visit-tracker-migrations
php artisan migrate
```

## Usage

### In model
