# Backend Sport Accessories

Laravel and Inertia Vue admin backend for a sport accessories store. The system manages catalog data, customer orders, store content, settings, notifications, and operational workflows from one dashboard.

## Stack

- PHP 8.4 / Laravel 13
- Inertia.js 3 with Vue 3
- Tailwind CSS 4
- MySQL
- Laravel Fortify, Sanctum, Wayfinder
- Spatie Laravel Permission

## Main Features

- Dashboard with revenue, order, customer, and product insights
- Product catalog management: products, categories, brands, colors, sizes, materials, warehouses
- Order management with invoice view, payment status, shipping cost, discount, and line items
- Customer, shipping address, wishlist, review, contact, and support ticket management
- Promotions: coupons, flash deals, feature deals, gift cards, stock clearance, deals of the day
- Content management: blogs, pages, banners, FAQs, testimonials, email templates
- Business settings for site identity, logo, contact details, currency, working hours, and checkout options
- Role and permission management for admin users
- API documentation console for testing customer-facing endpoints

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
```

## Development

Run the full local development stack:

```bash
composer run dev
```

Run only the Vite frontend dev server:

```bash
npm run dev
```

## Testing

Run the PHP test suite:

```bash
php artisan test --compact
```

Run frontend type checks:

```bash
npm run types:check
```

Run production frontend build:

```bash
npm run build
```

## Formatting

Format PHP:

```bash
vendor/bin/pint --dirty --format agent
```

Format frontend files:

```bash
npm run format
```
