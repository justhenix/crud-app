#!/usr/bin/env bash
###############################################################################
# Azure CLI Provisioning Script — PC Lab Inventory System
# Creates: Resource Group → NSG → Public IP → VM (Standard_B2ats_v2, Ubuntu 24.04)
# Target:  crud.henix.my.id
# Cost:    ~$6.57/month (B2ats_v2) — cheapest available burstable in SEA
# NOTE:    B1s/B1ls capacity-restricted in southeastasia for Student subs
###############################################################################
set -euo pipefail

# ── Configuration ────────────────────────────────────────────────────────────
RESOURCE_GROUP="rg-pclab-prod"
LOCATION="southeastasia"
VM_NAME="vm-pclab"
VM_SIZE="Standard_B2ats_v2"       # 2 vCPU, 1 GB RAM — cheapest available in SEA
VM_IMAGE="Canonical:ubuntu-24_04-lts:server:latest"
ADMIN_USER="azureuser"
NSG_NAME="nsg-pclab"
PUBLIC_IP_NAME="pip-pclab"
VNET_NAME="vnet-pclab"
SUBNET_NAME="subnet-pclab"
NIC_NAME="nic-pclab"
OS_DISK_SIZE=30

# ── Helpers ──────────────────────────────────────────────────────────────────
info()  { echo -e "\033[1;34m[INFO]\033[0m  $*"; }
ok()    { echo -e "\033[1;32m[OK]\033[0m    $*"; }
err()   { echo -e "\033[1;31m[ERR]\033[0m   $*" >&2; }

# ── Preflight ────────────────────────────────────────────────────────────────
info "Checking Azure CLI login..."
az account show --query "name" -o tsv || { err "Not logged in. Run: az login"; exit 1; }

# ── 1. Resource Group ────────────────────────────────────────────────────────
info "Creating resource group: $RESOURCE_GROUP"
az group create \
  --name "$RESOURCE_GROUP" \
  --location "$LOCATION" \
  --tags project=pc-lab-inventory environment=production \
  -o none
ok "Resource group ready"

# ── 2. Network Security Group ───────────────────────────────────────────────
info "Creating NSG: $NSG_NAME"
az network nsg create \
  --resource-group "$RESOURCE_GROUP" \
  --name "$NSG_NAME" \
  --location "$LOCATION" \
  -o none

for rule in "AllowSSH:22:100" "AllowHTTP:80:110" "AllowHTTPS:443:120"; do
  IFS=':' read -r name port priority <<< "$rule"
  info "  NSG rule: $name (port $port)"
  az network nsg rule create \
    --resource-group "$RESOURCE_GROUP" \
    --nsg-name "$NSG_NAME" \
    --name "$name" \
    --priority "$priority" \
    --direction Inbound \
    --access Allow \
    --protocol Tcp \
    --destination-port-ranges "$port" \
    -o none
done
ok "NSG configured (SSH + HTTP + HTTPS only)"

# ── 3. Virtual Network + Subnet ─────────────────────────────────────────────
info "Creating VNet: $VNET_NAME"
az network vnet create \
  --resource-group "$RESOURCE_GROUP" \
  --name "$VNET_NAME" \
  --address-prefix "10.0.0.0/16" \
  --subnet-name "$SUBNET_NAME" \
  --subnet-prefix "10.0.1.0/24" \
  --location "$LOCATION" \
  -o none
ok "VNet ready"

# ── 4. Public IP (Standard SKU — Basic not allowed on Student subs) ─────────
info "Creating static public IP: $PUBLIC_IP_NAME"
az network public-ip create \
  --resource-group "$RESOURCE_GROUP" \
  --name "$PUBLIC_IP_NAME" \
  --sku Standard \
  --allocation-method Static \
  --location "$LOCATION" \
  -o none
ok "Public IP created"

# ── 5. NIC ──────────────────────────────────────────────────────────────────
info "Creating NIC: $NIC_NAME"
az network nic create \
  --resource-group "$RESOURCE_GROUP" \
  --name "$NIC_NAME" \
  --vnet-name "$VNET_NAME" \
  --subnet "$SUBNET_NAME" \
  --network-security-group "$NSG_NAME" \
  --public-ip-address "$PUBLIC_IP_NAME" \
  --location "$LOCATION" \
  -o none
ok "NIC ready"

# ── 6. Virtual Machine ─────────────────────────────────────────────────────
info "Creating VM: $VM_NAME ($VM_SIZE)"
az vm create \
  --resource-group "$RESOURCE_GROUP" \
  --name "$VM_NAME" \
  --nics "$NIC_NAME" \
  --image "$VM_IMAGE" \
  --size "$VM_SIZE" \
  --admin-username "$ADMIN_USER" \
  --generate-ssh-keys \
  --os-disk-size-gb "$OS_DISK_SIZE" \
  --storage-sku Standard_LRS \
  -o none

ok "VM created"

# ── 7. Auto-shutdown (cost savings) ─────────────────────────────────────────
info "Setting auto-shutdown at 01:00 UTC (08:00 WIB)"
az vm auto-shutdown \
  --resource-group "$RESOURCE_GROUP" \
  --name "$VM_NAME" \
  --time "0100" \
  -o none 2>/dev/null || info "Auto-shutdown skipped (optional)"

# ── 8. Output ───────────────────────────────────────────────────────────────
PUBLIC_IP=$(az network public-ip show \
  --resource-group "$RESOURCE_GROUP" \
  --name "$PUBLIC_IP_NAME" \
  --query "ipAddress" -o tsv)

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║  PROVISIONING COMPLETE                                      ║"
echo "╠══════════════════════════════════════════════════════════════╣"
echo "║  VM:         $VM_NAME ($VM_SIZE)                            "
echo "║  Public IP:  $PUBLIC_IP                                     "
echo "║  User:       $ADMIN_USER                                    "
echo "║  SSH:        ssh $ADMIN_USER@$PUBLIC_IP                     "
echo "║                                                             "
echo "║  DNS ACTION REQUIRED:                                       "
echo "║  Create A record: crud.henix.my.id → $PUBLIC_IP             "
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""
echo "Next: scp azure/bootstrap.sh $ADMIN_USER@$PUBLIC_IP:~/"
echo "      ssh $ADMIN_USER@$PUBLIC_IP 'chmod +x ~/bootstrap.sh && sudo ~/bootstrap.sh'"
