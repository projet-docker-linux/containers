#!/bin/bash


echo "Quelle base de données voulez-vous récupérer ? (1. dev, 2. preprod, 3. prod)"
read -p "Entrez votre choix : " container_number

if [ "$container_number" == "1" ]; then
    docker exec dev-db-container mariadb-dump -u vguser -pvgpassword videgrenier > ../www/sql/dump.sql
elif [ "$container_number" == "2" ]; then
    docker exec preprod-db-container mariadb-dump -u vguser -pvgpassword videgrenier > ../www/sql/dump.sql
elif [ "$container_number" == "3" ]; then
    docker exec prod-db-container mariadb-dump -u vguser -pvgpassword videgrenier > ../www/sql/dump.sql
else
    echo "Numéro de conteneur invalide"
    exit 1
fi

echo "Dump créé avec succès dans ../www/sql/dump.sql"