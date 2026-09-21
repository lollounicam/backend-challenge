# Debitoo Backend Challenge

This project is a REST API developed with Laravel and MySQL.

It allows operators to register clients and manage their debt cases. Each debt case belongs to an existing client and follows a simple workflow through the `new`, `in_progress`, and `closed` statuses.

## Technologies

The project was developed and tested with:

- PHP 8.5
- Laravel 13
- MySQL 9
- Composer 2
- PHPUnit

## Requirements

Before installing the project, make sure the following tools are available:

- PHP 8.3 or later
- Composer
- MySQL
- Git

You can check the installed versions with:

```bash
php -v
composer --version
mysql --version
git --version
```

## Installation

Clone the repository:

```bash
git clone https://github.com/lollounicam/backend-challenge.git
cd backend-challenge
```

Install the PHP dependencies:

```bash
composer install
```

Create the local environment file:

```bash
cp .env.example .env
```

On Windows PowerShell, use:

```powershell
Copy-Item .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

## Database configuration

Connect to MySQL with an administrator account and create the database:

```sql
CREATE DATABASE debitoo_backend_challenge
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

You can use an existing MySQL user or create a dedicated one:

```sql
CREATE USER 'debitoo_app'@'localhost'
    IDENTIFIED BY 'your_password';

GRANT ALL PRIVILEGES
    ON debitoo_backend_challenge.*
    TO 'debitoo_app'@'localhost';

FLUSH PRIVILEGES;
```

Update the database section of the local `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=debitoo_backend_challenge
DB_USERNAME=debitoo_app
DB_PASSWORD=your_password
```

Depending on the local MySQL configuration, `DB_HOST=localhost` may be required instead of `127.0.0.1`.

The `.env` file contains local configuration and must not be committed. The repository only contains `.env.example`, without passwords or real credentials.

## Migrations

Create the database tables with:

```bash
php artisan migrate
```

To check the migration status:

```bash
php artisan migrate:status
```
## Sample data

The project includes a seeder with example clients and debt cases.

Load the sample data with:

```bash
php artisan db:seed
```

Migrations and sample data can also be loaded together:

```bash
php artisan migrate --seed
```

## Running the application

Start the local development server:

```bash
php artisan serve
```

The API will be available at:

```text
http://127.0.0.1:8000/api
```

## API endpoints

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/clients` | Create a client |
| `POST` | `/api/cases` | Create a debt case |
| `GET` | `/api/cases` | List all debt cases |
| `GET` | `/api/cases?status=new` | Filter debt cases by status |
| `GET` | `/api/cases/{id}` | View a debt case |
| `PATCH` | `/api/cases/{id}/status` | Update the status of a debt case |

All requests and responses use JSON.

Requests should include the following headers:

```text
Accept: application/json
Content-Type: application/json
