# Service Maintenance — Field Service / Repair Shop Management

A Laravel app for managing customer service requests: intake, technician assignment,
status tracking, notes/history, and invoicing.

## Features

- Role-based access: Admin, Technician, Staff
- Customers CRUD
- Service categories
- Service requests (tickets) with status, priority, scheduling, assignment
- Notes / activity log per ticket
- File attachments per ticket
- Invoices with line items, auto-calculated totals
- Dashboard with counts and today's schedule

## Requirements

- PHP >= 8.1
- Composer
- MySQL (or change DB_CONNECTION in .env to sqlite/pgsql)

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate

# create the database (or use sqlite — see note below), then:
php artisan migrate --seed

php artisan storage:link
php artisan serve
```

Visit http://localhost:8000 and log in with the seeded admin account:

- Email: admin@gmail.com
- Password: 123456

### Using SQLite instead of MySQL

```bash
touch database/database.sqlite
```

Then in `.env` set:

```
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

(remove the other DB\_\* lines or leave them, they're ignored for sqlite)

## Roles

- **admin** — full access: manage users' roles via seeder/tinker, categories, all tickets, invoices
- **technician** — sees tickets assigned to them, can update status/add notes
- **staff** — can create tickets, manage customers, view all tickets

## Seeded data

Running `php artisan migrate --seed` creates an admin user, a technician, a few
service categories, sample customers and sample tickets so you can explore the UI
immediately.
