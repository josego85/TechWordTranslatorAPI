# Development Guide

## Branching

- `main` — production-ready  
- `develop` — integration branch  
- `feature/*` — new features  
- `hotfix/*` — urgent fixes

## Code Standards

- Follow PSR-12 coding standards  
- Use PHPDoc for public methods  
- Enforce with PHP-CS-Fixer or Laravel Pint  

## Migrations & Seeders

```bash
php artisan migrate
php artisan db:seed
```

## Testing

- Unit tests: `tests/Unit`  
- Feature tests: `tests/Feature`  
- Run all tests:  
  ```bash
  php artisan test
  # or
  vendor/bin/phpunit
  ```

---

## Development Tools

### Dependencies Update

```bash
# Check outdated packages
composer outdated

# Update dependencies
composer update
```

### Unit Tests

```bash
# Run inside Docker container (if applicable)
docker exec -it app-container bash
php artisan test
```

### Format & Lint

```bash
# PHP-CS-Fixer (dry-run)
vendor/bin/php-cs-fixer fix --dry-run --diff

# Laravel Pint
vendor/bin/pint --test
```

---

## Tool Integration

### Code Quality

[See SonarQube documentation](sonarqube.md)
