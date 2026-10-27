# HealthMate Production Deployment Guide

## 1. System Requirements
- **OS**: Ubuntu 22.04 / 24.04 LTS Linux
- **Web Server**: Nginx
- **PHP**: PHP 8.3 or PHP 8.4+ with extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `intl`, `mbstring`, `openssl`, `pcre`, `pdo_mysql`, `redis`, `session`, `tokenizer`, `xml`
- **Database**: MySQL 8.0+ or MariaDB 10.11+ (InnoDB Engine)
- **Node.js**: v20 LTS+ for Vite asset compilation

## 2. Deployment Steps

### 2.1 Clone Repository & Install Dependencies
```bash
git clone git@github.com:your-org/healthmate.git /var/www/healthmate
cd /var/www/healthmate
composer install --no-dev --optimize-autoloader
npm ci && npm run build
```

### 2.2 Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```
Configure `.env` with production database credentials, APP_URL, and Redis configuration:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://healthmate.app

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=healthmate_production
DB_USERNAME=healthmate_user
DB_PASSWORD=your_secure_db_password

QUEUE_CONNECTION=redis
CACHE_STORE=redis
```

### 2.3 Run Migrations & Cache Config
```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

### 2.4 File Permissions
```bash
chown -R www-data:www-data /var/www/healthmate
chmod -R 775 /var/www/healthmate/storage /var/www/healthmate/bootstrap/cache
```

### 2.5 Nginx Configuration (`/etc/nginx/sites-available/healthmate.conf`)
```nginx
server {
    listen 80;
    server_name healthmate.app www.healthmate.app;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name healthmate.app www.healthmate.app;
    root /var/www/healthmate/public;

    ssl_certificate /etc/letsencrypt/live/healthmate.app/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/healthmate.app/privkey.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 2.6 Background Queue Worker (Systemd)
Create `/etc/systemd/system/healthmate-worker.service`:
```ini
[Unit]
Description=HealthMate Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/healthmate/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```
Enable and start the service:
```bash
systemctl daemon-reload
systemctl enable healthmate-worker.service
systemctl start healthmate-worker.service
```

### 2.7 Cron Scheduler
Add to `crontab -e -u www-data`:
```cron
* * * * * cd /var/www/healthmate && php artisan schedule:run >> /dev/null 2>&1
```
