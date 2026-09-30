FROM php:8.3-apache

# Install dependency sistem + ekstensi PHP untuk Laravel, MySQL, dan PostgreSQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql pdo_pgsql gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Aktifkan Apache rewrite
RUN a2enmod rewrite

# Arahkan document root Apache ke Laravel /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy source code
COPY . /var/www/html

WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependency Laravel
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist

# Permission Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Railway biasanya inject PORT
ENV PORT=8080

# Apache listen mengikuti PORT Railway
RUN sed -ri 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf \
    && sed -ri 's/:80>/:${PORT}>/' /etc/apache2/sites-available/000-default.conf

EXPOSE 8080

CMD ["apache2-foreground"]
