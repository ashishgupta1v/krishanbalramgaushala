#!/bin/bash
set -e

echo "🚀 Starting GauSeva Connect Deployment..."

# 1. Navigate to web directory
cd /var/www/krishanbalramgaushala

# 2. Prevent active devotee access during build
php artisan down || true

# 3. Pull latest changes
echo "📥 Pulling latest codebase from origin..."
git pull origin $(git branch --show-current)

# 4. Install Composer dependencies
echo "📦 Installing PHP packages..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# 5. Compile Frontend assets
echo "⚡ Compiling Vue assets..."
npm ci
npm run build

# 6. Run Database Migrations
echo "🗄️ Running migrations..."
php artisan migrate --force

# 7. Clear and optimize config cache
echo "🧹 Optimizing Laravel cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Set correct permissions for Nginx user (www-data)
echo "🔒 Securing files permissions..."
sudo chown -R www-data:www-data /var/www/krishanbalramgaushala
sudo chmod -R 775 /var/www/krishanbalramgaushala/storage /var/www/krishanbalramgaushala/bootstrap/cache

# 9. Restart queue workers managed by Supervisor
echo "⚙️ Restarting background queue workers..."
sudo supervisorctl restart gaushala-worker:*

# 10. Enable application
php artisan up

echo "✅ Deployment completed successfully! Application is live."
