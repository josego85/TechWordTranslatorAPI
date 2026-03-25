# Development Guide

## Branching

| Branch | Purpose |
|--------|---------|
| `main` | Production, protected |
| `develop` | Integration |
| `feature/**` | New features |
| `fix/**` | Bug fixes |
| `claude/**` | Claude Code generated branches |

CI/CD triggers on all branches above.

## Quality Checks

Run before every commit:

```bash
composer ci   # pint-test + phpstan + rector-check + test
```

Individual tools:

```bash
composer pint-test      # Style check (CI mode — no changes)
composer pint-fix       # Apply style fixes
composer phpstan        # Static analysis (level 5)
composer rector-check   # Refactoring suggestions (dry-run)
composer rector-fix     # Apply Rector changes
composer test           # PHPUnit (196 tests)
composer test-coverage  # PHPUnit + HTML coverage report
```

## Code Standards

- `declare(strict_types=1)` **required** in every PHP file
- Explicit types on all parameters and return types (PHPStan level 5)
- Repository pattern — never access models directly from controllers
- Business logic in `Services/`, not in controllers
- All input validation in Form Requests

See [Pint](pint.md) for style rules, [rector.md](rector.md) for automated refactorings.

## Testing

- **Framework:** PHPUnit 11 — tests in `tests/Feature/` and `tests/Unit/`
- **Database:** SQLite in-memory (fast, isolated)
- **Coverage threshold:** 74% (enforced in CI)

```bash
# Run all tests
composer test

# Single file
vendor/bin/phpunit tests/Feature/AuthApiTest.php

# Single test
vendor/bin/phpunit --filter test_user_can_login
```

## Migrations

```bash
php artisan migrate
php artisan migrate:fresh --seed   # Reset + seed (dev only)
```

## Tools

- [Pint](pint.md) — code style
- [rector.md](rector.md) — automated refactoring
- [sonarqube.md](sonarqube.md) — static analysis dashboard
- [xdebug.md](xdebug.md) — step debugger + coverage
- [nginx.md](nginx.md) — local web server config
