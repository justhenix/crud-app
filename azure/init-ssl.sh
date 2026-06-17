#!/usr/bin/env bash
set -euo pipefail

cd /var/www/pc-lab-inventory

echo "1. Generating dummy SSL certs..."
docker compose run --rm --entrypoint sh certbot -c '
  mkdir -p /etc/letsencrypt/live/crud.henix.my.id
  openssl req -x509 -nodes -newkey rsa:2048 -days 1 \
    -keyout /etc/letsencrypt/live/crud.henix.my.id/privkey.pem \
    -out /etc/letsencrypt/live/crud.henix.my.id/fullchain.pem \
    -subj "/CN=localhost"
'

echo "2. Booting Nginx, app, db..."
docker compose up -d web app db

echo "3. Replacing dummy cert with real Let's Encrypt certificate..."
docker compose run --rm --entrypoint sh certbot -c '
  rm -rf /etc/letsencrypt/live/crud.henix.my.id
  certbot certonly --webroot -w /var/www/certbot \
    -d crud.henix.my.id \
    --email gammafadhillah@gmail.com \
    --agree-tos --no-eff-email
'

echo "4. Reloading Nginx..."
docker compose exec web nginx -s reload

echo "Initialization Complete!"
