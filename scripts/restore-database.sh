#!/bin/bash
#
# BrandCall Database Restore Script
# Restores from a timestamped MySQL/MariaDB dump
#
# Usage: ./restore-database.sh <backup_file>
#

set -e

# Configuration
DB_NAME="${DB_NAME:-brandcall}"
DB_USER="${DB_USER:-brandcall}"
DB_PASS="${DB_PASS:-}"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check arguments
if [ -z "$1" ]; then
    echo -e "${RED}Error: No backup file specified${NC}"
    echo ""
    echo "Usage: $0 <backup_file.sql.gz>"
    echo ""
    echo "Available backups:"
    ls -lh /var/www/brandcall/backups/*.sql.gz 2>/dev/null || echo "  No backups found"
    exit 1
fi

BACKUP_FILE="$1"

# Check if file exists
if [ ! -f "$BACKUP_FILE" ]; then
    echo -e "${RED}Error: Backup file not found: ${BACKUP_FILE}${NC}"
    exit 1
fi

echo -e "${YELLOW}🗄️  BrandCall Database Restore${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Database: ${DB_NAME}"
echo "Backup: ${BACKUP_FILE}"
echo ""

# Confirm restore
echo -e "${RED}⚠️  WARNING: This will OVERWRITE the current database!${NC}"
read -p "Are you sure you want to continue? (yes/no): " CONFIRM

if [ "$CONFIRM" != "yes" ]; then
    echo "Restore cancelled."
    exit 0
fi

# Build mysql command
MYSQL_CMD="mysql"
if [ -n "$DB_PASS" ]; then
    MYSQL_CMD="$MYSQL_CMD -u${DB_USER} -p${DB_PASS}"
else
    MYSQL_CMD="$MYSQL_CMD -u${DB_USER}"
fi
MYSQL_CMD="$MYSQL_CMD ${DB_NAME}"

# Restore backup
echo ""
echo -e "${YELLOW}Restoring backup...${NC}"
if gunzip -c "$BACKUP_FILE" | $MYSQL_CMD; then
    echo -e "${GREEN}✅ Database restored successfully!${NC}"
else
    echo -e "${RED}❌ Restore failed!${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Restore complete!${NC}"
