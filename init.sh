#!/bin/bash

echo "=========================================="
echo "Starting FYB Fullstack Application Setup"
echo "=========================================="

# Create the SQLite database file
echo ""
echo "[1/6] Creating SQLite database file..."
touch backend/database/database.sqlite

# Create the .env file if it doesn't exist
if [ ! -f backend/.env ]; then
    echo "[2/6] Creating .env file from .env.example..."
    cp backend/.env.example backend/.env
else
    echo "[2/6] .env file already exists, skipping..."
fi

# Stop any existing containers
echo "[3/6] Stopping any existing containers..."
docker-compose down

# Build and start the docker containers
echo "[4/6] Building and starting Docker containers..."
docker-compose up -d --build

# Wait for the backend container to be ready
echo "[5/6] Waiting for containers to be ready..."
sleep 10

# Check if containers are running
echo ""
echo "Checking container status..."
docker-compose ps

# Run database migrations
echo ""
echo "[6/6] Running database migrations..."
docker-compose exec backend php artisan migrate --force

echo ""
echo "=========================================="
echo "Setup Complete!"
echo "=========================================="
echo ""
echo "Services:"
echo "  - Backend (Laravel):  http://localhost:8000"
echo "  - Frontend (Vue):     http://localhost:3001"
echo ""
echo "To view logs:"
echo "  docker-compose logs -f"
echo ""
echo "To stop services:"
echo "  docker-compose down"
echo ""

# Check if frontend is running
if docker-compose ps | grep -q "vue-frontend.*Up"; then
    echo "✓ Frontend is running"
else
    echo "✗ Frontend failed to start. Checking logs..."
    echo ""
    docker-compose logs frontend
fi

# Check if backend is running
if docker-compose ps | grep -q "laravel-backend.*Up"; then
    echo "✓ Backend is running"
else
    echo "✗ Backend failed to start. Checking logs..."
    echo ""
    docker-compose logs backend
fi