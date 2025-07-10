FROM php:8.4-fpm

# Arguments defined in docker-compose.yml
ARG user=laravel
ARG uid=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    vim \
    nano \
    supervisor \
    nginx \
    cron \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Node.js and npm (for Reverb and frontend assets)
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Copy configurations
COPY ./docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./docker/nginx/nginx.conf /etc/nginx/sites-available/default

# Setup cron for Laravel scheduler - run as www-data
RUN echo "* * * * * www-data cd /var/www && php artisan schedule:run >> /dev/null 2>&1" >> /etc/crontab

# Create log directories
RUN mkdir -p /var/log/supervisor
RUN mkdir -p /var/www/storage/logs
RUN mkdir -p /run/php

# Set working directory
WORKDIR /var/www

# Change ownership of our applications
RUN chown -R $user:www-data /var/www
RUN chmod -R 755 /var/www/storage

# Expose ports
EXPOSE 80 8080

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost || exit 1

# Start supervisor (which will start nginx, php-fpm, horizon, reverb, and cron)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]