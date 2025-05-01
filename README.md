# TechWordTranslatorAPI

[![Version](https://img.shields.io/badge/Version-1.9.0-blue.svg)](https://github.com/proyectosbeta/TechWordTranslatorAPI)
[![License](https://img.shields.io/badge/license-GPL%20v3-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-8.4.6-blue.svg)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-11.44.2-green.svg)](https://laravel.com/)
[![MySQL Version](https://img.shields.io/badge/MySQL-8.0.42-orange.svg?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Node.js Version](https://img.shields.io/badge/Node.js-v20.18.2-green.svg?logo=node.js&logoColor=white)](https://nodejs.org/)
[![NPM Version](https://img.shields.io/badge/NPM-10.8.2-red.svg?logo=npm&logoColor=white)](https://www.npmjs.com/)
[![PHPUnit](https://img.shields.io/badge/PHPUnit-10.5.45-blue?logo=php&logoColor=white)](https://phpunit.de/)
[![Composer](https://img.shields.io/badge/Composer-2.8.8-885630?logo=composer&logoColor=white)](https://getcomposer.org/)
[![Docker](https://img.shields.io/badge/docker-ready-blue?logo=docker)](https://www.docker.com/)
[![SonarQube](https://img.shields.io/badge/SonarQube-Latest-orange?logo=sonarqube)](http://localhost:9000)
[![JWT](https://img.shields.io/badge/JWT-Authentication-000000?logo=jsonwebtokens&logoColor=white)](https://jwt.io/)
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
    - [SonarQube Analysis](#sonarqube-analysis)
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
- **Laravel**: v11.44.2
- **MySQL**: 8.0.42
- **PHPUnit**: 10.5.45
- **Composer**: 2.8.8
- **Node.js**: v20.18.2
- **NPM**: 10.8.2
- **Docker**: 27.5.1
- **SonarQube Server:** Integrated for code quality analysis.

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

### SonarQube Analysis

Access SonarQube dashboard at: [http://localhost:9000](http://localhost:9000)

Default credentials:
- **Username:** `admin`
- **Password:** `admin`

For detailed SonarQube setup and usage, see our [SonarQube Documentation](docs/development/sonarqube.md).

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