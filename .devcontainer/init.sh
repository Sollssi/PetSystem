#!/bin/bash

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"

if [ -f "$ROOT_DIR/migrate.sh" ]; then
    ln -sf "$ROOT_DIR/migrate.sh" /usr/local/bin/migrate
    chmod +x "$ROOT_DIR/migrate.sh"
fi
