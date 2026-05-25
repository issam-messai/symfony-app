FROM php:8.3.30-apachebookworm

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

# copy app files
COPY config/ config/
COPY migrations migrations
COPY src/ src/
COPY templates/ templates/
COPY public/ public/
RUN rm -rf html && ln -s public/ html

# install & run composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-scripts --no-autoloader
RUN composer dump-autoload --no-scripts --optimize
RUN chown -R www-data:www-data *

CMD ["apache2-foreground"]
