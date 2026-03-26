# Multi-Vendor Checkout & Order Engine

A modular, scalable multi-vendor checkout system built with Laravel 12. Customers can add products from multiple vendors to their cart, and on checkout the system splits the cart into separate orders per vendor — simulating a real-world marketplace backend.

## Setup Instructions

```bash
# Clone and install dependencies
git clone <repository-url>
cd multi-vendor
composer install

# Configure environment
cp .env.example .env
# Update DB_* variables in .env for your MySQL connection:
#   DB_CONNECTION=mysql
#   DB_DATABASE=multi_vendor
#   DB_USERNAME=root
#   DB_PASSWORD=your_password

# Generate app key & run migrations
php artisan key:generate
php artisan migrate

# Seed vendors, products, and users
php artisan db:seed

# Start the server
php artisan serve
```

## Sample Credentials

### Users (Customer / Admin)

| Role     | Email                | Password   |
|----------|----------------------|------------|
| Admin    | admin@example.com    | password   |
| Customer | customer@example.com | password   |

### Seeded Vendors

| Vendor       | Email            | Products                                          |
|--------------|------------------|---------------------------------------------------|
| TechGadgets  | tech@vendor.com  | Wireless Mouse, Mechanical Keyboard, USB-C Hub    |
| BookHaven    | books@vendor.com | Laravel Up & Running, Clean Code                  |
| FitLife      | fit@vendor.com   | Yoga Mat, Resistance Bands Set, Water Bottle 1L   |

## Web Routes (Blade UI)

| URL                  | Description                              | Auth Required |
|----------------------|------------------------------------------|---------------|
| `/products`          | Browse all products, add to cart         | No (view only) |
| `/login`             | Login page                               | No            |
| `/register`          | Registration page                        | No            |
| `/cart`              | View cart grouped by vendor, checkout    | Yes           |
| `/checkout` (POST)   | Process checkout                         | Yes           |
| `/orders`            | View customer's own orders               | Yes           |
| `/orders/{id}`       | Order detail (items, totals, payment)    | Yes           |
| `/admin/orders`      | Admin: all orders with filters           | Admin only    |
| `/admin/orders/{id}` | Admin: order detail                      | Admin only    |

## API Endpoints

Base URL: `http://localhost:8000/api`

Authenticated routes require header: `Authorization: Bearer {token}`

### Auth
- `POST /api/register` — Register (name, email, password, password_confirmation)
- `POST /api/login` — Login (email, password) → returns token
- `POST /api/logout` — Logout (auth required)

### Products (public)
- `GET /api/products` — List all products (paginated)
- `GET /api/products/{id}` — Product detail

### Cart (auth required)
- `GET /api/cart` — View cart grouped by vendor
- `POST /api/cart` — Add item (product_id, quantity)
- `PUT /api/cart/{cartItemId}` — Update quantity
- `DELETE /api/cart/{cartItemId}` — Remove item

### Checkout (auth required)
- `POST /api/checkout` — Process checkout, splits cart by vendor

### Customer Orders (auth required)
- `GET /api/orders` — My orders
- `GET /api/orders/{id}` — Order detail

### Admin (auth + admin role required)
- `GET /api/admin/orders` — All orders (filterable: `vendor_id`, `user_id`, `status`)
- `GET /api/admin/orders/{id}` — Order detail

### Scheduled Command
```bash
# Auto-cancel unpaid orders older than 30 minutes (default)
php artisan orders:cancel-unpaid
php artisan orders:cancel-unpaid --minutes=60
```

## Architecture Decisions

- **Service Layer** — `CartService` and `CheckoutService` encapsulate all business logic, keeping controllers thin and focused on HTTP concerns.
- **Events & Listeners** — `OrderPlaced` and `PaymentSucceeded` events decouple order creation from side effects (notifications, order status updates). Listeners log to `laravel.log` (simulating email via `MAIL_MAILER=log`).
- **Pessimistic Locking** — `lockForUpdate()` inside a `DB::transaction()` during checkout prevents inventory race conditions under concurrent requests.
- **Policies & Middleware** — `OrderPolicy` restricts order viewing to the owner or admin. `EnsureAdmin` middleware gates all `/admin/*` routes. `CartPolicy` ensures cart ownership.
- **Form Requests** — Validation logic lives in dedicated `FormRequest` classes (`AddToCartRequest`, `UpdateCartItemRequest`, `LoginRequest`, `RegisterRequest`), not in controllers.
- **Sanctum** — Token-based API authentication for stateless API consumers. Web routes use standard session auth.
- **Dual Interface** — Both a Blade UI (web routes) and a JSON API (api routes) share the same service layer, avoiding code duplication.
- **Modular Structure** — Code is organized into `Services/`, `Events/`, `Listeners/`, `Policies/`, `Http/Controllers/`, `Http/Requests/`, `Http/Middleware/`, and `Console/Commands/`.

## Trade-offs & Assumptions

- **Simulated Payment** — Payment is marked as `paid` immediately during checkout. No real payment gateway is integrated; the `Payment` model and `PaymentSucceeded` event are structured to support real gateway integration later.
- **DB-backed Cart** — Cart is stored in the database (not session) for API compatibility and cross-device persistence.
- **Vendor as Standalone Entity** — Vendors are not tied to user accounts by default. A nullable `user_id` FK exists on the `vendors` table for future vendor-user linking.
- **Stock Atomicity** — Product stock is decremented within a database transaction using row-level locking (`lockForUpdate`), ensuring correctness under concurrent checkouts.
- **Scheduled Cancellation** — The `orders:cancel-unpaid` command runs hourly and restores stock for cancelled orders. The threshold is configurable via `--minutes`.
- **Email as Log** — Order placement notifications are logged to `storage/logs/laravel.log` using Laravel's log mail driver (`MAIL_MAILER=log`), satisfying the mock email requirement.
