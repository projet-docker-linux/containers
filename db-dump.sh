#!/bin/bash

echo "Creating a dump of the videgrenier database..."

docker exec dev-db-container mariadb-dump -u vguser -pvgpassword videgrenier > ./www/sql/dump.sql

echo "Dump saved to ./www/sql/dump.sql"
