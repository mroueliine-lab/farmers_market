# Farmers Market Platform — Laravel API

RESTful API backend for an agricultural marketplace in Côte d'Ivoire. Farmers can purchase pesticides, fertilizers, and seeds on cash or credit. Built as a technical test for a Full Stack Developer position.

## Tech Stack

- **Laravel 13** / PHP 8.3
- **SQLite** database
- **Laravel Sanctum** — token-based authentication
- **Laravel Herd** — local development server

## Features

- Role-based access control: `admin`, `supervisor`, `operator`
- Product catalog with nested categories (2+ levels)
- Farmer management with credit limit enforcement
- Transactions: cash and credit with configurable interest rate
- Debt tracking with FIFO repayment (commodity-based, kg → FCFA)

## Setup

### Requirements
- PHP 8.3+
- Composer
- Laravel Herd (or any local PHP server)

### Installation

```bash
git clone <repo-url>
cd farmers_market

composer install

cp .env.example .env
php artisan key:generate
```

### Database

The project uses SQLite. The database file is at `database/database.sqlite`.

```bash
touch database/database.sqlite   # create the file if it doesn't exist
php artisan migrate:fresh --seed
```

The seeder creates:
- 4 users: 1 admin, 1 supervisor, 2 operators
- 3 parent categories with 6 subcategories
- 9 products with realistic names and FCFA prices
- 3 farmers with different credit limits
- Sample transactions (cash & credit) and a partial repayment

### Default credentials

| Role       | Email                      | Password  |
|------------|----------------------------|-----------|
| Admin      | admin@marche.ci            | password  |
| Supervisor | superviseur@marche.ci      | password  |
| Operator 1 | operateur1@marche.ci       | password  |
| Operator 2 | operateur2@marche.ci       | password  |

## API

Base URL: `http://farmers_market.test/api`

All protected endpoints require:
```
Authorization: Bearer <token>
Accept: application/json
```

### Authentication
| Method | Endpoint        | Description        |
|--------|-----------------|--------------------|
| POST   | /register       | Register a user    |
| POST   | /login          | Login              |
| POST   | /logout         | Logout             |

### Users (admin only)
| Method | Endpoint        | Description              |
|--------|-----------------|--------------------------|
| GET    | /users          | List supervisors         |
| POST   | /users          | Create supervisor        |
| PUT    | /users/{id}     | Update user              |
| DELETE | /users/{id}     | Delete user              |

### Operators (supervisor only)
| Method | Endpoint        | Description              |
|--------|-----------------|--------------------------|
| GET    | /operators      | List operators           |
| POST   | /operators      | Create operator          |
| PUT    | /operators/{id} | Update operator          |
| DELETE | /operators/{id} | Delete operator          |

### Categories & Products (admin, supervisor)
| Method | Endpoint        | Description              |
|--------|-----------------|--------------------------|
| GET    | /categories     | List all (nested)        |
| POST   | /categories     | Create category          |
| PUT    | /categories/{id}| Update category          |
| DELETE | /categories/{id}| Delete category          |
| GET    | /products       | List all with category   |
| POST   | /products       | Create product           |
| PUT    | /products/{id}  | Update product           |
| DELETE | /products/{id}  | Delete product           |

### Farmers (all roles)
| Method | Endpoint                  | Description                    |
|--------|---------------------------|--------------------------------|
| POST   | /farmers                  | Create farmer                  |
| GET    | /farmers/search?q=        | Search by identifier or phone  |
| GET    | /farmers/{id}             | View profile with debts        |
| GET    | /farmers/{id}/debts       | List farmer's debts            |
| GET    | /farmers/{id}/repayments  | List farmer's repayments       |

### Transactions (operator creates, all roles read)
| Method | Endpoint            | Description              |
|--------|---------------------|--------------------------|
| POST   | /transactions       | Create transaction       |
| GET    | /transactions       | List all transactions    |
| GET    | /transactions/{id}  | Get transaction details  |

### Repayments (operator only)
| Method | Endpoint      | Description                        |
|--------|---------------|------------------------------------|
| POST   | /repayments   | Record commodity repayment (FIFO)  |

## Postman Collection

Import `Farmers.postman_collection.json` from the project root.

Create a Postman environment with these variables:
- `admin_token`
- `supervisor_token`
- `operator_token`
- `token` (general)

After logging in, the login request auto-saves `admin_token` and `token` via the Tests script.

