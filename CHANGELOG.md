# Changelog

All notable changes to this project will be documented in this file.  
This project follows [Semantic Versioning](https://semver.org/spec/v2.0.0.html) and adheres to the [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) guidelines.

---

## [v1.12.0] - 2025-06-11

### Added
- Redis caching implementation:
  - Added Redis service to Docker Compose
  - Created `CacheService` for centralized cache management
  - Implemented cache layer for `/api/v1/words` endpoints
  - Added `CacheableWordRepository` using decorator pattern
  - Configured Redis as the default cache driver

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

## [v1.11.0] - 2025-05-11

### Changed
- Updated MySQL image in Docker Compose from `mysql:8.0.42` to `mysql:8.4.5`.

### Added
- Added `docker/mysql/conf.d/my-overrides.cnf` file for variable overrides adapted to MySQL 8.4.5.

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

## [v1.9.1] - 2025-05-02

### Documentation
- Added missing PHPUnit coverage command documentation:
  - Added detailed instructions for generating coverage reports
  - Documented coverage configuration for SonarQube integration
  - Updated setup steps in SonarQube documentation

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

