#!/bin/bash

set -e
echo "A quel conteneur voulez-vous restaurer la base de données ? (1. dev, 2. preprod, 3. prod)"
read -p "Entrez votre choix : " container_number

if [ "$container_number" == "1" ]; then
    docker exec -i dev-db-container mariadb -u vguser -pvgpassword videgrenier < ../www/sql/dump.sql
elif [ "$container_number" == "2" ]; then
    docker exec -i preprod-db-container mariadb -u vguser -pvgpassword videgrenier < ../www/sql/dump.sql
elif [ "$container_number" == "3" ]; then
    docker exec -i prod-db-container mariadb -u vguser -pvgpassword videgrenier < ../www/sql/dump.sql
else
    echo "Numéro de conteneur invalide"
    exit 1
fi