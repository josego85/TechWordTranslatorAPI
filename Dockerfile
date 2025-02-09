FROM node:18 AS node_builder

WORKDIR /var/www

COPY package*.json ./
RUN npm install
COPY . .

RUN npm run build


FROM php:8.3-fpm
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    zip unzip git curl \
    && docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . /var/www
COPY --from=node_builder /var/www/public/build /var/www/public/build

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
