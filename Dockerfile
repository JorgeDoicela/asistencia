FROM php:8.2-apache

# Habilitar mod_rewrite de Apache y AllowOverride para .htaccess
RUN a2enmod rewrite \
    && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Instalar extensiones requeridas para MySQL (PDO MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . /var/www/html/

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html
