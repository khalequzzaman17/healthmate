# HealthMate Backup & Disaster Recovery Guide

## 1. Backup Strategy
A robust backup strategy is critical for healthcare compliance and business continuity.

### 1.1 Database Backups
Automate daily encrypted database dumps using `mysqldump`:
```bash
mysqldump -u healthmate_user -p --single-transaction --quick --routines --triggers healthmate_production \
  | gzip \
  | openssl enc -aes-256-cbc -salt -pass file:/etc/healthmate/backup_key.bin -out /var/backups/db-$(date +%F).sql.gz.enc
```

### 1.2 Storage & File Backups
Medical attachments stored in `storage/app/private` must be synchronized offsite (e.g. to S3-compatible cloud storage with server-side encryption) using `rclone` or AWS CLI.

## 2. Disaster Recovery & Restoration Procedure
1. Provision new server using [deployment.md](deployment.md).
2. Decrypt and restore the database dump:
   ```bash
   openssl enc -d -aes-256-cbc -in /var/backups/db-2026-10-27.sql.gz.enc -pass file:/etc/healthmate/backup_key.bin \
     | gunzip \
     | mysql -u healthmate_user -p healthmate_production
   ```
3. Synchronize medical attachments to `storage/app/private`.
4. Run `php artisan migrate --force` to apply any outstanding migrations.
5. Restart PHP-FPM, Nginx, and queue workers.
