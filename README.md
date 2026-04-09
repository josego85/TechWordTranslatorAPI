# TechWordTranslatorAPI

[![Version](https://img.shields.io/badge/Version-1.18.0-blue.svg)](https://github.com/josego85/TechWordTranslatorAPI)
[![License](https://img.shields.io/badge/license-GPL%20v3-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-8.4.16-blue.svg)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.55.1-green.svg)](https://laravel.com/)
[![MySQL Version](https://img.shields.io/badge/MySQL-8.4.7-orange.svg?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Redis Version](https://img.shields.io/badge/Redis-7.4.7-red.svg?logo=redis&logoColor=white)](https://redis.io/)
[![Node.js Version](https://img.shields.io/badge/Node.js-v22.21.1-green.svg?logo=node.js&logoColor=white)](https://nodejs.org/)
[![NGINX](https://img.shields.io/badge/nginx-v1.29.4-brightgreen.svg)](https://nginx.org)
[![NPM Version](https://img.shields.io/badge/NPM-10.9.2-red.svg?logo=npm&logoColor=white)](https://www.npmjs.com/)
[![PHPUnit](https://img.shields.io/badge/PHPUnit-10.5.45-blue?logo=php&logoColor=white)](https://phpunit.de/)
[![Composer](https://img.shields.io/badge/Composer-2.9.3-885630?logo=composer&logoColor=white)](https://getcomposer.org/)
[![Docker](https://img.shields.io/badge/docker-ready-blue?logo=docker)](https://www.docker.com/)
[![SonarQube](https://img.shields.io/badge/SonarQube-Latest-orange?logo=sonarqube)](http://localhost:9000)
[![JWT](https://img.shields.io/badge/JWT-Authentication-000000?logo=jsonwebtokens&logoColor=white)](https://jwt.io/)
[![CI](https://github.com/josego85/TechWordTranslatorAPI/workflows/CI/badge.svg)](https://github.com/josego85/TechWordTranslatorAPI/actions/workflows/ci.yml)
![Last Commit](https://img.shields.io/github/last-commit/josego85/TechWordTranslatorAPI?logo=git&logoColor=white)

> A RESTful API (+ GraphQL) providing translations of IT-related terms between English, Spanish, and German — with automatic thematic classification via local LLM (Ollama).

---

## 📚 Quick Links

- [Getting Started Guide](docs/guides/setup.md)
- [Contributing Guidelines](CONTRIBUTING.md)
- [API Documentation](docs/api/README.md)
- [REST API Reference](docs/guides/rest.md)
- [GraphQL API Reference](docs/guides/graphql.md)
- [Auto-Classification Guide](docs/guides/classification.md)
- [Security & Performance](docs/guides/security.md)
- [Development Guide](docs/development/README.md)
- [Support](docs/SUPPORT.md)
- [Changelog](CHANGELOG.md)

---

## 🚀 Core Features

- English ↔ Spanish ↔ German translations  
- Automatic thematic classification via local LLM (Ollama + `prism-php/prism`) — 13 categories, many-to-many  
- Filter words by category (`?category=networking`)  
- Alphabetical sorting (`?sort=alpha-asc` / `?sort=alpha-desc`)  
- Cursor-based pagination support  
- JWT-based authentication  
- Dockerized environment for easy setup  
- Comprehensive PHPUnit test suite  

---

## 🗄️ Database Architecture

- **MySQL (8.4.7)** – Primary storage for words and translations

---

## ✨ Additional Features

- Eager-loaded translation relations for performance  
- Clean, versioned RESTful endpoints  
- GraphQL endpoint for flexible querying  
- Custom Content-Security-Policy headers  
- Redis-based caching system ([documentation](docs/cache/redis.md))
- Automatic word classification ([documentation](docs/guides/classification.md))

---

## 🤝 Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for contribution guidelines.

---

## 📄 License

Licensed under the [GPL-3.0 License](LICENSE).

---

© 2025 TechWordTranslatorAPI
