FROM php:8.4-fpm

# Instalar dependencias del sistema, Node.js 22 y extensiones para MariaDB/Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear usuario no-root 'appuser'
RUN groupadd -g 1000 appuser && \
    useradd -u 1000 -ms /bin/bash -g appuser appuser

WORKDIR /var/www

# Copiar el código del proyecto
COPY . /var/www

# Instalar dependencias de PHP y compilar frontend
RUN composer install --no-interaction --optimize-autoloader \
    && npm install \
    && npm run build \
    && rm -rf node_modules

# Ajustar permisos para el usuario no-root
RUN chown -R appuser:appuser /var/www

USER appuser

EXPOSE 9000