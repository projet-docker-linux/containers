#!/bin/bash

echo "🚀 Starting DEV environment for Vide Grenier..."

cd "$(dirname "$0")"

# Stop and remove old containers, networks, volumes if needed
docker compose -f docker-compose.dev.yml down

# Build and start fresh containers in detached mode
docker compose -f docker-compose.dev.yml up -d --build

echo "✅ DEV environment is running!"
echo "🌐 Access it at: http://localhost:8080"
