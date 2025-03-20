# Use PHP 8.2 with FPM on Alpine
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
# Create a non-root user
RUN addgroup -g 1000 symfony && adduser -G symfony -u 1000 -D symfony
# Copy application files
COPY . .
# Ensure var/cache and var/log exist with correct permissions
RUN mkdir -p var/cache var/log \
    && chown -R symfony:symfony /var/www/html \
    && chmod -R 775 /var/www/html
# Switch to non-root user
USER symfony
# Install Symfony dependencies as non-root user
RUN composer install --no-dev --optimize-autoloader
# Switch back to root user for supervisor and nginx
USER root
# Create NGINX configuration
RUN echo " \
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
            try_files \$uri /index.php\$is_args\$args; \
        } \
        location ~ ^/index\\.php(/|$) { \
            fastcgi_pass 127.0.0.1:9000; \
            fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name; \
            include fastcgi_params; \
        } \
        error_log /var/log/nginx/error.log; \
        access_log /var/log/nginx/access.log; \
    } \
}" > /etc/nginx/nginx.conf

# Create separate Supervisor config files
RUN mkdir -p /etc/supervisor.d/

# Create main supervisord.conf
RUN echo "[supervisord]\nnodaemon=true\n" > /etc/supervisord.conf
RUN echo "[include]\nfiles = /etc/supervisor.d/*.ini" >> /etc/supervisord.conf

# Create program configs
RUN echo "[program:php-fpm]\ncommand=docker-php-entrypoint php-fpm\nautostart=true\nautorestart=true\nstderr_logfile=/var/log/php-fpm.err.log\nstdout_logfile=/var/log/php-fpm.out.log" > /etc/supervisor.d/php-fpm.ini
RUN echo "[program:nginx]\ncommand=nginx -g 'daemon off;'\nautostart=true\nautorestart=true\nstderr_logfile=/var/log/nginx.err.log\nstdout_logfile=/var/log/nginx.out.log" > /etc/supervisor.d/nginx.ini

# Expose port 80
EXPOSE 80
# Start Supervisor to run both NGINX & PHP-FPM
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
