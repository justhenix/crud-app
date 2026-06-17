#!/usr/bin/env bash
###############################################################################
# SSL Setup Script — Run ONCE after DNS is pointed and HTTP is working
# Obtains Let's Encrypt cert via Certbot for crud.henix.my.id
# Then enables HTTPS in nginx config
###############################################################################
set -euo pipefail

DEPLOY_DIR="/var/www/pc-lab-inventory"
COMPOSE_FILE="docker-compose.prod.yml"
DOMAIN="crud.henix.my.id"
EMAIL="${1:-admin@henix.my.id}"

info()  { echo -e "\033[1;34m[SSL]\033[0m  $*"; }
ok()    { echo -e "\033[1;32m[OK]\033[0m   $*"; }
err()   { echo -e "\033[1;31m[ERR]\033[0m  $*" >&2; exit 1; }

cd "$DEPLOY_DIR"

# ── 1. Obtain certificate ──────────────────────────────────────────────────
info "Requesting SSL certificate for $DOMAIN..."
docker compose --env-file .env.production -f "$COMPOSE_FILE" run --rm certbot \
  certonly \
  --webroot \
  --webroot-path=/var/www/certbot \
  --email "$EMAIL" \
  --agree-tos \
  --no-eff-email \
  -d "$DOMAIN"

ok "Certificate obtained"

# ── 2. Enable HTTPS in nginx ───────────────────────────────────────────────
info "Enabling HTTPS server block in nginx config..."
NGINX_CONF="$DEPLOY_DIR/docker/nginx/production.conf"

# Uncomment the HTTPS redirect in HTTP block
sed -i 's|^    # location / {|    location / {|' "$NGINX_CONF"
sed -i 's|^    #     return 301 https://\$host\$request_uri;|        return 301 https://$host$request_uri;|' "$NGINX_CONF"
sed -i 's|^    # }|    }|' "$NGINX_CONF"

# Uncomment the HTTPS server block (remove leading "# " from each line)
sed -i '/^# ── HTTPS server block/,$ s/^# //' "$NGINX_CONF"

ok "HTTPS config enabled"

# ── 3. Reload nginx ────────────────────────────────────────────────────────
info "Reloading nginx..."
docker compose --env-file .env.production -f "$COMPOSE_FILE" exec web nginx -s reload
ok "Nginx reloaded with HTTPS"
 
# ── 4. Setup auto-renewal cron ──────────────────────────────────────────────
info "Setting up cert renewal cron..."
CRON_CMD="0 3 * * * cd $DEPLOY_DIR && docker compose --env-file .env.production -f $COMPOSE_FILE run --rm certbot renew --quiet && docker compose --env-file .env.production -f $COMPOSE_FILE exec web nginx -s reload"
(crontab -l 2>/dev/null; echo "$CRON_CMD") | sort -u | crontab -
ok "Auto-renewal cron installed (daily 3 AM)"

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║  SSL SETUP COMPLETE                                         ║"
echo "║  Site: https://crud.henix.my.id                             ║"
echo "╚══════════════════════════════════════════════════════════════╝"
