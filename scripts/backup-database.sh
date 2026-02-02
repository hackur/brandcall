#!/bin/bash
#
# BrandCall Database Backup Script
# Creates timestamped MySQL/MariaDB dumps
#
# Usage: ./backup-database.sh [backup_dir]
#

set -e

# Configuration
DB_NAME="${DB_NAME:-brandcall}"
DB_USER="${DB_USER:-brandcall}"
DB_PASS="${DB_PASS:-}"
BACKUP_DIR="${1:-/var/www/brandcall/backups}"
RETENTION_DAYS="${RETENTION_DAYS:-30}"

# Generate timestamp
TIMESTAMP=$(date +"%Y-%m-%d_%H-%M-%S")
BACKUP_FILE="${BACKUP_DIR}/${DB_NAME}_${TIMESTAMP}.sql.gz"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}🗄️  BrandCall Database Backup${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Database: ${DB_NAME}"
echo "Timestamp: ${TIMESTAMP}"
echo "Backup Dir: ${BACKUP_DIR}"
echo ""

# Create backup directory if it doesn't exist
if [ ! -d "$BACKUP_DIR" ]; then
    echo -e "${YELLOW}Creating backup directory...${NC}"
    mkdir -p "$BACKUP_DIR"
    chmod 700 "$BACKUP_DIR"
fi

# Build mysqldump command
DUMP_CMD="mysqldump --single-transaction --routines --triggers --events"

# Add credentials
if [ -n "$DB_PASS" ]; then
    DUMP_CMD="$DUMP_CMD -u${DB_USER} -p${DB_PASS}"
else
    DUMP_CMD="$DUMP_CMD -u${DB_USER}"
fi

DUMP_CMD="$DUMP_CMD ${DB_NAME}"

# Run backup
echo -e "${YELLOW}Creating backup...${NC}"
if $DUMP_CMD | gzip > "$BACKUP_FILE"; then
    FILESIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    echo -e "${GREEN}✅ Backup created successfully!${NC}"
    echo "   File: ${BACKUP_FILE}"
    echo "   Size: ${FILESIZE}"
else
    echo -e "${RED}❌ Backup failed!${NC}"
    exit 1
fi

# Cleanup old backups
echo ""
echo -e "${YELLOW}Cleaning up backups older than ${RETENTION_DAYS} days...${NC}"
DELETED=$(find "$BACKUP_DIR" -name "${DB_NAME}_*.sql.gz" -type f -mtime +${RETENTION_DAYS} -delete -print | wc -l)
echo "   Deleted: ${DELETED} old backup(s)"

# List recent backups
echo ""
echo -e "${YELLOW}Recent backups:${NC}"
ls -lh "$BACKUP_DIR"/${DB_NAME}_*.sql.gz 2>/dev/null | tail -5 || echo "   No backups found"

echo ""
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Backup complete!${NC}"
