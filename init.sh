#!/bin/bash

# Create the SQLite database file
echo "Creating SQLite database file..."
touch backend/database/database.sqlite

# Create the .env file if it doesn't exist
if [ ! -f backend/.env ]; then
    echo "Creating .env file from .env.example..."
    cp backend/.env.example backend/.env
fi

# Start the docker containers
echo "Building and starting Docker containers..."
docker-compose up -d --build

# Wait for the backend container to be ready
echo "Waiting for the backend container to be ready..."
sleep 10

# Run database migrations
echo "Running database migrations..."
docker-compose exec backend php artisan migrate

echo "Setup complete! The application should be running on http://localhost:8000"
