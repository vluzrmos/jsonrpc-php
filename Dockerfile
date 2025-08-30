FROM php:5.6-cli-alpine

# Install system dependencies required for PHP extensions, then install the extensions
RUN apk update && apk add --no-cache \
    curl-dev \
    && docker-php-ext-install \
    curl \
    json \
    mbstring \
    && rm -rf /var/cache/apk/*

# Copy composer from the official image
COPY --from=composer:2.2 /usr/bin/composer /usr/local/bin/composer

# Set the working directory
WORKDIR /app

# Copy application source
COPY . .

RUN composer install

CMD ["php", "vendor/bin/phpunit"]
