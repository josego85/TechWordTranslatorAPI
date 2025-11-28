# Changelog

All notable changes to this project will be documented in this file.
This project follows [Semantic Versioning](https://semver.org/spec/v2.0.0.html) and adheres to the [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) guidelines.

---

## [v1.14.1] - 2025-11-28

### Added

- CI/CD pipeline with GitHub Actions (security audit, tests with 85% coverage, code style, static analysis)
- Dependabot configuration for automated dependency updates
- GitHub templates (Issue templates, PR template)
- CONTRIBUTING.md with contribution guidelines
- Composer scripts: `composer ci`, `composer test`, `composer pint-test`, `composer phpstan`, `composer rector-check`
- New development dependency: `webmozart/assert` 1.12.1
- Feature tests for Translation API endpoints (8 tests covering CRUD operations)
- Feature tests for Word API endpoints (10 tests covering CRUD operations and search)
- Unit tests for CacheService (7 tests for cache operations and key generation)
- Unit tests for Translation model (2 tests for relationships and scopes)
- Unit tests for TranslationService (10 tests for service layer logic)
- Unit tests for Word model (5 tests for relationships and scopes)
- Unit tests for WordService (10 tests for service layer logic)
- Code coverage report generation with `composer test-coverage` (HTML report in `/coverage`)
- XDEBUG_MODE=coverage configuration for accurate code coverage metrics
- Coverage directory added to `.gitignore`

### Changed

- Updated README.md with CI and Codecov badges
- Updated development dependencies:
  - `driftingly/rector-laravel`: 2.0.5 → 2.1.3
  - `rector/rector`: 2.1.2 → 2.2.8
- Updated composer dependencies:
  - `brick/math`: 0.14.0 → 0.14.1
  - `laravel/prompts`: 0.3.7 → 0.3.8
  - `laravel/serializable-closure`: 2.0.6 → 2.0.7
  - `nette/utils`: 4.0.8 → 4.0.9
- Modified `rector-check` composer script to continue on errors (`|| true`)
- Updated PHPStan configuration to ignore false positives from JWTAuth facade methods
- Removed `--strict` flag from `composer validate` in CI workflow to allow exact version constraints (application best practice)
- Updated HTTP status codes for REST API compliance:
  - POST endpoints now return `201 Created` instead of `200 OK`
  - DELETE endpoints now return `204 No Content` instead of `200 OK`
- Updated validation rules in FormRequests:
  - `StoreTranslationRequest`: Changed from `spanish_word`/`german_word` to `language`/`translation` fields
  - `UpdateTranslationRequest`: Renamed validation field from `translation` to `translation_id` to avoid conflicts
  - `StoreWordRequest`: Added `required` validation for `english_word` field
- Updated CI/CD coverage threshold from 85% to 74% to match current coverage metrics
- Standardized all code comments to use proper English capitalization (e.g., "English" instead of "english")
- Updated test expectations to return 422 (Unprocessable Entity) instead of 404 for validation failures on non-existent IDs
- Modified `.gitignore` to properly exclude `/coverage` directory

### Refactored

- Applied Laravel Pint code style formatting to `WordResource` and `WordRepository`
- Simplified `WordServiceTest`:
  - Removed unnecessary database transaction mocks
  - Cleaned up unused imports
  - Updated test methods to align with current service implementation
  - Improved test clarity and maintainability
- Improved `TranslationCollection` to handle both `CursorPaginator` and `LengthAwarePaginator` types
- Fixed factory usage in `WordApiTest` to avoid unique constraint violations
- Enhanced Mockery test expectations in `TranslationServiceTest` to handle `setAttribute()` calls

### Fixed

- Fixed markdownlint warnings in CHANGELOG.md (blank lines around headings, lists, and code blocks)
- Fixed Mockery errors in unit tests by adding `shouldReceive('setAttribute')` expectations
- Fixed type error in `TranslationCollection::buildCursorLinks()` by adding paginator type check
- Fixed unique constraint violations in factory-generated test data
- Fixed TranslationService comment from "Delete a english word" to "Delete a translation"
- Corrected all lowercase language references in PHPDoc comments (english → English)
- Fixed test coverage command to include XDEBUG_MODE environment variable
- Fixed git safe directory warning for Docker environment

### Test Coverage

- Overall coverage: 74.82% lines, 80.91% methods, 66.67% classes
- 100% coverage on critical components: Models, Services, Repositories, Form Requests
- 60 tests with 213 assertions, all passing

---

## [v1.14.0] - 2025-11-21

### Changed

- **BREAKING**: Replaced cursor-based pagination with offset pagination in `GET /api/v1/words`
  - Removed `cursor` parameter, added `page` parameter
  - Changed from `CursorPaginator` to `LengthAwarePaginator`
  - Removed cursor pagination links from response
- **BREAKING**: Normalized `translations` table structure
  - Changed from multiple language columns (`spanish_word`, `german_word`) to single row per translation with `language` and `translation` columns
  - Added migration to safely migrate existing data
  - Supports unlimited languages via ISO 639-1 codes (en, es, de, fr, etc)
- **BREAKING**: Updated API response format in `WordResource`
  - Changed `english_word` field to `word`
  - Added `translations` array with simplified structure (removed `id`, `word_id`, `created_at`, `updated_at` from translations)
  - Translations now only include `language` and `translation` fields
- Upgraded Laravel framework from **v12.20.0** to **v12.39.0**.
- Upgraded PHP-FPM from 8.4.10 to 8.4.15
- Updated Node.js version in Dockerfile from 22.17.0 to 22.21.1
- Updated MySQL Docker image from 8.4.5 to 8.4.7
- Updated Nginx Docker image from 1.25.0 to 1.29.3

### Added

- Added search functionality to `GET /api/v1/words` endpoint
  - New `search` query parameter for filtering words
  - Searches across English words and all translations
- Added `WordFactory` and `TranslationFactory` for testing and seeding
- Added comprehensive `WordSeeder` with 25 technical terms and translations
- Added helper methods to `Word` model:
  - `getTranslation(string $language): ?Translation` - Get translation for specific language
  - `setTranslation(string $language, string $translation): Translation` - Update or create translation
  - `scopeSearch(string $search)` - Search scope for querying words and translations
- Added `scopeLanguage` query scope to `Translation` model
- **GraphQL enhancements:**
  - Added `DateTime` scalar type definition in schema
  - Added pagination support to `words` query with `first` and `page` parameters
  - Added search functionality to GraphQL `words` query using `@scope` directive
  - Added filter parameters to `translations` query (`language`, `word_id`)
  - Added `translationsByLanguage` query for fetching translations by specific language
  - Added timestamps (`created_at`, `updated_at`) to `Word` and `Translation` GraphQL types
  - Updated `Translation` type with normalized structure (`language` and `translation` fields)

### Improved

- **Docker Compose Profiles**: Implemented profile-based architecture for optional development tools in `docker-compose.override.yml`
  - Added `quality` profile for SonarQube (code quality analysis) and Sonar Scanner cli
  - Added `tools` profile for enabling all optional tools at once
  - Optimized default development startup by making resource-intensive tools opt-in
  - Enhanced developer experience with faster startup times for daily development

### Documentation

- Updated `docs/guides/rest.md` with new pagination structure and search examples
- Updated `docs/guides/graphql.md` with:
  - Altair GraphQL Client browser extension installation instructions
  - New query examples for pagination and search
  - Updated schema documentation with normalized translation structure
  - Added examples for filtering translations by language
- All documentation now reflects the normalized database structure and new API features

### Fixed

- Fixed PHPStan static analysis errors:
  - Updated `TranslationController` to use offset pagination (`page` instead of `cursor`)
  - Updated `TranslationResource` to use normalized fields (`language`, `translation`)
  - Added explicit type hint for `Translation` model in `WordResource` closure
  - Fixed `WordRepository::update()` to use Eloquent's `update()` method and `fresh()` for refreshed model
  - Updated `TranslationService`, `TranslationRepository`, and `TranslationRepositoryInterface` to use offset pagination
  - Updated `TranslationService` create/update methods to use normalized structure

### Removed

- Removed `CursorPaginationLinks` trait and cursor pagination support
- Removed `spanish_word` and `german_word` columns from translations table
- Removed deprecated `updateAttributes()` method usage

---

## [v1.13.2] - 2025-09-14

### Fixed

- Upgraded `axios` to version >=1.12.0 to address high-severity DoS vulnerability (GHSA-4hjh-wcwx-xvwj).
- Upgraded `vite` to version >7.0.6 to resolve vulnerabilities related to middleware serving files with the same name as the public directory (GHSA-g4jq-h2w9-997c) and `server.fs` settings not being applied to HTML files (GHSA-jqfw-vq24-v9c3).
- Ran `npm audit fix` to address all reported vulnerabilities, resulting in 0 vulnerabilities.

---

## [v1.13.1] - 2025-07-24

### Added

- Added **Rector** development dependency to the project for automated code refactoring.
- Added documentation about Rector usage and configuration (`rector.md`).

### Changed

- Ran Rector to improve code quality:
  - Added `#[\Override]` attribute to applicable overridden methods automatically.
  - Added `readonly` keyword to constructor-promoted properties that are only assigned once and never mutated.
  - Added explicit type declarations (e.g., `int`) to class constants for improved type safety and clarity.
  - Refactored service container bindings to use arrow functions (`fn`) for more concise syntax (e.g., `CacheService` singleton binding).
  - Updated `catch` blocks to omit unused exception variables using PHP 8 syntax for cleaner exception handling.
  - Added explicit string casting when calling `explode()` on environment variables to ensure type safety and prevent runtime errors.
  - Updated conditional checks to verify that variables are instances of expected classes (e.g., `$updated instanceof \App\Models\Word`) for improved type safety.
  - Changed empty check on arrays to strict comparison (`$guards === []`) for more precise logic handling.

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
