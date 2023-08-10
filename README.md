# Description

## Technologies

-   PHP 8.1.21
-   Composer 2.5.4
-   Laravel v10.17.1
-   MySQL 5.7.xx
-   PHPUnit 10.3.1

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

### Testing

#### PHPUnit

##### All the test

```bash

```
