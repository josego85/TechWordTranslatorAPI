# Getting Started Guide

This guide will help you setup the TechWordTranslatorAPI locally.

## Prerequisites

- PHP 8.4.16
- Laravel 12.44.0
- Composer 2.9.3
- Node.js v22.21.1 & NPM 10.9.2
- Docker & Docker Compose
- MySQL 8.4.7
- Redis 7.4.7
- Ollama (optional, for automatic word classification — see [classification guide](classification.md))

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
   php artisan jwt:secret
   ```
   
   > This will generate and set the JWT_SECRET in your .env file, required for authentication.

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

## Ollama (optional — word classification)

Install Ollama natively on the host to enable automatic thematic classification:

```bash
# Linux
curl -fsSL https://ollama.com/install.sh | sh
ollama pull llama3.2

# macOS
brew install ollama
ollama pull llama3.2
```

Set in `.env`:
```bash
OLLAMA_URL=http://host.docker.internal:11434   # Mac/Windows
# OLLAMA_URL=http://172.17.0.1:11434           # Linux
OLLAMA_MODEL=llama3.2
```

If Ollama is not running, words are saved without categories. See the [classification guide](classification.md) for more details.

## Running Tests

```bash
php artisan test
```

## Access the App

- API: <http://localhost:8000/api>
