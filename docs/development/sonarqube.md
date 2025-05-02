# Code Quality and SonarQube Integration

As of **version 1.9.0**, the Tech Word Translator API project integrates **SonarQube** to enhance code quality, maintainability, and security through continuous inspection and analysis.

## SonarQube Overview

**SonarQube** is a leading tool for continuous inspection of code quality. It automatically analyzes code to detect bugs, code smells, security vulnerabilities, and adherence to best practices.

## Setup Instructions

SonarQube is included as a service in the project's `docker-compose.dev.yml` file for streamlined local development and code quality analysis.

### 1. Install SonarQube via Docker Compose

Ensure that Docker and Docker Compose are installed on your system before proceeding.

Start all services, including SonarQube:

```bash
docker compose -f docker-compose.dev.yml up --build -d
```

SonarQube will be accessible at:

- [http://localhost:9000](http://localhost:9000)

### 2. Default Credentials

- **Username:** `admin`
- **Password:** `admin`

_Upon your first login, you will be prompted to change the default password for security purposes._

### 3. Create a SonarQube Project and Token

- Log in to SonarQube.
- Create a new project manually.
- Generate an authentication **token** for scanner usage.

**Important:** Avoid hardcoding tokens in project files to prevent security risks.

### 4. Configure Project for SonarQube

The project includes a pre-configured `sonar-project.properties` file with the essential settings for SonarQube integration.

#### Requirements:

- Create a `.env` file in the project root containing:

```env
SONAR_TOKEN=your_generated_token
```

- Ensure `.env` is listed in `.gitignore` to avoid exposing secrets.

### 5. Generate Code Coverage Report

Before running the SonarQube analysis, generate a code coverage report:

```bash
XDEBUG_MODE=coverage \
./vendor/bin/phpunit \
  --do-not-cache-result \
  --coverage-clover coverage.xml
```

This command:
- Enables Xdebug coverage mode
- Runs PHPUnit without caching results
- Generates a coverage report in Clover XML format
- Saves the report as `coverage.xml` for SonarQube to analyze

### 6. Run the scanner with:

```bash
docker compose run --rm scanner
```

### 6. Project Improvements Based on SonarQube Recommendations

As of **version 1.9.0**, the following improvements were applied:

- **Separation of Source and Test Files:**
  - `sonar.sources=app,routes`
  - `sonar.tests=tests`
  - `sonar.exclusions=vendor/**,storage/**,bootstrap/**`
  - `sonar.test.inclusions=tests/**/*.php`
- **Token Management Best Practices:**
  - Authentication tokens must never be committed to the source code.
- **General Code Quality Improvements:**
  - Refactored the codebase to address minor code smells and enhance overall maintainability.

## Useful Links

- [SonarQube Official Site](https://www.sonarsource.com/products/sonarqube/): Official website for SonarQube, providing detailed product information.
- [SonarScanner CLI Documentation](https://docs.sonarqube.org/latest/analysis/scan/sonarscanner/): Documentation for the SonarScanner CLI tool.
- [SonarQube on Docker Hub](https://hub.docker.com/_/sonarqube): Official Docker image for SonarQube.

> 📅 **Last Updated:** 2025-05-01

---