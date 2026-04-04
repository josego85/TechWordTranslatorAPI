# CLAUDE.md — TechWordTranslatorAPI

Reference guide for Claude Code when working on this project.
Last updated: 2026-04-04 (Sort param on GET /api/v1/words)

---

## Project Overview

**TechWordTranslatorAPI** is a RESTful API (+ GraphQL) for translating IT-world terms between English, Spanish, and German (extensible to any ISO 639-1 language). Built with Laravel 12, PHP 8.4, JWT authentication, and Redis cache.

- **Repository:** github.com/josego85/TechWordTranslatorAPI
- **Current version:** 1.17.0 (1.18.0 in progress — branch `feature/auto-classification`)
- **License:** MIT
- **Main branch:** `main`

---

## Tech Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Framework | Laravel | 12.55.1 |
| Language | PHP | ^8.4 |
| Database | MySQL | 8.4.7 |
| Cache/Queue | Redis | 7.4.7 |
| Authentication | JWT (php-open-source-saver/jwt-auth) | 2.9.0 |
| GraphQL | Lighthouse (nuwave/lighthouse) | 6.65.0 |
| LLM integration | prism-php/prism (Ollama provider) | — |
| Local LLM | Ollama + llama3.2 (native host install) | — |
| Web server | Nginx | 1.29.4 |
| Node.js | Frontend build | 22.21.1 |
| Containers | Docker + Docker Compose | — |

---

## Git Branch Conventions

| Prefix | Use |
|--------|-----|
| `main` | Production, protected |
| `develop` | Integration |
| `feature/**` | New features |
| `fix/**` | Bug fixes |
| `claude/**` | Claude Code generated branches |

> **IMPORTANT:** CI/CD workflows trigger on `main`, `develop`, `feature/**`, `fix/**`, and `claude/**`.

---

## Application Architecture

### Layers (pattern in use)

```
HTTP Request
    └─> Middleware (JWT / Sanctum, SecurityHeaders, CORS, CSP, Rate Limiting)
        └─> FormRequest (validation + Policy authorization)
            └─> Controller (app/Http/Controllers/API/V1/)
                └─> Policy (app/Policies/) ← Gate::allows('write', Model)
                └─> Service (app/Services/)
                    └─> Repository (app/Repositories/)
                        └─> Model (app/Models/) → MySQL
                            └─> CacheService → Redis
```

### Key Directory Structure

```
app/
├── Http/
│   ├── Controllers/API/V1/   # AuthController, WordController, TranslationController, ServiceTokenController
│   ├── Middleware/            # JWTMiddleware, SecurityHeaders
│   ├── Requests/              # Form requests with validation + Policy authorization
│   └── Resources/             # JSON:API resources (WordResource, TranslationResource)
├── Interfaces/                # Contracts: WordRepositoryInterface, TranslationRepositoryInterface, CategoryRepositoryInterface
├── Models/                    # User, Word, Translation, Category
├── Policies/                  # WordPolicy, TranslationPolicy — write authorization via Gate
├── Providers/                 # AuthServiceProvider — model→policy mappings registered here
├── Repositories/              # WordRepository, TranslationRepository, CacheableWordRepository, CategoryRepository
├── Services/                  # WordService, TranslationService, CacheService, ClassificationService
├── Support/Csp/               # ContentPolicy (Spatie CSP)
└── Exceptions/                # WordNotFoundException, TranslationException

routes/
├── api.php                    # API v1 endpoints
└── web.php                    # Web routes

database/
├── migrations/                # 6 migrations (users, words, translations, refactor, categories, word_category pivot)
├── factories/
└── seeders/

.github/
├── workflows/
│   ├── ci.yml                 # Main CI pipeline
│   ├── security-audit.yml     # Daily security audit
│   └── codeql.yml             # CodeQL static analysis (weekly)
└── actions/
    ├── setup-php-project/     # Reusable PHP composite action
    └── setup-node-project/    # Reusable Node.js composite action
```

---

## API Endpoints

### Auth (no JWT required unless noted)

