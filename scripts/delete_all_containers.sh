#!/bin/bash

set -e

echo "Suppression de tous les conteneurs..."
docker stop $(docker ps -aq) 2>/dev/null || true
docker rm $(docker ps -aq) 2>/dev/null || true

echo "Voulez-vous supprimer toutes les images ? (y/n)"
read remove_images
if [ "$remove_images" = "y" ]; then
    docker rmi $(docker images -q) -f 2>/dev/null || true
fi

echo "Voulez-vous supprimer tous les volumes ? (y/n)"
read remove_volumes
if [ "$remove_volumes" = "y" ]; then
    docker volume rm $(docker volume ls -q) 2>/dev/null || true
fi

echo "Voulez-vous supprimer tous les réseaux ? (y/n)"
read remove_networks
if [ "$remove_networks" = "y" ]; then
    docker network rm $(docker network ls -q) 2>/dev/null || true
fi

echo "Tous les conteneurs, images, volumes et réseaux ont été traités."
