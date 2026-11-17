FROM php:8.4-fpm

# Instalar dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP para MariaDB/Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# CREAR USUARIO NO-ROOT (Requisito del profesor: ID 1000)
RUN groupadd -g 1000 archivum && \
    useradd -u 1000 -ms /bin/bash -g archivum archivum

WORKDIR /var/www

# Cambiar permisos del directorio de trabajo al usuario no-root
RUN chown -R archivum:archivum /var/www

# Cambiar al usuario no root para la ejecución
USER archivum

EXPOSE 9000
CMD ["php-fpm"]