```
POST  /api/v1/user/register   throttle: 3/60min per IP
POST  /api/v1/user/login      throttle: 5/min per IP + 10/15min per email
POST  /api/v1/user/refresh    [jwt.verify]
POST  /api/v1/user/logout     [jwt.verify]
```

### Words

```
GET    /api/v1/words           Paginated; ?search=, ?category=, ?sort= (alpha-asc|alpha-desc, default: alpha-asc)
GET    /api/v1/words/{id}      With embedded translations and categories
POST   /api/v1/words           [auth:api,sanctum] + WordPolicy::write
PUT    /api/v1/words/{id}      [auth:api,sanctum] + WordPolicy::write
DELETE /api/v1/words/{id}      [auth:api,sanctum] + WordPolicy::write
```

### Translations

```
GET    /api/v1/translations    Paginated
GET    /api/v1/translations/{id}
POST   /api/v1/translations    [auth:api,sanctum] + TranslationPolicy::write
PUT    /api/v1/translations/{id} [auth:api,sanctum] + TranslationPolicy::write
DELETE /api/v1/translations/{id} [auth:api,sanctum] + TranslationPolicy::write
```

### Service Tokens (Sanctum)

```
POST   /api/v1/service-tokens           [jwt.verify] — creates Sanctum token for MCP server
DELETE /api/v1/service-tokens/{tokenId} [jwt.verify] — revokes token
```

### GraphQL

```
GET/POST /graphql              AttemptAuthentication (optional)
```

---

## Database

### Normalized schema (post-migration 2025_11_21)

```sql
users:         id, name, email(unique), password, email_verified_at, remember_token, timestamps
words:         id, english_word, timestamps
translations:  id, word_id(FK→words), language(5), translation(text), timestamps
               UNIQUE KEY (word_id, language)
               INDEX (language)
categories:    id, slug(unique, 50), name(100), timestamps
               13 predefined slugs (networking, databases, security, algorithms, data-structures,
               operating-systems, programming-languages, web, cloud, devops, hardware,
               artificial-intelligence, other)
word_category: word_id(FK→words), category_id(FK→categories)
               PRIMARY KEY (word_id, category_id) — pivot, no timestamps
```

> The `translations` table is normalized: one row per language per word. Supports unlimited ISO 639-1 languages (not just `es`/`de`).
> The `categories` / `word_category` tables implement a proper many-to-many — a word can belong to 1–3 categories.

### Existing migrations

1. `2014_10_12_000000_create_users_table.php`
2. `2023_08_10_142806_create_words_table.php`
3. `2023_08_10_152932_create_translations_table.php`
4. `2025_11_21_131414_refactor_translations_table_to_normalized_structure.php`
5. `{ts}_create_categories_table.php`
6. `{ts}_create_word_category_table.php`

---

## Security

### Critical rules — NEVER violate

- **Do not expose** JWT details in error responses (use generic messages)
- **Do not disable** rate limiting in production
- **Do not store** secrets in source code; use environment variables
- **Do not commit** real `.env` (only `.env.example`)
- **Do not lower** PHPStan level below 5
- **Do not lower** test coverage threshold below 74%
- **Do not disable** JWT blacklist (`JWT_BLACKLIST_ENABLED=true` always)
- **Do not use** `APP_DEBUG=true` in production
- **Do not use** wildcard CORS origins (`*`)

### Implemented security layers

#### JWT Authentication
- Algorithm: `HS256` (symmetric, key in `JWT_SECRET`)
- TTL: 15 minutes (`JWT_TTL=15`)
- Refresh TTL: 1440 minutes / 24h (`JWT_REFRESH_TTL=1440`)
- Blacklist enabled — tokens invalidated on logout
- `lock_subject: true` — prevents model impersonation
- Required claims: `iss, iat, exp, nbf, sub, jti`

#### Rate Limiting
- Register: 3 attempts / 60 min / IP
- Login: 5 attempts / min / IP + 10 / 15min / email (IP rotation resistant)
- General API: 60 req / min / IP

#### Security Headers (`SecurityHeaders` middleware)
```
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=()
Strict-Transport-Security: max-age=31536000; includeSubDomains; preload  (HTTPS only)
```

