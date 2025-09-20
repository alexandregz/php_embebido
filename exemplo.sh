#!/bin/bash
# Servidor PHP embebido

# Directorio raíz do servidor
DOCROOT="."

# IP e porto
HOST="0.0.0.0"
PORT="31337"

exec /usr/bin/php -S ${HOST}:${PORT} -t "$DOCROOT"
