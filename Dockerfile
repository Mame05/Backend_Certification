FROM php:8.3-cli

RUN apt-get update -y && apt-get install -y \
    openssl \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    libzip-dev \
    libpng-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    mariadb-client \
    && docker-php-ext-install pdo_mysql mbstring \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php \
    -- --install-dir=/usr/local/bin \
    --filename=composer

WORKDIR /app

COPY . /app

RUN composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-dev

RUN chown -R www-data:www-data /app

EXPOSE 10000

CMD php artisan storage:link || true; \
    php artisan migrate --force; \
    php artisan serve --host=0.0.0.0 --port=${PORT:-10000}