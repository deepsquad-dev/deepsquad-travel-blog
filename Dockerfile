FROM php:8.4-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends libicu-dev libsqlite3-dev unzip git \
    && docker-php-ext-install intl pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
EXPOSE 8765

CMD ["php", "bin/cake.php", "server", "-H", "0.0.0.0", "-p", "8765"]
