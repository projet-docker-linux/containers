#!/bin/bash

set -e

echo "Quels conteneurs voulez-vous supprimer ? (1. dev, 2. preprod, 3. prod, 4. tous)"
read -p "Entrez votre choix : " container_number

if [ "$container_number" == "1" ]; then
    docker-compose -f ../docker-compose.dev.yml down --remove-orphans
elif [ "$container_number" == "2" ]; then
    docker-compose -p preprod -f ../docker-compose.preprod.yml down --remove-orphans
    docker network rm preprod_net || true
elif [ "$container_number" == "3" ]; then
    docker-compose -p prod -f ../docker-compose.prod.yml down --remove-orphans
    docker network rm prod_net || true
elif [ "$container_number" == "4" ]; then
    docker-compose -f ../docker-compose.dev.yml down --remove-orphans
    docker-compose -p preprod -f ../docker-compose.preprod.yml down --remove-orphans
    docker-compose -p prod -f ../docker-compose.prod.yml down --remove-orphans
    docker network rm preprod_net || true
    docker network rm prod_net || true
else
    echo "Numéro de conteneur invalide"
    exit 1
fi

echo "Souhaitez-vous supprimer les images ? (y/n)"
read -p "Entrez votre choix : " remove_images
if [ "$remove_images" == "y" ]; then
    docker rmi $(docker images -q) -f
fi

echo "Souhaitez-vous supprimer les volumes ? (y/n)"
read -p "Entrez votre choix : " remove_volumes
if [ "$remove_volumes" == "y" ]; then
    docker volume rm $(docker volume ls -q)
fi

echo "Les conteneurs ont été supprimés avec succès :"
docker ps -a