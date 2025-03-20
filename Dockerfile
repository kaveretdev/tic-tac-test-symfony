# Use PHP 8.2 with FPM
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache nginx supervisor git unzip libpng-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql gd intl opcache \
    && docker-php-ext-enable opcache

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html/var \
    && chmod -R 775 /var/www/html/var

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

# Start Supervisor (manages PHP-FPM & NGINX)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
