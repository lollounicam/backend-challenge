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
