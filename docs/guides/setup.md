# Getting Started Guide

This guide will help you setup the TechWordTranslatorAPI locally.

## Prerequisites

- PHP 8.4.7  
- Composer 2.8.8  
- Node.js v20.18.2 & NPM 10.8.2  
- Docker & Docker Compose  
- MySQL 8.4.5

## Installation

1. **Clone repository**  
   ```bash
   git clone git@github.com:proyectosbeta/TechWordTranslatorAPI.git
   cd TechWordTranslatorAPI
   ```

2. **Environment**  
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Install PHP dependencies**  
   ```bash
   composer install
   ```

4. **Install JS dependencies & compile assets**  
   ```bash
   npm install
   npm run dev
   npm run build
   ```

## Docker Setup

Build and start services:

```bash
docker compose build --no-cache
docker compose up -d
```

## Database

Run migrations & seeders:

```bash
php artisan migrate --seed
```

## Running Tests

```bash
php artisan test
```

## Access the App

- API: <http://localhost:8000/api>
