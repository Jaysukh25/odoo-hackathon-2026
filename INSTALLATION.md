# FleetFlow Installation Guide

## Quick Start

### Prerequisites
- PHP 8.1 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Node.js 16+
- Web server (Apache/Nginx)

### Installation Steps

1. **Clone the repository**
```bash
git clone <repository-url>
cd FleetFlow
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fleetflow
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Create database**
```sql
CREATE DATABASE fleetflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

6. **Create required tables**
Ensure these tables exist in your database:
- users
- vehicles
- drivers
- trips
- maintenance_logs
- fuel_logs
- driver_scores
- trip_status_history
- notifications

7. **Seed sample data**
```bash
php artisan db:seed
```

8. **Build assets**
```bash
npm run build
```

9. **Start the application**
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

### Demo Accounts
- Manager: manager@fleetflow.com / password
- Dispatcher: dispatcher@fleetflow.com / password
- Safety: safety@fleetflow.com / password
- Finance: finance@fleetflow.com / password

## Detailed Installation

### System Requirements

#### PHP Requirements
- PHP 8.1 or higher
- Required extensions:
  - php-fpm
  - php-mysql
  - php-xml
  - php-mbstring
  - php-curl
  - php-zip
  - php-bcmath
  - php-json
  - php-tokenizer
  - php-ctype
  - php-fileinfo

#### Database Requirements
- MySQL 5.7+ or MariaDB 10.3+
- InnoDB storage engine
- UTF-8 character set

#### Web Server Requirements
- Apache 2.4+ with mod_rewrite
- Nginx 1.18+ with PHP-FPM
- SSL certificate (recommended for production)

#### System Requirements
- Minimum 2GB RAM
- 20GB storage space
- PHP memory limit: 256M or higher
- Max execution time: 300 seconds

### Installation Methods

#### Method 1: Standard Installation

1. **Download FleetFlow**
```bash
# Using Git
git clone https://github.com/your-org/fleetflow.git
cd fleetflow

# Or download ZIP file and extract
```

2. **Install PHP Dependencies**
```bash
composer install --optimize-autoloader --no-dev
```

3. **Install Node Dependencies**
```bash
npm install
npm run build
```

4. **Environment Configuration**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure Application**
Edit `.env` file:
```env
APP_NAME=FleetFlow
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fleetflow
DB_USERNAME=fleetflow_user
DB_PASSWORD=secure_password

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
```

6. **Database Setup**
```sql
-- Create database
CREATE DATABASE fleetflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user (optional)
CREATE USER 'fleetflow_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON fleetflow.* TO 'fleetflow_user'@'localhost';
FLUSH PRIVILEGES;
```

7. **Create Database Tables**
Run these SQL commands to create required tables:

```sql
-- Users table
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL DEFAULT 'user',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Vehicles table
CREATE TABLE vehicles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    model VARCHAR(255) NOT NULL,
    license_plate VARCHAR(255) UNIQUE NOT NULL,
    max_capacity DECIMAL(10,2) NOT NULL,
    odometer DECIMAL(10,2) NOT NULL,
    status ENUM('available', 'on_trip', 'in_shop', 'out_of_service') NOT NULL DEFAULT 'available',
    out_of_service BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Drivers table
CREATE TABLE drivers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    license_number VARCHAR(255) NOT NULL,
    license_expiry DATE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    status ENUM('available', 'on_duty', 'off_duty') NOT NULL DEFAULT 'available',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Trips table
CREATE TABLE trips (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    driver_id BIGINT UNSIGNED NOT NULL,
    origin VARCHAR(255) NOT NULL,
    destination VARCHAR(255) NOT NULL,
    cargo_weight DECIMAL(10,2) NOT NULL,
    distance DECIMAL(10,2) NOT NULL,
    estimated_duration INT NOT NULL,
    status ENUM('draft', 'dispatched', 'on_trip', 'completed', 'cancelled') NOT NULL DEFAULT 'draft',
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    arrived_late BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (driver_id) REFERENCES drivers(id)
);

-- Maintenance logs table
CREATE TABLE maintenance_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    cost DECIMAL(10,2) NOT NULL,
    odometer_at_service DECIMAL(10,2) NOT NULL,
    performed_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
);

-- Fuel logs table
CREATE TABLE fuel_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    liters DECIMAL(10,2) NOT NULL,
    cost_per_liter DECIMAL(10,2) NOT NULL,
    cost DECIMAL(10,2) NOT NULL,
    odometer DECIMAL(10,2) NOT NULL,
    fuel_date TIMESTAMP NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
);

-- Driver scores table
CREATE TABLE driver_scores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    driver_id BIGINT UNSIGNED NOT NULL,
    score DECIMAL(5,2) NOT NULL,
    reason TEXT NOT NULL,
    score_date TIMESTAMP NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (driver_id) REFERENCES drivers(id)
);

-- Trip status history table
CREATE TABLE trip_status_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trip_id BIGINT UNSIGNED NOT NULL,
    old_status VARCHAR(255) NULL,
    new_status VARCHAR(255) NOT NULL,
    changed_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES trips(id)
);

-- Notifications table
CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(255) NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

8. **Seed Sample Data**
```bash
php artisan db:seed
```

9. **Set Permissions**
```bash
sudo chown -R www-data:www-data /path/to/fleetflow
sudo chmod -R 775 /path/to/fleetflow/storage
sudo chmod -R 775 /path/to/fleetflow/bootstrap/cache
```

10. **Optimize Application**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Method 2: Docker Installation

1. **Using Docker Compose**
```yaml
# docker-compose.yml
version: '3.8'
services:
  app:
    build: .
    ports:
      - "8000:8000"
    volumes:
      - .:/var/www/html
    depends_on:
      - mysql
      - redis
    environment:
      - DB_HOST=mysql
      - REDIS_HOST=redis

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: fleetflow
      MYSQL_USER: fleetflow
      MYSQL_PASSWORD: password
      MYSQL_ROOT_PASSWORD: root
    volumes:
      - mysql_data:/var/lib/mysql

  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"

