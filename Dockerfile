FROM php:8.1-fpm as php-builder

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    libmcrypt-dev \
    mariadb-client \
    libzip-dev \
    && docker-php-ext-install pdo_mysql zip
RUN apt-get install -y nginx supervisor
# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configurar el directorio de trabajo
WORKDIR /var/www/html
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY nginx.conf /etc/nginx/sites-available/default
# Copiar la aplicación al contenedor
COPY . /var/www/html

# Instalar dependencias con Composer
RUN composer install

# Dar permisos al directorio storage
RUN chown -R www-data:www-data storage
RUN chmod -R 775 storage

# Exponer puerto 80 para Nginx
EXPOSE 80

# Comando de inicio para supervisord
CMD ["/usr/bin/supervisord"]