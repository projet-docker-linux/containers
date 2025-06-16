#!/bin/bash

echo "📦 Creating a dump of the videgrenier database..."

docker exec db-container \
  mysqldump -u vguser -pvgpassword videgrenier > ./www/sql/dump.sql

echo "✅ Dump saved to ./www/sql/dump.sql"
