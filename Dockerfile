FROM dunglas/frankenphp

# Install dependencies & PHP extension pdo_pgsql + Composer
RUN apt-get update && apt-get install -y curl unzip git libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

WORKDIR /app

COPY . .

# Optional: untuk fresh install
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Optional: permission fix
RUN chmod -R 775 storage bootstrap/cache

# Copy custom Caddy config
COPY Caddyfile /etc/caddy/Caddyfile
