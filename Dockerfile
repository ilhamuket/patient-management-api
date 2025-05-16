FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    git \
    unzip \
    zip \
    curl \
    && docker-php-ext-install pdo_pgsql pgsql mbstring xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*




# Install Composer global
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

# Copy semua file ke container (optional, bisa pakai volume juga)
# COPY . /var/www

# Jalankan composer install saat build (opsional, biasanya dijalankan manual)
# RUN composer install --no-dev --optimize-autoloader

# Set permission (opsional)
# RUN chown -R www-data:www-data /var/www

EXPOSE 9000
CMD ["php-fpm"]
