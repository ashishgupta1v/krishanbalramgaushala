#!/bin/bash
# Server provisioning script for GauSeva Connect on Ubuntu 22.04 / 24.04 LTS
set -e

echo "=== GauSeva Connect — Hostinger KVM2 VPS Setup ==="

# 1. Update OS packages
echo "📦 Updating system package list..."
sudo apt update && sudo apt upgrade -y

# 2. Install Git, Curl, Zip, SQLite
echo "📦 Installing system utilities..."
sudo apt install -y git curl zip unzip sqlite3 libsqlite3-dev build-essential supervisor software-properties-common

# 3. Add PHP Repository and Install PHP 8.2 & extensions
echo "📦 Installing PHP 8.2 and production extensions..."
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-sqlite3 php8.2-mysql php8.2-zip php8.2-gd php8.2-intl

# 4. Install Composer (Globally)
echo "📦 Installing Composer..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# 5. Install Node.js (LTS version) and NPM
echo "📦 Installing Node.js LTS and NPM..."
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# 6. Install Nginx and Certbot
echo "📦 Installing Nginx and Certbot for SSL..."
sudo apt install -y nginx certbot python3-certbot-nginx

# 7. Configure Directory Structure
echo "📁 Setting up application directory..."
sudo mkdir -p /var/www
sudo chown -R $USER:www-data /var/www

# Check if application repository is already cloned, if not clone or suggest
if [ ! -d "/var/www/krishanbalramgaushala" ]; then
    echo "⚠️ Application directory /var/www/krishanbalramgaushala not found."
    echo "Please clone your Git repository into /var/www/krishanbalramgaushala:"
    echo "  git clone <YOUR_REPO_URL> /var/www/krishanbalramgaushala"
    echo "For now, creating a placeholder directory..."
    mkdir -p /var/www/krishanbalramgaushala
fi

# 8. Set up SQLite Database (if used)
echo "🗄️ Preparing SQLite database container..."
mkdir -p /var/www/krishanbalramgaushala/database || true
touch /var/www/krishanbalramgaushala/database/database.sqlite || true
chown -R www-data:www-data /var/www/krishanbalramgaushala/database

# 9. Register Nginx Server Block Configuration
echo "🌐 Registering Nginx configuration..."
sudo cp /var/www/krishanbalramgaushala/deploy/nginx.conf /etc/nginx/sites-available/krishanbalramgaushala
sudo ln -sf /etc/nginx/sites-available/krishanbalramgaushala /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default || true
sudo nginx -t
sudo systemctl restart nginx

# 10. Register Supervisor Queue Worker
echo "⚙️ Registering Supervisor daemon..."
sudo cp /var/www/krishanbalramgaushala/deploy/supervisor.conf /etc/supervisor/conf.d/gaushala-worker.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all

# 11. Register Daily Automated Milestones Cron Job
echo "🕒 Registering cron runner for automatic blessings..."
CRON_JOB="* * * * * cd /var/www/krishanbalramgaushala && php artisan schedule:run >> /dev/null 2>&1"
(crontab -l 2>/dev/null | grep -F "$CRON_JOB") || (crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -

echo ""
echo "=== Setup Base Completed! ==="
echo "Next Steps to do manually:"
echo "1. Put your production .env inside /var/www/krishanbalramgaushala/.env"
echo "2. Run php artisan key:generate"
echo "3. Run certbot --nginx -d YOUR_DOMAIN to secure your site with HTTPS"
echo "4. Execute the deploy script to finish first installation: bash /var/www/krishanbalramgaushala/deploy/deploy.sh"
