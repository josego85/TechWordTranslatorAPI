FROM node:22.17.0 AS node_builder

WORKDIR /var/www

COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build


FROM php:8.4.10-fpm

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    git curl unzip zip && \
    pecl install redis xdebug && \
    docker-php-ext-enable redis xdebug && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo pdo_mysql zip gd && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=node_builder /var/www/public/build /var/www/public/build
COPY . .

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]