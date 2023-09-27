# Utiliza una imagen base con PHP 8.1 o superior y Apache
FROM php:8.1-apache

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Instala las dependencias necesarias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev && rm -rf /var/lib/apt/lists/*

# Instala la extensión zip de PHP
RUN docker-php-ext-install pdo pdo_mysql zip

# Copia el código de tu aplicación al contenedor
COPY . .

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala las dependencias de Composer
RUN composer install

# Expone el puerto 80
EXPOSE 80

# Comando para iniciar Apache
CMD ["apache2-foreground"]
