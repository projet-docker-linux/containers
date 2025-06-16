#!/bin/bash

echo "🧨 Resetting the database volume: projet_docker_db_data"
docker compose -f docker-compose.dev.yml down

# Remove the DB volume
docker volume rm projet_docker_db_data

# Rebuild and restart
docker compose -f docker-compose.dev.yml up -d --build

echo "✅ Database reset complete!"
echo "🌐 Visit your app at: http://localhost:8080"
