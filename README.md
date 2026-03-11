# Event Ticketing Application — Laravel Backend

A RESTful API backend for an event ticketing system built with Laravel 11, MySQL, and Stripe.

---

## Tech Stack

- **Framework:** Laravel 11
- **Database:** MySQL
- **Authentication:** Laravel Sanctum (token-based)
- **Payments:** Stripe
- **PHP:** >= 8.2

---

## Setup Instructions

### 1. Clone & Install Dependencies

```bash
git clone <repo-url>
cd event-ticketing-backend
composer install
```

### 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your values:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=event_ticketing
DB_USERNAME=root
DB_PASSWORD=

STRIPE_KEY=pk_test_xxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxx

FRONTEND_URL=http://localhost:5173
```

### 3. Run Migrations & Seeders

```bash
php artisan migrate --seed
```

This will create all tables and seed:
- 1 Admin user: `admin@example.com` / `password`
- 1 Regular user: `user@example.com` / `password`
- 5 sample events

### 4. Start the Server

```bash
php artisan serve
```

API will be available at `http://localhost:8000/api`

---

## API Endpoints

### Docs

Postman Docs will be available at `https://documenter.getpostman.com/view/26153121/2sBXiesZZx`

### Auth
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/register` | Register a new user |
| POST | `/api/login` | Login and receive token |
| POST | `/api/logout` | Logout (revoke token) |
| GET | `/api/me` | Get authenticated user |

### Events (Public)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/events` | List all upcoming events |
| GET | `/api/events/{id}` | Get event details |

### Orders (Authenticated)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/orders` | Create order & initiate payment |
| GET | `/api/orders/{id}` | Get order details |
| GET | `/api/orders` | List user's own orders |

### Payments (Authenticated)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/payments/create-intent` | Create Stripe PaymentIntent |
| POST | `/api/payments/confirm` | Confirm payment & finalize order |
| POST | `/api/webhook/stripe` | Stripe webhook handler |

### Admin (Admin only)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/orders` | List all orders with filters |
| GET | `/api/admin/orders/{id}` | Get order details |
| GET | `/api/admin/stats` | Dashboard statistics |
| GET | `/api/admin/events` | Manage events |
| POST | `/api/admin/events` | Create event |
| PUT | `/api/admin/events/{id}` | Update event |
| DELETE | `/api/admin/events/{id}` | Delete event |

---

## Database Schema

```
users           - id, name, email, password, role (user/admin), timestamps
events          - id, title, description, venue, date, total_tickets, available_tickets, price, image_url, timestamps
orders          - id, user_id, event_id, quantity, total_amount, status, stripe_payment_intent_id, stripe_payment_status, timestamps
```

---

## Stripe Webhook Setup (Local Dev)

```bash
# Install Stripe CLI
stripe listen --forward-to localhost:8000/api/webhook/stripe
```

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── API/
│   │   │   ├── EventController.php      # Public event listing
│   │   │   ├── OrderController.php      # Order management
│   │   │   └── PaymentController.php    # Stripe payment flow
│   │   ├── Admin/
│   │   │   ├── OrderController.php      # Admin order management
│   │   │   ├── EventController.php      # Admin event CRUD
│   │   │   └── DashboardController.php  # Stats & analytics
│   │   └── Auth/
│   │       └── AuthController.php       # Auth endpoints
│   ├── Middleware/
│   │   └── EnsureIsAdmin.php
│   └── Requests/                        # Form request validation
├── Models/
│   ├── User.php
│   ├── Event.php
│   └── Order.php
└── Services/
    └── StripeService.php                # Stripe abstraction layer
```
