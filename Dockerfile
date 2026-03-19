FROM php:8.2-apache

# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite para Apache (útil para rutas amigables)
RUN a2enmod rewrite

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Ajustar permisos (opcional pero recomendado)
RUN chown -R www-data:www-data /var/www/html
