FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libgd-dev \
    jpegoptim optipng pngquant gifsicle \
    vim \
    nano \
    build-essential

# Install Node.js 18.x and npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Configure GD extension
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy composer files first
COPY --chown=www:www composer.json composer.lock* package.json package-lock.json* /var/www/html/

# Change current user to www
USER www

# Install PHP and JS dependencies
WORKDIR /var/www/html
RUN composer config --global process-timeout 2000
RUN composer config --global github-protocols https
RUN composer config --global platform-check false
RUN composer config --global repositories.packagist composer https://repo.packagist.org
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev --ignore-platform-reqs || echo "Composer install failed, continuing..."
RUN composer require brick/money --no-interaction --prefer-dist --optimize-autoloader --no-dev --ignore-platform-reqs || echo "Composer require failed, continuing..."
RUN npm install

# Switch back to root for file operations
USER root

# Copy the rest of the application
COPY --chown=www:www . /var/www/html

# Set proper permissions
RUN chown -R www:www /var/www/html
RUN chmod -R 755 /var/www/html
RUN chmod -R 775 /var/www/html/storage
RUN chmod -R 775 /var/www/html/bootstrap/cache

# Switch back to www user
USER www

# Build assets
RUN npm run build

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
