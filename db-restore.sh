#!/bin/bash

echo "Restoring database from dump..."

if [ ! -f "./www/sql/dump.sql" ]; then
    echo "Error: dump.sql file not found!"
    exit 1
fi

docker exec -i dev-db-container mariadb -u vguser -pvgpassword videgrenier < ./www/sql/dump.sql

echo "Database restoration completed" 