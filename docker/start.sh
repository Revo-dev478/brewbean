#!/bin/sh
set -e

echo "🚀 Starting BrewBean Application..."

# Create .env file from environment variables if they exist
if [ -n "$DB_HOST" ]; then
    echo "Creating .env file from environment variables..."
    cat > /var/www/html/.env << EOF
# Database Configuration
DB_HOST=${DB_HOST:-localhost}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD:-}
DB_DATABASE=${DB_DATABASE:-db_brewbeans}

# Midtrans Configuration
MIDTRANS_SERVER_KEY=${MIDTRANS_SERVER_KEY:-}
MIDTRANS_SERVER_KEY_ALT=${MIDTRANS_SERVER_KEY_ALT:-}
MIDTRANS_CLIENT_KEY=${MIDTRANS_CLIENT_KEY:-}
MIDTRANS_IS_PRODUCTION=${MIDTRANS_IS_PRODUCTION:-false}

# RajaOngkir Configuration
RAJAONGKIR_API_KEY=${RAJAONGKIR_API_KEY:-}
RAJAONGKIR_BASE_URL=${RAJAONGKIR_BASE_URL:-https://rajaongkir.komerce.id/api/v1}
EOF
    chown www-data:www-data /var/www/html/.env
    chmod 600 /var/www/html/.env
    echo "✅ .env file created successfully"
fi

# Ensure correct permissions
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html

# Create required directories
mkdir -p /var/run/php
mkdir -p /run/nginx

echo "✅ Application ready!"
exec /usr/bin/supervisord -c /etc/supervisord.conf
