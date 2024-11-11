FROM php:8.3-apache

# Installer les paquets nécessaires pour les extensions PHP et autres outils
RUN apt-get update -y && apt-get install -y \
    openssl \
    zip \
    unzip \
    git \
    libonig-dev \
    libzip-dev \
    libpng-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    mariadb-client \
    libgd-dev \
    libmagickwand-dev \
    libmagickcore-dev \
    imagemagick \
    && docker-php-ext-install pdo_mysql mbstring gd exif imagick \
    && apt-get clean

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Copier les fichiers du projet dans le conteneur
COPY . /app

# Définir les permissions
RUN chown -R www-data:www-data /app

# Installer les dépendances Composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Publier les configurations de JWT Auth et générer les clés nécessaires
CMD ["php", "artisan", "vendor:publish", "--provider=PHPOpenSourceSaver\\JWTAuth\\Providers\\LaravelServiceProvider"] && \
    CMD ["php", "artisan", "storage:link"] && \
    CMD ["php", "artisan", "key:generate"] && \
    CMD ["php", "artisan", "migrate:refresh"] && \
    CMD ["php", "artisan", "jwt:secret"] && \
    CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8181"]

EXPOSE 8181
