# Changelog

All notable changes to this project will be documented in this file.  
This project follows [Semantic Versioning](https://semver.org/spec/v2.0.0.html) and adheres to the [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) guidelines.

---

## [v1.13.0] - 2025-07-17

### Added
- Integrated **GraphQL** support for querying `Word` and `Translation` entities.
- Added GraphQL types for `Word` and `Translation` models.
- Implemented GraphQL queries:
  - `words` and `word(id: ID!)` to fetch English words.
  - `translations` and `translation(id: ID!)` to fetch Spanish and German translations.
  - Query to fetch translations by word ID.
- Added GraphQL schema definitions including types and query declarations (no custom resolvers).
- Added **Xdebug** support for PHP debugging.
- Added **Nginx** container as a web server for the PHP-FPM service.
- Added `index`, `show`, `update`, `store`, and `destroy` methods to `TranslationController`.
- Added custom `FormRequest` classes for validation in translation and word endpoints.
- Added **service** and **repository** layers for managing `Translation` logic.
- Added `entrypoint.sh` script to set proper permissions for Laravel storage and bootstrap folders.
- Added Laravel Pint configuration.
- Added Composer commands:
  - `"pint-test": "./vendor/bin/pint --config=pint.json --test"`
  - `"pint": "./vendor/bin/pint --config=pint.json"`

### Changed
- Updated `README.md`:
  - Refreshed version badges.
- Updated `TODO.md` with completed and pending tasks.

### Fixed
- Fixed logic in `index`, `show`, `update`, `store`, and `destroy` methods in `WordController`.
- Refactored word and translation request validation using custom `FormRequest` classes.
- Fixed implementation in **Word** service and repository layers.
- Fixed type casting issues:
  - Converted route parameter `word` from `string` to `int` in controller before passing to service.
  - Converted route parameter `translation` from `string` to `int` in controller before passing to service.
  - Updated `IndexRequest::getCursor()` to return a `?string` instead of a `Stringable`.
- Removed duplicate Xdebug extension line in `xdebug.ini` that caused the extension to load twice.
- Fixed incorrect PHPStan configuration:
  - Updated `phpstan.neon` to reference `vendor/larastan/larastan/extension.neon`.

### Refactored
- Used **PHPStan** to refactor and improve code quality in `TranslationService`.
- Refactored codebase using Laravel Pint with its configuration.

### Updated
- Updated package `laravel/pint` from version `v1.11.0` to `v1.24.0`.

### Removed
- Removed package `laravel/sail`.

### Replaced
- Replaced abandoned package `nunomaduro/larastan` with `larastan/larastan`.

---

## [v1.12.3] - 2025-07-14

### Added
- Integrated **PHPStan** for static code analysis.

### Changed
- Executed PHPStan and made several improvements to comply with static analysis standards and Laravel best practices.
- **Updated** `docs/guides/setup.md`:
  - Added `php artisan jwt:secret` setup step.
  - Noted the required `.env` variable `JWT_SECRET=`.

### Fixed
- Refactored the method signature of `getAllWordsWithTranslations()` in the `WordRepository`:
  - Removed the default value from the `$perPage` parameter to avoid deprecated parameter order issues in PHP 8+.
- Improved the `translations` relationship in the `Word` model:
  - Added missing import for the `Translation` model.
  - Added PHPDoc annotations to improve Larastan compatibility and IDE autocompletion.
- Resolved property access warnings in `WordResource`:
  - Added `@mixin \App\Models\Word` annotation to inform static analyzers of the underlying model.
  - Explicitly typed the `$translation` variable to help PHPStan infer the correct model and avoid false positives.
- Fixed return type in `WordCollection::toArray()`:
  - Converted the collection to a plain array using `$this->collection->all()` to satisfy PHPStan's type expectations.
- Updated PHPDoc types of `$fillable` and `$hidden` in `User` model to use `list<string>` to comply with Laravel base model definition and fix PHPStan covariance warnings.
- **Fixed** `docs/api/README.md`:
  - Clarified authentication endpoints

---

## [v1.12.2] - 2025-07-13

### Changed
- Upgraded Laravel framework from **v11.44.7** to **v12.20.0**.
- Updated `README.md`:
  - Refreshed version badges.

---

## [v1.12.1] - 2025-07-11

### Changed
- Upgraded PHP-FPM from 8.4.8 to 8.4.10
- Updated Node.js version in Dockerfile from 20 to 22.17.0
- Updated Redis Docker image from 7.4.4 to 7.4.5
- Improved `README.md` layout with horizontally aligned badges and updated Redis version badge

### Security
- Upgraded the following dependencies to address known vulnerabilities and ensure compatibility with the latest tooling:
  - `axios`
  - `laravel-vite-plugin`
  - `vite`

---

## [v1.12.0] - 2025-06-12

### Added
- Redis caching implementation:
  - Added Redis service to Docker Compose
  - Created `CacheService` for centralized cache management
  - Implemented cache layer for `/api/v1/words` endpoints
  - Added `CacheableWordRepository` using decorator pattern
  - Configured Redis as the default cache driver
- Added detailed Redis cache documentation in `docs/cache/redis.md`

### Changed
- Upgraded PHP-FPM from 8.4.7 to 8.4.8
- Upgraded Composer from 2.8.8 to 2.8.9
- Upgraded Laravel Framework from 11.42.2 to 11.42.7
- Upgraded JWT-Auth from 2.7.3 to 2.8.2
- Upgraded Sail from 1.41.0 to 1.41.1
- Upgraded PHPUnit from 10.5.45 to 10.5.46

### Infrastructure
- Added Redis container configuration in `docker-compose.yml`
- Set up Redis connection parameters in `.env.example`

---

## [v1.11.0] - 2025-05-11

### Changed
- Updated MySQL image in Docker Compose from `mysql:8.0.42` to `mysql:8.4.5`.

### Added
- Added `docker/mysql/conf.d/my-overrides.cnf` file for variable overrides adapted to MySQL 8.4.5.

---

## [v1.10.0] - 2025-05-10

### Added
- Cursor-based pagination on `GET /api/v1/words` with `per_page` and `cursor` parameters for efficient infinite scrolling.  
- `WordIndexRequest` to validate `per_page` and `cursor` inputs on the `words` endpoint.  
- `WordResource` and `WordCollection` for consistent, self-documented JSON output.  
- `CursorPaginationLinks` trait to centralize next/previous link generation.  
- API versioning: all routes now prefixed with `/api/v1`.  

### Changed
- Upgraded PHP-FPM from 8.4.6 to 8.4.7.  
- Updated PHP dependencies via `composer update`.  
- Slimmed down `README.md`, offloading detailed REST, setup, GraphQL and pagination docs into `docs/`.  
- Enhanced `docs/guides/rest.md` with a top-level **Pagination** section and flow examples.  
- Added SonarQube integration notes to `docs/development/README.md`.  
- Refactored `WordService`: renamed constructor property from `$wordRepository` to `$repo` for uniformity.  
- Refactored `WordRepository`: simplified constructor signature and `getAllWordsWithTranslations()` implementation.  
- Updated `TODO.md` with current tasks and status.  
- Changed `GET /api/v1/words/{id}` to return `WordResource` directly (no `data` wrapper).  

  **Example response**  
  ```json
  {
    "id": 1,
    "word": "Computer",
    "locale": {
      "es": "Computadora / Ordenador",
      "de": "Computer / Rechner"
    }
  }

---

## [v1.9.1] - 2025-05-02

### Documentation
- Added missing PHPUnit coverage command documentation:
  - Added detailed instructions for generating coverage reports
  - Documented coverage configuration for SonarQube integration
  - Updated setup steps in SonarQube documentation

---

## [v1.9.0] - 2025-05-01

### Added
- Integrated **SonarQube** for code quality and static analysis.
- Added PHPUnit coverage report generation (`coverage.xml`) for SonarQube integration.
- Added a new item to the **To-Do List** feature.
- Added specific Docker images for **PHP 8.4** and **MySQL 8** in `docker-compose.yml` and `Dockerfile`.

### Changed
- Updated PHP dependencies via `composer update`.

### Documentation
- Enhanced **README.md** with:
  - Added new badges for technologies (MySQL, Node, NPM, PHPUnit, Composer, SonarQube, JWT)
  - Updated existing badges with logos and current versions
  - Improved badge organization and readability
  - Added SonarQube setup and configuration section
  - Updated table of contents to include new sections
- Added comprehensive SonarQube documentation in `docs/development/sonarqube.md`:
  - Detailed setup instructions
  - Configuration guidelines
  - Best practices for token management
  - Project improvements based on analysis

---

## [v1.8.0] - 2025-04-15

### Added
- Introduced a new **To-Do List** feature.
- Added `WordServiceTest` using PHPUnit.

### Fixed
- In `WordService`, moved `DB::beginTransaction()` to the appropriate location in `destroyWordWithTranslations`.

### Security
- Updated security-related packages in `composer.json`.

### Documentation
- Enhanced the **README** file with updated badges, a comprehensive table of contents, and improved formatting for clarity.

---

## [v1.7.0]

### Added
- N/A

### Changed
- Refactored `WordService` to implement the Repository Design Pattern.

### Documentation
- Updated `README.md`.

---

## [v1.6.0]

### Added
- Docker support with Docker Compose.

### Changed
- Upgraded to Laravel v11.41.3.
- Upgraded to PHP 8.4 and PHP-FPM 8.4.

### Fixed
- Addressed security vulnerabilities in `package.json`.

---

## [v1.5.0]

### Added
- Custom Content-Security-Policy headers.
- Welcome page.

### Documentation
- Updated `README.md`.

---

## [v1.4.0]

### Added
- JWT authentication support.

### Changed
- Updated Docker Compose configuration.

### Fixed
- Fixed PHPUnit configuration.

---

## [v1.3.1]

### Fixed
- Corrected project name in `package.json`.

---

## [v1.3.0]

### Added
- Unit tests for `WordService` using PHPUnit.

---

## [v1.2.0]

### Added
- `GET /words` endpoint.
- `PUT /words` endpoint.

### Changed
- Introduced `wordServices` abstraction.
- Refactored `WordController` for improved structure.
- Updated Docker Compose configuration.

### Documentation
- Updated `README.md`.

### Fixed
- Resolved issue in `createWordWithTranslations` service.

---

## [v1.1.0]

### Added
- `DELETE /words` endpoint.

### Fixed
- Translation constraints in the database.
- `POST /words` endpoint logic.

---

## [v1.0.0]

### Added
- Initial Laravel 10 project setup.
- Word migration.
- `POST /words` and `GET /words` endpoints.
- Support for basic CRUD operations on words (POST and GET only initially).

### Changed
- Removed timestamps from `GET /words` response.

### Documentation
- Created initial `CHANGELOG.md`.

---

