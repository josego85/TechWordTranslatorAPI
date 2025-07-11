FROM node:22.17.0 AS node_builder

WORKDIR /var/www

COPY package*.json ./
RUN npm install
COPY . .

RUN npm run build


FROM php:8.4.10-fpm

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    libzip-dev \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    git curl unzip zip && \
    pecl install redis && \
    docker-php-ext-enable redis && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo pdo_mysql zip gd && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . /var/www
COPY --from=node_builder /var/www/public/build /var/www/public/build

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
