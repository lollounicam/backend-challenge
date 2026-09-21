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
```

## API examples

### Create a client

```bash
curl -X POST http://127.0.0.1:8000/api/clients \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -d '{
        "first_name": "Mario",
        "last_name": "Rossi",
        "email": "mario.rossi@example.com"
    }'
```

A successful request returns status `201 Created`.

Example response:

```json
{
    "id": 1,
    "first_name": "Mario",
    "last_name": "Rossi",
    "email": "mario.rossi@example.com"
}
```

### Create a debt case

The `client_id` must refer to an existing client.

```bash
curl -X POST http://127.0.0.1:8000/api/cases \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -d '{
        "client_id": 1,
        "description": "Personal loan debt",
        "debt_amount": "1250.50"
    }'
```

The case ID, opening date and initial status are generated automatically.

A new case always starts with the `new` status.

### List debt cases

```bash
curl http://127.0.0.1:8000/api/cases \
    -H "Accept: application/json"
```

### Filter debt cases by status

```bash
curl "http://127.0.0.1:8000/api/cases?status=new" \
    -H "Accept: application/json"
```

The accepted status values are:

- `new`
- `in_progress`
- `closed`

### View a debt case

```bash
curl http://127.0.0.1:8000/api/cases/1 \
    -H "Accept: application/json"
```

A request for a case that does not exist returns status `404 Not Found`.

### Start working on a case

```bash
curl -X PATCH http://127.0.0.1:8000/api/cases/1/status \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -d '{
        "status": "in_progress"
    }'
```

### Close a case

```bash
curl -X PATCH http://127.0.0.1:8000/api/cases/1/status \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -d '{
        "status": "closed"
    }'
```

## Case workflow

A debt case follows this workflow:

```text
new -> in_progress -> closed
```

The following rules are applied:

- A case cannot skip a status.
- A case cannot return to a previous status.
- A closed case cannot be reopened.
- Sending the current status again is accepted without changing the case.

For example, changing a case directly from `new` to `closed` is rejected.

## Validation and errors

The API returns understandable JSON validation errors with status `422 Unprocessable Content`.

The main validation rules are:

- First name and last name are required.
- Email is required, must be valid and must be unique.
- The client associated with a debt case must exist.
- Description is required and cannot exceed 500 characters.
- Debt amount must be positive and have no more than two decimal places.
- Status must be one of the supported values.
- Automatically generated fields cannot be supplied when creating a case.

Example validation error:

```json
{
    "message": "The status is required.",
    "errors": {
        "status": [
            "The status is required."
        ]
    }
}
```

## Data model

The application uses two main entities:

### Client

A client contains:

- An automatically generated ID
- First name
- Last name
- A unique email address

### Debt case

A debt case contains:

- An automatically generated ID
- The ID of the associated client
- A description
- The debt amount
- The current status
- An automatically generated opening date

A client can have multiple debt cases, while each debt case belongs to one client.

The relationship is enforced by a database foreign key.

## Automated tests

Run the complete test suite with:

```bash
php artisan test
```

The tests use an in-memory SQLite database so they do not modify the local MySQL database.

The test suite covers, among other cases:

- Client creation
- Duplicate email rejection
- Debt case creation
- Rejection of non-positive amounts
- Rejection of amounts with more than two decimal places
- Listing and filtering debt cases
- Viewing an existing case
- Handling a missing case
- Invalid status filters
- Valid status transitions
- Rejection of skipped or backward transitions
- Rejection of reopening a closed case

## Code formatting

Laravel Pint is used to format the PHP code:

```bash
./vendor/bin/pint
```

On Windows PowerShell:

```powershell
.\vendor\bin\pint
```

## Tools and AI usage

The official PHP, Laravel and MySQL documentation was consulted during development.

Postman and `curl` were used to test the API manually. MySQL Workbench was used to inspect and configure the database.

ChatGPT/Codex was used to clarify PHP and Laravel concepts, help investigate errors, review the automated tests and support the preparation of the documentation.

AI tools were also used to help write this README.

## Time spent

Approximately 10 hours were spent completing the project.
