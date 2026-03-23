# Usamos PHP con Apache
FROM php:8.2-apache

# Instalamos los drivers para PostgreSQL (Supabase)
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Habilitamos el modo rewrite (para futuras URLs amigables)
RUN a2enmod rewrite

# Exponemos el puerto interno
EXPOSE 80