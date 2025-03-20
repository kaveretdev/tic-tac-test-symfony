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
# Create Supervisor configuration with proper formatting
RUN echo "[supervisord]\n\
nodaemon=true\n\
\n\
[program:php-fpm]\n\
command=docker-php-entrypoint php-fpm\n\
autostart=true\n\
autorestart=true\n\
stderr_logfile=/var/log/php-fpm.err.log\n\
stdout_logfile=/var/log/php-fpm.out.log\n\
\n\
[program:nginx]\n\
command=nginx -g 'daemon off;'\n\
autostart=true\n\
autorestart=true\n\
stderr_logfile=/var/log/nginx.err.log\n\
stdout_logfile=/var/log/nginx.out.log\n\
" > /etc/supervisord.conf
# Expose port 80
EXPOSE 80
# Start Supervisor to run both NGINX & PHP-FPM
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
