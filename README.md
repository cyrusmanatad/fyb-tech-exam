# FYB Fullstack Application

This is a fullstack application with a Laravel backend and a Vue.js frontend. The database used is SQLite.

## Getting Started

Follow these steps to get the application up and running.

### 1. Run the Backend (Laravel)

Navigate to the `backend` directory and install the PHP dependencies:

```bash
cd backend
composer install
```

Copy the example environment file and generate an application key:

```bash
cp .env.example .env
php artisan key:generate
```

Generate the JWT secret key:

```bash
php artisan jwt:secret
```

Since we are using SQLite, make sure your `.env` file has the following database configuration:

```
DB_CONNECTION=sqlite
```

Create an empty `database.sqlite` file in the `database` directory:

```bash
touch database/database.sqlite
```

Run the database migrations and seed the database (optional):

```bash
php artisan migrate --seed
```

Finally, start the Laravel development server:

```bash
php artisan serve
```

The backend will typically run on `http://127.0.0.1:8000`.

### 2. Run the Frontend (Vue.js)

Open a new terminal, navigate to the `frontend` directory, and install the JavaScript dependencies:

```bash
cd frontend
npm install
```

Start the Vue.js development server:

```bash
npm run dev
```

The frontend will run on `http://localhost:3001`. Open this URL in your browser to access the application.
