#!/bin/bash
set -e

# Configuration
REPO_URL="https://github.com/projet-docker-linux/containers.git"
BRANCH="pre_prod"
CONTAINER_NAME="preprod-php-container"
TARGET_DIR="/var/www/html"

echo "Initialisation du conteneur PHP..."

# Vérifier si le conteneur existe
if ! docker ps | grep -q $CONTAINER_NAME; then
    echo "Le conteneur $CONTAINER_NAME n'est pas en cours d'exécution"
    exit 1
fi

# Supprimer le contenu du répertoire dans le conteneur
echo "Suppression du contenu existant..."
docker exec $CONTAINER_NAME bash -c "rm -rf $TARGET_DIR/* $TARGET_DIR/.* 2>/dev/null || true"

# Cloner le repository dans le conteneur
echo "Clonage du dépôt git sur la branche $BRANCH..."
docker exec $CONTAINER_NAME git clone -b "$BRANCH" "$REPO_URL" "$TARGET_DIR"

# Définir les permissions dans le conteneur
echo "Configuration des permissions..."
docker exec $CONTAINER_NAME chown -R www-data:www-data $TARGET_DIR
docker exec $CONTAINER_NAME chmod -R 755 $TARGET_DIR

echo "Mise à jour terminée avec succès !" 