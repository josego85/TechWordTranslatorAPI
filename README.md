# TechWordTranslatorAPI

[![Version](https://img.shields.io/badge/Version-1.8.0-blue.svg)](https://github.com/proyectosbeta/TechWordTranslatorAPI)
[![PHP Version](https://img.shields.io/badge/PHP-8.4-blue.svg)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-11.41.3-green.svg)](https://laravel.com/)
![License](https://img.shields.io/github/license/proyectosbeta/TechWordTranslatorAPI?color=blue)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ed?logo=docker&logoColor=white)
![Last Commit](https://img.shields.io/github/last-commit/proyectosbeta/TechWordTranslatorAPI?logo=git&logoColor=white)

> A RESTful API that provides translations of IT-related terms into Spanish and German.

---

## 📚 Table of Contents

- [TechWordTranslatorAPI](#techwordtranslatorapi)
  - [📚 Table of Contents](#-table-of-contents)
  - [🚀 Features](#-features)
  - [🛠️ Technologies](#️-technologies)
  - [🧑‍💻 Getting Started](#-getting-started)
    - [Installation](#installation)
    - [Docker Setup](#docker-setup)
    - [Database Migration](#database-migration)
    - [Web Access](#web-access)
    - [CSS Compilation](#css-compilation)
    - [Testing](#testing)
      - [Run All Tests](#run-all-tests)
      - [Run Specific Test](#run-specific-test)
  - [🚀 Production Deployment](#-production-deployment)
  - [📄 License](#-license)

---

## 🚀 Features

- RESTful API for IT terminology translations
- Supports English, Spanish, and German
- JWT-based authentication
- Dockerized environment for easy setup
- Comprehensive testing with PHPUnit
- Custom Content-Security-Policy headers

---

## 🛠️ Technologies

- **PHP**: 8.4.6
- **Laravel**: v11.41.3
- **MySQL**: 8
- **PHPUnit**: 10.3.1
- **Composer**: 2.8.8
- **Node.js**: v20.18.2
- **NPM**: 10.8.2
- **Docker**: 27.5.1

---

## 🧑‍💻 Getting Started

### Installation

```bash
cp .env.example .env
php artisan key:generate
```

### Docker Setup

```bash
docker compose build --no-cache
docker compose up -d
```

### Database Migration

```bash
php artisan migrate
```

### Web Access

Access the application at: [http://localhost:8000](http://localhost:8000)

### CSS Compilation

```bash
npm install
npm run dev
npm run build
```

### Testing

#### Run All Tests

```bash
php artisan test
```

#### Run Specific Test

```bash
php artisan test tests/Unit/WordServiceTest.php
```

---

## 🚀 Production Deployment

```bash
cd /home/$USER/repositoriosGit
git clone git@github.com:proyectosbeta/TechWordTranslatorAPI.git
sudo chown -R $USER:www-data TechWordTranslatorAPI
mv TechWordTranslatorAPI TechWordTranslatorAPI.proyectosbeta.net
cd TechWordTranslatorAPI.proyectosbeta.net
composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader
```

Copy the example environment file:

```bash
cp .env.example .env
```

Set the following environment variables in `.env`:

```
APP_ENV=production
APP_DEBUG=false
```

Generate the application key:

```bash
php artisan key:generate
```

Set appropriate permissions:

```bash
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
chmod 644 .env*
```

---

## 📄 License

This project is licensed under the [GPL-3.0 License](LICENSE).

---

For more information, visit the [official repository](https://github.com/proyectosbeta/TechWordTranslatorAPI/tree/refactor/code).