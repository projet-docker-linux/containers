#!/bin/bash

set -e
echo "A quel conteneur voulez-vous vous connecter ? (1. dev, 2. preprod, 3. prod)"
read -p "Entrez votre choix : " container_number

if [ "$container_number" == "1" ]; then
    docker exec -it dev-db-container mariadb -u vguser -pvgpassword videgrenier
elif [ "$container_number" == "2" ]; then
    docker exec -it preprod-db-container mariadb -u vguser -pvgpassword videgrenier
elif [ "$container_number" == "3" ]; then
    docker exec -it prod-db-container mariadb -u vguser -pvgpassword videgrenier
else
    echo "Numéro de conteneur invalide"
    exit 1
fi