#### Content Security Policy (Spatie CSP)
- Policy class: `App\Support\Csp\ContentPolicy`
- Controlled via env: `CSP_ENABLED=true`
- Strict directives: `default-src 'none'`, `frame-ancestors 'none'`, `object-src 'none'`

#### CORS
- Allowed origins: `CORS_ALLOWED_ORIGINS` (env variable)
- `credentials: false` — JWT-based, not cookie-based
- Allowed methods: GET, POST, PUT, PATCH, DELETE, OPTIONS

#### Password Validation (`RegisterRequest`)
- Minimum 12 characters
- Mixed uppercase and lowercase
- Numbers and symbols required
- `uncompromised()` check (HaveIBeenPwned API)

#### Dual Authentication (JWT + Sanctum)

- **JWT guard** (`api`): human users — short-lived tokens (15 min), full write access
- **Sanctum guard**: MCP server — long-lived opaque tokens, scoped abilities
- Write routes use `auth:api,sanctum` middleware — Laravel tries JWT first, falls back to Sanctum
- `currentAccessToken()` returns `null` for JWT users, `PersonalAccessToken` for Sanctum users
- **Sanctum token abilities**: `['words:write', 'translations:write']`
- Service token lifecycle: human logs in (JWT) → calls `POST /api/v1/service-tokens` → gets Sanctum token for MCP

#### Authorization (Laravel Policies)

- `WordPolicy::write(User $user)` — registered in `AuthServiceProvider` for `Word` model
- `TranslationPolicy::write(User $user)` — registered for `Translation` model
- Rule: JWT user (`currentAccessToken() === null`) → always allowed; Sanctum user → must `tokenCan(ability)`
- FormRequests call `$user->can('write', Model::class)` in `authorize()`
- Controllers `destroy()` call `$request->user()->cannot('write', Model::class)`
- PHPDoc workaround: `/** @var mixed $token */ $token = $user->currentAccessToken();` to silence PHPStan false positive (Sanctum PHPDoc says non-nullable but runtime can be null)

#### Audit Logging
- Auth events: register, login (success/fail), refresh, logout (IP + user agent logged)
- CRUD mutations: create/update/delete on Word and Translation (IP logged)
- JWT errors: logged server-side, never exposed to the client

---

## Development Commands

### Composer Scripts

```bash
# Run all quality checks (recommended before committing)
composer ci

# Unit and integration tests
composer test

# Tests with HTML coverage report
composer test-coverage

# PHPStan static analysis (level 5)
composer phpstan

# Check code style without modifying
composer pint-test

# Apply code style fixes
composer pint-fix

# View Rector suggestions (dry-run, no changes)
composer rector-check

# Apply Rector refactorings
composer rector-fix
```

### Artisan

```bash
# Generate JWT secret (required on initial setup)
php artisan jwt:secret

# Migrations
php artisan migrate
php artisan migrate:fresh --seed

# Cache (production)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Docker

```bash
# Start full development environment
docker compose up -d

# With SonarQube (quality analysis)
docker compose --profile tools/quality up -d

# View logs
docker compose logs -f app

# Run commands inside the container
docker compose exec app php artisan migrate
docker compose exec app composer test
```

**Available Docker services:**

| Service | Description | Port |
|---------|-------------|------|
| `app` | PHP-FPM 8.4 | 9000 |
| `nginx` | Nginx 1.29.4 | 8000:80 |
| `mysql` | MySQL 8.4.7 | 3306:3306 |
| `redis` | Redis 7.4.7-alpine | 6379:6379 |
| `sonarqube` | SonarQube (profile: `tools/quality`) | 9000:9000 |

---

## Tests

### Configuration

- **Framework:** PHPUnit 11.5.50
- **Database:** SQLite in-memory (`:memory:`) — fast, isolated
- **Cache:** Array driver
- **Queue:** Sync
- **BCRYPT_ROUNDS:** 4 (speed in tests)
- **Throttling:** Disabled in `TestCase::setUp()`
- **JWT Middleware:** Disabled in base `TestCase` (enabled in auth integration tests)

### Coverage threshold: **74%** (enforced in CI — currently 92.21%, 211 tests, 598 assertions)

### Test structure

```
tests/
├── Feature/               # HTTP integration tests
│   ├── AuthApiTest.php
│   ├── WordApiTest.php
│   ├── TranslationApiTest.php
│   ├── ServiceTokenApiTest.php
│   └── GraphQL/           # WordGraphQLTest, TranslationGraphQLTest, MutationGraphQLTest
└── Unit/
    ├── Models/
    ├── Services/          # WordServiceTest, TranslationServiceTest, CacheServiceTest, ClassificationServiceTest
    ├── Repositories/      # CategoryRepositoryTest
    ├── Middleware/        # JWTMiddlewareTest, SecurityHeadersTest
    ├── Policies/          # WordPolicyTest, TranslationPolicyTest
    ├── Requests/          # RegisterRequestTest, LoginRequestTest
    └── Resources/         # TranslationCollectionTest
