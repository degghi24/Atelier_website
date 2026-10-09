FROM php:8.4-apache

# Estensioni per collegarsi a MariaDB/MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Configurazione PHP da sviluppo: mostra gli errori a schermo
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Abilita mod_rewrite (utile se userete .htaccess)
RUN a2enmod rewrite