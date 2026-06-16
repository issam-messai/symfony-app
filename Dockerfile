FROM php:8.3.30-apache-bookworm

# Install app dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    zlib1g-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install -j$(nproc) pdo_mysql opcache intl zip \
    && mv ${PHP_INI_DIR}/php.ini-production ${PHP_INI_DIR}/php.ini

# Set the working directory
WORKDIR /var/www/

# install composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# copy app files, excluded files in .dockerignore
# composer.json copied first for Docker layered cache
COPY composer.json ./
RUN composer install --no-scripts --no-autoloader

COPY . .
RUN composer dump-autoload --no-scripts --optimize \
 && chown -R www-data:www-data /var/www

# enable mod rewrite required for symfony routing
RUN a2enmod rewrite \
  && sed -i 's|AllowOverride None|AllowOverride All\n        FallbackResource /index.php|' /etc/apache2/apache2.conf

# replace Apache default document root with a symlink to Symfony's public/ directory
RUN rm -rf html && ln -s public html

CMD ["apache2-foreground"]
