#!/usr/bin/env bash
###############################################################################
# VM Bootstrap Script — Run ONCE as root on fresh Ubuntu 24.04 VM
# Installs: Docker CE, Docker Compose plugin, swap, deployment dirs
# Usage:    sudo ./bootstrap.sh [DEPLOY_USER]
###############################################################################
set -euo pipefail

DEPLOY_USER="${1:-azureuser}"
DEPLOY_DIR="/var/www/pc-lab-inventory"
SWAP_SIZE="2G"

# ── Helpers ──────────────────────────────────────────────────────────────────
info()  { echo -e "\033[1;34m[INFO]\033[0m  $*"; }
ok()    { echo -e "\033[1;32m[OK]\033[0m    $*"; }
err()   { echo -e "\033[1;31m[ERR]\033[0m   $*" >&2; exit 1; }

[[ $EUID -eq 0 ]] || err "Must run as root. Use: sudo $0"
id "$DEPLOY_USER" &>/dev/null || err "User '$DEPLOY_USER' does not exist"

# ── 1. Update packages ─────────────────────────────────────────────────────
info "Updating system packages..."
export DEBIAN_FRONTEND=noninteractive
apt-get update -qq
apt-get upgrade -y -qq
ok "Packages updated"

# ── 2. Swap (2GB) ──────────────────────────────────────────────────────────
if [[ ! -f /swapfile ]]; then
  info "Creating ${SWAP_SIZE} swap file..."
  fallocate -l "$SWAP_SIZE" /swapfile
  chmod 600 /swapfile
  mkswap /swapfile
  swapon /swapfile
  echo '/swapfile none swap sw 0 0' >> /etc/fstab
  # Tune swappiness for low-RAM VM
  echo 'vm.swappiness=10' >> /etc/sysctl.conf
  sysctl vm.swappiness=10
  ok "Swap enabled"
else
  info "Swap already exists, skipping"
fi

# ── 3. Install Docker CE ───────────────────────────────────────────────────
if ! command -v docker &>/dev/null; then
  info "Installing Docker CE..."
  apt-get install -y -qq \
    ca-certificates \
    curl \
    gnupg \
    lsb-release

  install -m 0755 -d /etc/apt/keyrings
  curl -fsSL https://download.docker.com/linux/ubuntu/gpg | \
    gpg --dearmor -o /etc/apt/keyrings/docker.gpg
  chmod a+r /etc/apt/keyrings/docker.gpg

  echo \
    "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
    https://download.docker.com/linux/ubuntu \
    $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
    tee /etc/apt/sources.list.d/docker.list > /dev/null

  apt-get update -qq
  apt-get install -y -qq \
    docker-ce \
    docker-ce-cli \
    containerd.io \
    docker-buildx-plugin \
    docker-compose-plugin
  ok "Docker CE installed"
else
  info "Docker already installed"
fi

# ── 4. Enable Docker ───────────────────────────────────────────────────────
systemctl enable docker
systemctl start docker
ok "Docker running"

# ── 5. Add deploy user to docker group ──────────────────────────────────────
usermod -aG docker "$DEPLOY_USER"
ok "$DEPLOY_USER added to docker group"

# ── 6. Create deployment directory ──────────────────────────────────────────
info "Creating deployment directory: $DEPLOY_DIR"
mkdir -p "$DEPLOY_DIR"
chown -R "$DEPLOY_USER":"$DEPLOY_USER" "$DEPLOY_DIR"
chmod 755 "$DEPLOY_DIR"

# Create Laravel writable dirs
mkdir -p \
  "$DEPLOY_DIR/storage/framework/cache/data" \
  "$DEPLOY_DIR/storage/framework/sessions" \
  "$DEPLOY_DIR/storage/framework/views" \
  "$DEPLOY_DIR/storage/logs" \
  "$DEPLOY_DIR/bootstrap/cache"

chown -R "$DEPLOY_USER":"$DEPLOY_USER" "$DEPLOY_DIR"
ok "Deployment directory ready"

# ── 7. Install useful tools ────────────────────────────────────────────────
apt-get install -y -qq git rsync htop ncdu
ok "Utilities installed"

# ── 8. Basic firewall (UFW) ────────────────────────────────────────────────
info "Configuring UFW firewall..."
apt-get install -y -qq ufw
ufw default deny incoming
ufw default allow outgoing
ufw allow 22/tcp   # SSH
ufw allow 80/tcp   # HTTP
ufw allow 443/tcp  # HTTPS
echo "y" | ufw enable
ok "UFW active (22, 80, 443 only)"

# ── Done ────────────────────────────────────────────────────────────────────
echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║  BOOTSTRAP COMPLETE                                         ║"
echo "╠══════════════════════════════════════════════════════════════╣"
echo "║  Docker:    $(docker --version)                             "
echo "║  Compose:   $(docker compose version)                       "
echo "║  Swap:      $(swapon --show --noheadings | awk '{print $3}')"
echo "║  Deploy to: $DEPLOY_DIR                                     "
echo "║  User:      $DEPLOY_USER                                    "
echo "║                                                             "
echo "║  ⚠  Log out and back in for docker group to take effect     "
echo "╚══════════════════════════════════════════════════════════════╝"