volumes:
  mysql_data:
```

2. **Build and Run**
```bash
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

#### Method 3: Shared Hosting

1. **Upload Files**
Upload all files to your hosting directory via FTP or file manager

2. **Install Dependencies**
```bash
# If SSH access available
composer install --optimize-autoloader --no-dev

# Or use hosting provider's composer
```

3. **Configure Environment**
Edit `.env` file with your hosting details

4. **Set Permissions**
Use hosting file manager or cPanel to set permissions

5. **Run Commands**
Use hosting control panel or SSH to run artisan commands

### Web Server Configuration

#### Apache Configuration
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /path/to/fleetflow/public
    
    <Directory /path/to/fleetflow/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/fleetflow_error.log
    CustomLog ${APACHE_LOG_DIR}/fleetflow_access.log combined
</VirtualHost>
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/fleetflow/public;
    index index.php index.html;
    
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

### SSL Configuration

#### Let's Encrypt (Recommended)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache -y

# Obtain certificate
sudo certbot --apache -d your-domain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

#### Manual SSL
1. Obtain SSL certificate from your provider
2. Upload certificate files
3. Configure web server for SSL
4. Update APP_URL in `.env` to use HTTPS

### Database Setup

#### MySQL Setup
```bash
# Install MySQL
sudo apt install mysql-server -y

# Secure installation
sudo mysql_secure_installation

# Create database and user
mysql -u root -p
```

#### MariaDB Setup
```bash
# Install MariaDB
sudo apt install mariadb-server -y

# Secure installation
sudo mysql_secure_installation

# Create database and user
mysql -u root -p
```

### Redis Setup (Optional but Recommended)
```bash
# Install Redis
sudo apt install redis-server -y

# Configure Redis
sudo nano /etc/redis/redis.conf

# Start Redis
sudo systemctl start redis-server
sudo systemctl enable redis-server
```

### Testing Installation

#### Verify Installation
```bash
# Check Laravel version
php artisan --version

# Check configuration
php artisan about

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

#### Run Tests
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test tests/Feature/AuthTest.php
```

#### Check Functionality
1. Visit your application in browser
2. Login with demo accounts
3. Test all major features
4. Check error logs if issues occur

### Troubleshooting

#### Common Issues

**500 Internal Server Error**
```bash
# Check permissions
sudo chown -R www-data:www-data /path/to/fleetflow
sudo chmod -R 755 /path/to/fleetflow/storage

# Check logs
tail -f storage/logs/laravel.log

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

**Database Connection Error**
```bash
# Verify database credentials
php artisan tinker
>>> DB::connection()->getPdo();

# Check database exists
mysql -u username -p
>>> SHOW DATABASES;

# Test connection
php artisan migrate:status
```

**Asset Loading Issues**
```bash
# Rebuild assets
npm run build

# Check file permissions
ls -la public/

# Clear browser cache
```

**Permission Issues**
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Fix ownership
sudo chown -R www-data:www-data .
```

#### Error Messages

**"Class not found"**
```bash
# Regenerate autoloader
composer dump-autoload
```

**"Key not found"**
```bash
# Generate new key
php artisan key:generate
```

**"Route not defined"**
```bash
# Clear route cache
php artisan route:clear
php artisan route:cache
```

### Performance Optimization

#### Production Optimizations
```bash
# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize composer
composer dump-autoload --optimize
```

#### Server Optimizations
- Enable OPcache
- Use Redis for caching
- Configure PHP-FPM properly
- Enable Gzip compression
- Use CDN for static assets

### Maintenance Mode

#### Enable Maintenance
```bash
php artisan down
```

#### Disable Maintenance
```bash
php artisan up
```

### Backup Setup

#### Database Backup
```bash
# Create backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u username -p'password' fleetflow > /backups/fleetflow_$DATE.sql
```

#### File Backup
```bash
# Backup application files
tar -czf /backups/fleetflow_files_$DATE.tar.gz /path/to/fleetflow
```

### Update Process

#### Updating FleetFlow
```bash
# Backup current version
cp -r /path/to/fleetflow /path/to/fleetflow_backup

# Update dependencies
composer update
npm update

# Run migrations if needed
php artisan migrate

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild assets
npm run build

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Support

#### Getting Help
- Check documentation: `/docs`
- Search GitHub issues
- Contact support: support@fleetflow.com
- Community forum: forum.fleetflow.com

#### Reporting Issues
1. Check existing issues
2. Create detailed bug report
3. Include system information
4. Provide steps to reproduce
5. Add screenshots if applicable

---

## Installation Complete! 🎉

Your FleetFlow installation is now complete. Here's what to do next:

1. **Login** with one of the demo accounts
2. **Explore** the dashboard and features
3. **Add** your own vehicles, drivers, and data
4. **Configure** settings for your organization
5. **Train** your team on the system

For additional help, check our [FAQ](FAQ.md) or [Documentation](docs/).

Happy fleet managing! 🚛
