# Changelog

All notable changes to this project will be documented in this file.  
This project follows [Semantic Versioning](https://semver.org/spec/v2.0.0.html) and adheres to the [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) guidelines.

---

## [v1.9.0] - 2025-05-01

### Added
- Integrated **SonarQube** for code quality and static analysis.
- Added PHPUnit coverage report generation (`clover.xml`) for SonarQube integration.
- Added a new item to the **To-Do List** feature.
- Added specific Docker images for **PHP 8.4** and **MySQL 8** in `docker-compose.yml` and `Dockerfile`.

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

