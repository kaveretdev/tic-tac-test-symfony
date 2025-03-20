# Use PHP 8.2 with FPM on Alpine (lightweight)
FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and required PHP extensions
RUN apk update && apk add --no-cache \
    nginx \
    supervisor \
    git \
    unzip \
    libpng \
    libpng-dev \
    freetype \
    freetype-dev \
    libjpeg-turbo \
    libjpeg-turbo-dev \
    icu \
    icu-dev \
    postgresql-dev \
    mariadb-connector-c-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql gd intl opcache \
    && docker-php-ext-enable opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Ensure var directory exists before setting permissions
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var \
    && chmod -R 775 var

# Install Symfony dependencies
RUN composer install --no-dev --optimize-autoloader

# Configure NGINX
RUN echo ' \
worker_processes auto; \
events { worker_connections 1024; } \
http { \
    include /etc/nginx/mime.types; \
    sendfile on; \
    server { \
        listen 80; \
        server_name _; \
        root /var/www/html/public; \
        location / { \
            try_files $uri /index.php$is_args$args; \
        } \
        location ~ ^/index\.php(/|$) { \
            fastcgi_pass 127.0.0.1:9000; \
            fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name; \
            include fastcgi_params; \
        } \
        error_log /var/log/nginx/error.log; \
        access_log /var/log/nginx/access.log; \
    } \
}' > /etc/nginx/nginx.conf

# Configure Supervisor
RUN echo ' \
[supervisord] \
nodaemon=true \
[program:php-fpm] \
command=docker-php-entrypoint php-fpm \
autostart=true \
autorestart=true \
[program:nginx] \
command=nginx -g "daemon off;" \
autostart=true \
autorestart=true \
' > /etc/supervisord.conf

# Expose port 80
EXPOSE 80

# Start Supervisor to run both NGINX & PHP-FPM
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
