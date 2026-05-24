FROM php:8.3.30-apachebookworm

# Install os and app dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    zlib1g-dev \
    libzip-dev \ 
    libpng-dev \
    libjpeg62-turbo-dev \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

RUN (docker-php-ext-configure gd --with-jpeg-dir=/usr/include/ || docker-php-ext-configure gd --with-jpeg) \
	&& docker-php-ext-install -j$(nproc) pdo_mysql pdo_pgsql pgsql opcache intl zip gd \
    && mv ${PHP_INI_DIR}/php.ini-production ${PHP_INI_DIR}/php.ini \
	&& a2enmod rewrite headers

# Set the working directory
WORKDIR /var/www/html

# copy app files
COPY config/ config/
COPY migrations migrations
COPY public/ public/
COPY src/ src/
COPY templates/ templates/

# install & run composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-scripts --no-autoloader
RUN composer dump-autoload --no-scripts --optimize
RUN chown -R www-data:www-data *

CMD ["apache2-foreground"]
