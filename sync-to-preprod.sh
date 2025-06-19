#!/bin/bash

set -e

SOURCE_DIR="./dev_php"
DEST_DIR="./preprod_php"

echo "Syncing code from $SOURCE_DIR to $DEST_DIR ..."

# Ensure destination exists
if [ ! -d "$DEST_DIR" ]; then
  echo "Creating $DEST_DIR ..."
  mkdir -p "$DEST_DIR"
fi

# Use rsync to sync (excluding .git and node_modules if needed)
rsync -av --delete \
  --exclude=".git" \
  --exclude="node_modules" \
  "$SOURCE_DIR/" "$DEST_DIR/"

echo "✅ Sync to preprod_php complete."
