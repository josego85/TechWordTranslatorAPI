# Description

A REST API that has words that are used in the IT world and are translated into Spanish and German.

## Technologies

-   PHP 8.4
-   PHP FPM 8.4
-   Composer 2.8.5
-   Laravel v11.41.3
-   MySQL 8
-   PHPUnit 10.3.1
-   NPM 10.8.2
-   NodeJS v20.18.2
-   Docker 27.5.0

## Dev

### Install

```bash
cp .env.example .env
php artisan key:generate
```

### Docker

```bash
docker compose up -d --build
```

### Migration

```bash
php artisan migrate
```

### Web access

```
http://localhost:8000
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


