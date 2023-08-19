# Description

A REST API that has words that are used in the IT world and are translated into Spanish and German.

## Technologies

-   PHP 8.2.8
-   Composer 2.5.8
-   Laravel v10.18.0
-   MySQL 8
-   PHPUnit 10.3.1
-   NPM 9.6.6
-   NodeJS v14.21.3

## Dev

### Install

```bash
cp .env.example .env
php artisan key:generate
composer install
```

#### Run server

```bash
php artisan serve
```

##### Web access

```
http://127.0.0.1:8000
```

### Create database

```bash
mysql -u root -p
CREATE DATABASE techword
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

#### Create user

```bash
GRANT ALL PRIVILEGES ON techword.* TO 'techword-user'@'localhost' IDENTIFIED BY 'xxxxxx';
FLUSH PRIVILEGES;
```

#### Migration

```bash
php artisan migrate
```

### Compile CSS

```bash
npm run dev
npm run build
```

### Testing

#### PHPUnit

##### All the test

```bash
php artisan test
```

##### Specific test

```bash
php artisan test tests/Unit/WordServiceTest.php
```

## Docker

```bash
#docker-compose build
#docker-compose up
# docker run -p 8888:80 myapp
```

### Web access

```
http://localhost
```

## Production

```bash
cd /home/$USER/repositoriosGit
git clone git@github.com:proyectosbeta/TechWordTranslatorAPI.git
sudo chown -R $USER:www-data TechWordTranslatorAPI
mv TechWordTranslatorAPI TechWordTranslatorAPI.proyectosbeta.net
cd TechWordTranslatorAPI.proyectosbeta.net
composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader
```

Copiamos el .env.example:

```bash
cp .env.example .env
```


Estas variables deberían de tener:

```
APP_ENV=production
APP_DEBUG=false
```


Laravel provee un modo de generar un APP_KEY desde la consola, hay que acceder a la carpeta del proyecto y ejecutar:

```bash
php artisan key:generate
```

Cambiamos las constantes de .env
Volvemos a la terminal:

```bash
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
chmod 644 .env*
```


