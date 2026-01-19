# ============================================
# BrewBean PHP Application - Cloud Run Deployment
# ============================================

# Stage 1: Build stage
FROM php:8.2-fpm-alpine AS builder

# Install build dependencies
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libzip-dev \
    zip \
    unzip

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    mysqli \
    pdo_mysql \
    gd \
    mbstring \
    zip \
    opcache

# ============================================
# Stage 2: Production stage
# ============================================
FROM php:8.2-fpm-alpine

# Install runtime dependencies and nginx
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng \
    libjpeg-turbo \
    freetype \
    oniguruma \
    libzip

# Copy PHP extensions from builder
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Create required directories
RUN mkdir -p /var/run/php \
    && mkdir -p /var/log/supervisor \
    && mkdir -p /run/nginx

# Copy nginx configuration
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Copy PHP configuration
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Copy supervisor configuration (path sesuai gcp-laravel)
COPY docker/supervisord.conf /etc/supervisord.conf

# Copy application source
WORKDIR /var/www/html
COPY --chown=www-data:www-data . .

# Remove unnecessary files
RUN rm -rf docker/ \
    && rm -f *.sql \
    && rm -f .env.example \
    && rm -f prepros-6.config \
    && rm -rf .git

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Copy startup script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Expose port 8080 (Cloud Run default)
EXPOSE 8080

# Start services
CMD ["/start.sh"]
