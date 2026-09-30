FROM php:8.3-apache

# Install ekstensi PHP yang dibutuhkan Laravel & MySQL
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libzip-dev zip unzip \
    && docker-php-ext-install pdo_mysql gd zip

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# Ubah arah root Apache ke folder /public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy seluruh kode aplikasi
COPY . /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Berikan izin akses folder storage dan cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
