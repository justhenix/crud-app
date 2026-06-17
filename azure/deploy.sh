#!/usr/bin/env bash
###############################################################################
# Laravel Deployment Script — PC Lab Inventory System
# Runs on VM via SSH (manually or from GitLab Runner)
# Usage:  ./deploy.sh
###############################################################################
set -euo pipefail

DEPLOY_DIR="/var/www/pc-lab-inventory"
COMPOSE_FILE="docker-compose.prod.yml"
MAX_WAIT=60  # seconds to wait for DB health

# ── Helpers ──────────────────────────────────────────────────────────────────
info()  { echo -e "\033[1;34m[DEPLOY]\033[0m $*"; }
ok()    { echo -e "\033[1;32m[OK]\033[0m     $*"; }
err()   { echo -e "\033[1;31m[FAIL]\033[0m   $*" >&2; exit 1; }

# ── 1. Verify deploy dir ───────────────────────────────────────────────────
[[ -d "$DEPLOY_DIR" ]] || err "Deploy directory not found: $DEPLOY_DIR"
cd "$DEPLOY_DIR"
[[ -f "$COMPOSE_FILE" ]] || err "Compose file not found: $COMPOSE_FILE"
[[ -f ".env.production" ]] || err ".env.production not found. Copy from .env.production.example and fill secrets."

info "Deploying from: $DEPLOY_DIR"

# ── 2. Build and start containers ──────────────────────────────────────────
info "Building and starting containers..."
docker compose -f "$COMPOSE_FILE" up -d --build --remove-orphans

# ── 3. Wait for PostgreSQL health ──────────────────────────────────────────
info "Waiting for PostgreSQL to be healthy..."
elapsed=0
until docker compose -f "$COMPOSE_FILE" exec -T db pg_isready -U "${DB_USERNAME:-pclab_user}" -d "${DB_DATABASE:-pclab}" &>/dev/null; do
  sleep 2
  elapsed=$((elapsed + 2))
  if [[ $elapsed -ge $MAX_WAIT ]]; then
    err "PostgreSQL did not become healthy within ${MAX_WAIT}s"
  fi
  echo -n "."
done
echo ""
ok "PostgreSQL is healthy"

# ── 4. Fix permissions ─────────────────────────────────────────────────────
info "Fixing storage permissions..."
docker compose -f "$COMPOSE_FILE" exec -T app sh -c "
  chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
  chmod -R 775 /var/www/storage /var/www/bootstrap/cache
"
ok "Permissions set"

# ── 5. Run migrations ──────────────────────────────────────────────────────
info "Running database migrations..."
docker compose -f "$COMPOSE_FILE" exec -T app php artisan migrate --force
ok "Migrations complete"

# ── 6. Production optimizations ─────────────────────────────────────────────
info "Caching config, routes, views..."
docker compose -f "$COMPOSE_FILE" exec -T app php artisan config:cache
docker compose -f "$COMPOSE_FILE" exec -T app php artisan route:cache
docker compose -f "$COMPOSE_FILE" exec -T app php artisan view:cache
docker compose -f "$COMPOSE_FILE" exec -T app php artisan event:cache
ok "Optimizations cached"

# ── 7. Storage link ────────────────────────────────────────────────────────
info "Creating storage symlink..."
docker compose -f "$COMPOSE_FILE" exec -T app php artisan storage:link --force 2>/dev/null || true
ok "Storage linked"

# ── 8. Verify ───────────────────────────────────────────────────────────────
info "Verifying containers..."
docker compose -f "$COMPOSE_FILE" ps

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║  DEPLOYMENT COMPLETE                                        ║"
echo "║  Site: https://crud.henix.my.id                             ║"
echo "╚══════════════════════════════════════════════════════════════╝"
