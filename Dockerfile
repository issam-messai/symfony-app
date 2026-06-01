FROM php:8.4-apache

# Install app dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    zlib1g-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install -j$(nproc) pdo_mysql mysqli opcache intl zip \
    && mv ${PHP_INI_DIR}/php.ini-production ${PHP_INI_DIR}/php.ini

# Enable Apache rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/

# Copy app files
COPY composer.json composer.lock ./
COPY .env .env
COPY bin/ bin/
COPY config/ config/
COPY migrations migrations
COPY src/ src/
COPY templates/ templates/
COPY public/ public/

# Install Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Permissions
RUN chown -R www-data:www-data /var/www

# Symlink html → public
RUN rm -rf /var/www/html && ln -s /var/www/public /var/www/html

# COPY DU VIRTUALHOST
COPY docker/apache-vhost.conf /etc/apache2/sites-available/symfony.conf

# ACTIVER LE VIRTUALHOST ET DÉSACTIVER LE DEFAULT
RUN a2dissite 000-default && a2ensite symfony

# Entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

CMD ["docker-entrypoint.sh"]
