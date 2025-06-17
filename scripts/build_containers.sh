#!/bin/bash

set -e 
echo "Quels conteneurs voulez-vous installer ? (1. dev, 2. preprod, 3. prod, 4. tous)"
read -p "Entrez votre choix : " container_number

if [ "$container_number" == "1" ]; then
    docker-compose -f ../docker-compose.dev.yml down && docker-compose -f ../docker-compose.dev.yml up --build -d
elif [ "$container_number" == "2" ]; then
    docker-compose -p preprod_net -f ../docker-compose.preprod.yml down && docker-compose -p preprod -f ../docker-compose.preprod.yml up --build -d
elif [ "$container_number" == "3" ]; then
    docker-compose -p prod_net -f ../docker-compose.prod.yml down && docker-compose -p prod -f ../docker-compose.prod.yml up --build -d
elif [ "$container_number" == "4" ]; then
    docker-compose -f ../docker-compose.dev.yml down && docker-compose -f ../docker-compose.dev.yml up --build -d
    docker-compose -p preprod_net -f ../docker-compose.preprod.yml down && docker-compose -p preprod -f ../docker-compose.preprod.yml up --build -d
    docker-compose -p prod_net -f ../docker-compose.prod.yml down && docker-compose -p prod -f ../docker-compose.prod.yml up --build -d
else
    echo "Numéro de conteneur invalide"
    exit 1
fi

echo "Les conteneurs ont été installés avec succès :"
docker ps -a