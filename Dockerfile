# Usamos PHP 8.2 con Apache
FROM php:8.2-apache

# 1. Instalar dependencias del sistema y librerías para Postgres
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    zip \
    && docker-php-ext-install pdo pdo_pgsql

# 2. Habilitar mod_rewrite de Apache (Vital para las rutas de Laravel)
RUN a2enmod rewrite

# 3. Ajustar la carpeta pública como raíz del servidor web
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Instalar Composer dentro del contenedor
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Configurar directorio de trabajo
WORKDIR /var/www/html

# 6. Copiar los archivos del proyecto al contenedor
COPY . .

# 7. Instalar dependencias de PHP (Production mode)
RUN composer install --no-dev --optimize-autoloader

# 8. Dar permisos a las carpetas de almacenamiento
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Exponer el puerto 80
EXPOSE 80


# 10. COMANDO DE ARRANQUE (Esto reemplaza al Start Command de la web)
# Solo arranca Apache, sin migraciones
CMD sh -c "apache2-foreground"