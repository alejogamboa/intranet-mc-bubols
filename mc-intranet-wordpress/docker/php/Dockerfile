FROM php:8.0-apache

# Install necessary PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set the document root to the WordPress theme directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/tema wordpress

# Update the Apache configuration to use the new document root
RUN echo '<Directory /var/www/html/tema wordpress/>' \
    '    Options Indexes FollowSymLinks' \
    '    AllowOverride All' \
    '    Require all granted' \
    '</Directory>' \
    'DocumentRoot /var/www/html/tema wordpress' \
    > /etc/apache2/sites-available/000-default.conf

# Copy the WordPress theme files into the container
COPY ./tema wordpress /var/www/html/tema wordpress

# Set permissions for the WordPress directory
RUN chown -R www-data:www-data /var/www/html/tema wordpress

# Expose port 80
EXPOSE 80

CMD ["apache2-foreground"]