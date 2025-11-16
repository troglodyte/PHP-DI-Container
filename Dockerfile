FROM php:8.4-cli

# Install build tools and dependencies for PECL extensions
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install intl zip mbstring opcache \
    && rm -rf /var/lib/apt/lists/*

# Install and enable Xdebug via PECL
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Xdebug configuration (no zend_extension line here!)
RUN { \
    echo "xdebug.mode=develop,debug"; \
    echo "xdebug.start_with_request=yes"; \
    echo "xdebug.discover_client_host=true"; \
    echo "xdebug.client_host=host.docker.internal"; \
    echo "xdebug.client_port=9003"; \
    echo "xdebug.log_level=0"; \
  } > /usr/local/etc/php/conf.d/xdebug-custom.ini

WORKDIR /var/www/html
COPY . /var/www/html

#CMD ["php", "-v"]