#!/bin/bash

echo "🚀 Starting PREPROD environment for Vide Grenier..."

cd "$(dirname "$0")"

# Stop and remove old containers, networks, volumes if needed
docker compose -f docker-compose.preprod.yml down

# Build and start fresh containers in detached mode
docker compose -f docker-compose.preprod.yml up -d --build

echo "✅ PREPROD environment is running!"
echo "🌐 Access it at: http://localhost:8081"