```

> `ClassificationServiceTest` uses `Prism::fake()` — no real Ollama connection needed in tests.

### Running specific tests

```bash
# Single file
vendor/bin/phpunit tests/Feature/AuthApiTest.php

# Single test method
vendor/bin/phpunit --filter test_user_can_login

# With coverage
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html coverage
```

---

## GitHub Actions CI/CD

### Workflows

#### `ci.yml` — Main pipeline
**Triggers:** push to `main`, `develop`, `feature/**`, `fix/**`, `claude/**`; PR to `main`/`develop`

| Job | Description | Blocking |
|-----|-------------|---------|
| `dependency-review` | CVE check on dependencies (PRs only) | Yes (moderate+) |
| `security-audit` | `composer audit` | Yes |
| `php-tests` | PHPUnit + coverage ≥74% + Codecov | Yes |
| `code-style` | Laravel Pint format check | Yes |
| `static-analysis` | PHPStan level 5 | Yes |
| `rector-check` | Rector suggestions | No (`continue-on-error`) |
| `frontend-build` | `npm run build` | Yes |

#### `security-audit.yml` — Security audit
**Triggers:** push, PR, **daily at 2:00 AM UTC**, manual dispatch

- NPM audit (level: moderate)
- Composer audit + JSON artifacts (30-day retention)
- Dependency review on PRs

#### `codeql.yml` — CodeQL Analysis
**Triggers:** push, PR, **weekly Mondays at 6:00 AM UTC**

- Language: `javascript-typescript`
- Results uploaded to GitHub Security tab

### Workflow security best practices (already implemented)

- **All Actions pinned by commit SHA** (not floating tags) — prevents supply chain attacks
- **Minimum permissions declared** (`contents: read`; `security-events: write` only where needed)
- **`concurrency`** configured to cancel duplicate runs on same branch
- **Composite reusable actions** in `.github/actions/` to reduce duplication
- **Dependency caching** configured for both Composer and npm

### Required GitHub Secrets

```
CODECOV_TOKEN   — Token to upload coverage to Codecov
SONAR_TOKEN     — SonarQube token (self-hosted, not cloud)
```

---

## Code Quality Tools

### PHPStan (static analysis)

- **Level:** 5
- **Analyzed paths:** `app/`, `database/`, `routes/`
- **Config:** `phpstan.neon`
- Active options: `checkUnionTypes`, `checkExplicitMixed`, `checkDynamicProperties`, `inferPrivatePropertyTypeFromConstructor`
- Ignored: JWT-Auth facade calls (no PHPStan stubs available for this package)

### Laravel Pint (code style)

- **Preset:** `laravel`
- **Config:** `pint.json`
- Key rules:
  - `declare_strict_types: true` — required in every PHP file
  - `single_quote: true`
  - `ordered_imports: alpha`
  - `no_unused_imports: true`
  - `array_syntax: short` — use `[]` not `array()`
  - `binary_operator_spaces: align` for `=`

### Rector (automated refactoring)

- **Config:** `rector.php`
- **PHP target:** 8.4
- **Active sets:** `LARAVEL_CODE_QUALITY`, `LARAVEL_COLLECTION`, `CODE_QUALITY`
- **No parallelism** (`->withoutParallel()`) — Docker IPC issues
- CI mode: `--dry-run` (non-blocking)

### SonarQube

- Available as Docker service (profile: `tools/quality`)
- Scanner integrated in docker-compose
- Token configured in `.env` (`SONAR_TOKEN`)

---

## Critical Environment Variables

```bash
# Application
APP_ENV=production
APP_DEBUG=false          # NEVER true in production
APP_KEY=                 # Generate with: php artisan key:generate

# JWT — ALL required
JWT_SECRET=              # Generate with: php artisan jwt:secret
JWT_ALGO=HS256
JWT_TTL=15               # Minutes
JWT_REFRESH_TTL=1440     # Minutes (24h)
JWT_BLACKLIST_ENABLED=true  # NEVER false

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=techword
DB_USERNAME=techword-user
DB_PASSWORD=             # Use a strong password

# Cache
CACHE_DRIVER=redis
REDIS_HOST=redis         # Docker service name, NOT localhost
REDIS_PORT=6379

# CORS
CORS_ALLOWED_ORIGINS="http://localhost:3000"  # Set to real domain in production

# CSP
CSP_ENABLED=true

# Ollama — AI word classification (native host install, NOT Docker)
# Install: curl -fsSL https://ollama.com/install.sh | sh && ollama pull llama3.2
OLLAMA_URL=http://host.docker.internal:11434   # Mac/Windows; use http://172.17.0.1:11434 on Linux
OLLAMA_MODEL=llama3.2

# SonarQube (development)
SONAR_TOKEN=
```

---

## Code Patterns and Conventions

### PHP

- **Always** `declare(strict_types=1);` at the top of every PHP file
- **Explicit types** on parameters and return types (required by PHPStan level 5)
- **Repository Pattern** — never access models directly from controllers
- **Service Layer** — business logic belongs in `Services/`, not in Controllers or Repositories
- **Form Requests** — all input validation in dedicated Request classes
- **Resources** — all response transformation in Resource classes
- **Custom exceptions** for domain errors (`WordNotFoundException`, `TranslationException`)
- **Constructor injection** of interfaces, not concrete implementations

### Naming conventions

```
Controllers:  PascalCase + Controller  (WordController)
Services:     PascalCase + Service     (WordService)
Repositories: PascalCase + Repository  (WordRepository)
Interfaces:   PascalCase + Interface   (WordRepositoryInterface)
Requests:     Verb + Noun + Request    (StoreWordRequest, UpdateWordRequest)
Resources:    PascalCase + Resource    (WordResource, WordCollection)
Models:       PascalCase singular      (Word, Translation, User)
Tests:        Class + Test             (WordApiTest, WordServiceTest)
```

### API response format

- JSON:API format via `ResourceCollection` and `Resource`
- Cursor pagination with links
- Timestamps in ISO 8601
- Error messages: generic, never expose internal details

---

## Cache

- **Driver:** Redis (`predis/predis`)
- **Default TTL:** 1440 minutes (24h)
- **Key patterns:**
  - `word:{id}` — single word (includes eager-loaded categories)
  - `words:perPage:{n}:page:{n}` — paginated list
  - `words:perPage:{n}:page:{n}:search:{hash}` — filtered list
  - `words:perPage:{n}:page:{n}:category:{slug}` — category-filtered list
  - `words:perPage:{n}:page:{n}:sort:{alpha-asc|alpha-desc}` — sorted list (combinable with search/category)
  - `translation:{id}` — single translation
  - `translations:perPage:{n}:page:{n}` — paginated list
- **Invalidation:** on Word and Translation create/update/delete
- **Decorator pattern:** `CacheableWordRepository` and `CacheableTranslationRepository` wrap their base repos; both registered in `AppServiceProvider`
- **Pending:** cache with tags for selective invalidation

---

## Open Tasks (active TODO)

### In Progress

- Opcache configuration
- Grafana monitoring
- Swagger/OpenAPI documentation

### Completed (2026-04-04)

- ✅ Alphabetical sort on `GET /api/v1/words` — `?sort=alpha-asc` (default) / `?sort=alpha-desc`; validated in `IndexRequest` (`Rule::in`), applied via `match` in `WordRepository::getAll()`; cache key includes `:sort:` segment
- ✅ Sort on GraphQL `words` query — `WordSort` enum (`ALPHA_ASC` | `ALPHA_DESC`, default `ALPHA_ASC`), `scopeSortBy` on `Word` model wired via `@scope(name: "sortBy")`; 3 new tests — 211 tests, 598 assertions

### Completed (2026-04-03)

- ✅ Automatic thematic classification — `ClassificationService` (Prism + Ollama), `Category` model, `CategoryRepository`, `categories`/`word_category` tables, 13 slugs, `?category=` filter (REST + GraphQL), `categories[]` override in requests — 208 tests, 590 assertions, 92.21% line coverage → see `docs/tasks/auto-classification/`

### Completed (2026-03-25)

- ✅ Docs for Xdebug, Nginx, Pint — `docs/development/xdebug.md`, `nginx.md`, `pint.md`; `docs/guides/graphql.md` updated with mutations, auth, cache, security

### Completed (2026-03-24)

- ✅ GraphQL integration — tests, security limits, cache (`@cache`), mutations (custom PHP resolvers → Services), Model Observers for audit logging — 196 tests, 566 assertions → see `docs/tasks/graphql-integration/`
- ✅ Cache for Translation endpoints — `CacheableTranslationRepository` decorator
- ✅ PHPUnit test coverage exceeded 74% (208 tests, 590 assertions, 92.21%)

### Recommended Next Steps

- ISO 639-1 language code validation in Request layer
- Cache with tags for selective invalidation (REST layer — GraphQL already uses tagged cache)
- Unit tests for Models and Repositories
- E2E tests for REST API
- Performance benchmarks for search functionality
- Opcache configuration
- Grafana monitoring
- Swagger/OpenAPI documentation

---

## Initial Setup (new environment)

```bash
# 1. Clone and enter
git clone git@github.com:josego85/TechWordTranslatorAPI.git
cd TechWordTranslatorAPI

# 2. Environment variables
cp .env.example .env

# 3. PHP dependencies
composer install

# 4. Node.js dependencies
npm ci

# 5. Generate keys
php artisan key:generate
php artisan jwt:secret

# 6. Start Docker
docker compose up -d

# 7. Migrate database
docker compose exec app php artisan migrate --seed

# 8. Build frontend
npm run build

# 9. Verify quality
composer ci
```

---

## Security Guidelines for Claude Code

When generating or modifying code in this project:

1. **Validate all inputs** in Form Requests before reaching Services
2. **Use Repository interfaces** — never access models directly from controllers
3. **Never expose** stack traces or internal messages in HTTP responses
4. **Maintain PHPStan level 5** — do not add `@phpstan-ignore` without documented justification
5. **Do not reduce** JWT token TTL below configured values
6. **Add audit logging** on any operation that mutates data (pattern already established)
7. **Pin GitHub Actions by SHA** when adding new steps to workflows
8. **Declare minimum permissions** in any new workflow
9. **Never use wildcard CORS** (`*`) — always use specific origins
10. **Validate ISO 639-1 codes** when adding `language` field validation (currently pending)
11. **Password strength** must include `uncompromised()` check — do not relax this rule
12. **Rate limiting** must be applied to any new auth endpoint

---

## Troubleshooting

### JWT token invalid/expired
- Verify `JWT_SECRET` is set and `JWT_BLACKLIST_ENABLED=true`
- In tests: base `TestCase` disables JWT middleware by default

### PHPStan fails on JWT-Auth facades
- Errors for `JWTAuth::parseToken()` etc. are in `ignoreErrors` in `phpstan.neon` — this is expected

### Rector parallelism error in Docker
- `rector.php` uses `->withoutParallel()` — this is intentional (Docker IPC issues)

### Coverage below 74%
- Add tests for uncovered code paths
- Inspect `coverage.xml` to identify gaps

### `npm run build` fails in CI
- Verify Dockerfile Stage 1 uses Node 22
- Check `package-lock.json` has no conflicts

### Redis connection refused
- Use `REDIS_HOST=redis` (Docker service name, not `localhost`)
- Check Redis service health check in docker-compose.yml
