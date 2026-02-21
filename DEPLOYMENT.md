# FleetFlow Deployment Guide

## Prerequisites

### Server Requirements
- PHP 8.1 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Web server (Apache/Nginx)
- Composer
- Node.js (for asset compilation)
- SSL certificate (recommended)

### System Requirements
- Minimum 2GB RAM
- 20GB storage space
- PHP extensions: mbstring, openssl, pdo, tokenizer, xml, ctype, json, bcmath, fileinfo

## Installation Steps

### 1. Server Setup
```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install required PHP extensions
sudo apt install php8.1 php8.1-fpm php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip php8.1-bcmath php8.1-json -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

### 2. Database Setup
```sql
CREATE DATABASE fleetflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'fleetflow_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON fleetflow.* TO 'fleetflow_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Application Deployment
```bash
# Clone or upload application files
cd /var/www/html
git clone <repository-url> fleetflow
cd fleetflow

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install

# Environment configuration
cp .env.example .env
php artisan key:generate

# Configure .env file
nano .env
```

### 4. Environment Configuration
```env
APP_NAME=FleetFlow
APP_ENV=production
APP_KEY=your-generated-key
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fleetflow
DB_USERNAME=fleetflow_user
DB_PASSWORD=strong_password

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Database Setup
```bash
# Create database tables (using existing structure)
# Ensure the following tables exist:
# - users
# - vehicles
# - drivers
# - trips
# - maintenance_logs
# - fuel_logs
# - driver_scores
# - trip_status_history
# - notifications

# Seed sample data (optional)
php artisan db:seed
```

### 6. Asset Compilation
```bash
# Build frontend assets
npm run build

# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7. File Permissions
```bash
# Set proper file permissions
sudo chown -R www-data:www-data /var/www/html/fleetflow
sudo find /var/www/html/fleetflow -type f -exec chmod 644 {} \;
sudo find /var/www/html/fleetflow -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/html/fleetflow/storage
sudo chmod -R 775 /var/www/html/fleetflow/bootstrap/cache
```

## Web Server Configuration

### Apache Configuration
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    Redirect permanent / https://your-domain.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName your-domain.com
    DocumentRoot /var/www/html/fleetflow/public
    
    SSLEngine on
    SSLCertificateFile /path/to/ssl/cert.pem
    SSLCertificateKeyFile /path/to/ssl/private.key
    
    <Directory /var/www/html/fleetflow/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/fleetflow_error.log
    CustomLog ${APACHE_LOG_DIR}/fleetflow_access.log combined
</VirtualHost>
```

### Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com;
    root /var/www/html/fleetflow/public;
    index index.php index.html;
    
    ssl_certificate /path/to/ssl/cert.pem;
    ssl_certificate_key /path/to/ssl/private.key;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.ht {
        deny all;
    }
}
```

## SSL Configuration

### Let's Encrypt (Recommended)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache -y

# Obtain SSL certificate
sudo certbot --apache -d your-domain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

## Performance Optimization

### Redis Setup
```bash
# Install Redis
sudo apt install redis-server -y

# Configure Redis
sudo nano /etc/redis/redis.conf
# Set: supervised systemd

# Start Redis
sudo systemctl start redis-server
sudo systemctl enable redis-server
```

### PHP-FPM Optimization
```ini
# Edit /etc/php/8.1/fpm/php.ini
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 64M
post_max_size = 64M
```

## Monitoring & Logging

### Application Monitoring
```bash
# Setup log rotation
sudo nano /etc/logrotate.d/fleetflow
```

```
/var/www/html/fleetflow/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
    postrotate
        /usr/bin/php /var/www/html/fleetflow/artisan log:clear --keep=30
    endscript
}
```

### Health Checks
```bash
# Create health check endpoint
# Add to routes/web.php:
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});
```

## Backup Strategy

### Database Backup
```bash
# Create backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u fleetflow_user -p'strong_password' fleetflow > /backups/fleetflow_$DATE.sql
find /backups -name "fleetflow_*.sql" -mtime +30 -delete
```

### File Backup
```bash
# Backup application files
tar -czf /backups/fleetflow_files_$DATE.tar.gz /var/www/html/fleetflow
```

## Maintenance Commands

### Scheduled Tasks
```bash
# Add to crontab
* * * * * cd /var/www/html/fleetflow && php artisan schedule:run >> /dev/null 2>&1
```

### Common Commands
```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check system status
php artisan about
php artisan route:list
php artisan config:show

# Maintenance mode
php artisan down
php artisan up
```

## Troubleshooting

### Common Issues

#### 500 Internal Server Error
- Check file permissions
- Verify .env configuration
- Check Laravel logs: `tail -f storage/logs/laravel.log`

#### Database Connection Issues
- Verify database credentials
- Check database server status
- Test connection: `php artisan tinker`

#### Asset Loading Issues
- Rebuild assets: `npm run build`
- Check file permissions in public directory
- Verify asset URLs

### Performance Issues
- Enable query logging
- Check slow queries
- Monitor memory usage
- Optimize database indexes

## Security Considerations

### Production Security Checklist
- [ ] Debug mode disabled
- [ ] Error reporting disabled
- [ ] Proper file permissions set
- [ ] SSL certificate installed
- [ ] Firewall configured
- [ ] Regular backups enabled
- [ ] Security updates applied
- [ ] Monitoring configured

### Security Headers
```php
// Add to AppServiceProvider boot method
$response->headers->set('X-Frame-Options', 'DENY');
$response->headers->set('X-Content-Type-Options', 'nosniff');
$response->headers->set('X-XSS-Protection', '1; mode=block');
```

## Scaling Considerations

### Load Balancing
- Configure multiple web servers
- Use shared storage for uploads
- Implement session affinity
- Configure database clustering

### Database Optimization
- Add appropriate indexes
- Optimize slow queries
- Consider read replicas
- Implement connection pooling

### Caching Strategy
- Redis for session storage
- Application-level caching
- CDN for static assets
- Database query caching

## Support & Maintenance

### Regular Tasks
- Weekly: Check logs and performance metrics
- Monthly: Apply security updates
- Quarterly: Review and optimize performance
- Annually: Security audit and penetration testing

### Emergency Procedures
- Database recovery procedures
- Application rollback process
- Incident response plan
- Communication protocols

### Contact Information
- Development team: dev@company.com
- System administrator: admin@company.com
- Emergency support: emergency@company.com
