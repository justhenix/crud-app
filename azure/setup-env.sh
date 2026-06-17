#!/bin/bash
set -euo pipefail
cd /var/www/pc-lab-inventory

# Generate strong password
DB_PASS=$(openssl rand -base64 24 | tr -d '/+=')

# Copy template
cp .env.production.example .env.production

# Replace password placeholder
sed -i "s/CHANGE_ME_STRONG_PASSWORD_HERE/$DB_PASS/" .env.production

# Set SESSION_SECURE_COOKIE=false initially (no SSL yet)
sed -i "s/SESSION_SECURE_COOKIE=true/SESSION_SECURE_COOKIE=false/" .env.production

echo "DB_PASSWORD=$DB_PASS"
echo ".env.production created successfully"
cat .env.production
