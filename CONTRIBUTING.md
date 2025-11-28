# Contributing to TechWordTranslatorAPI

Thank you for considering contributing to TechWordTranslatorAPI! We welcome contributions from the community.

## Code of Conduct

This project adheres to the [Contributor Covenant Code of Conduct](https://www.contributor-covenant.org/). By participating, you are expected to uphold this code.

## How to Contribute

### Reporting Bugs

Before creating bug reports, please check existing issues. When creating a bug report, include:

- Clear, descriptive title
- Steps to reproduce the behavior
- Expected vs actual behavior
- PHP/Laravel version, OS, and environment details
- Code samples or error messages (if applicable)

### Suggesting Features

Feature requests are welcome. Please provide:

- Clear use case and problem statement
- Proposed solution or implementation ideas
- Any alternatives you've considered

### Pull Requests

1. **Fork & Branch**
   - Fork the repository
   - Create a feature branch: `feature/your-feature` or `fix/issue-description`

2. **Code Quality**
   - Run `composer ci` before committing
   - Ensure all tests pass with 85%+ coverage
   - Follow PSR-12 and Laravel conventions
   - Add tests for new functionality

3. **Commit Standards**
   - Use [Conventional Commits](https://www.conventionalcommits.org/)
   - Examples: `feat:`, `fix:`, `docs:`, `test:`, `refactor:`

4. **Submit PR**
   - Use the PR template
   - Link related issues
   - Ensure all CI checks pass
   - Request review from maintainers

### Branch Naming

- `feature/*` - New features
- `fix/*` - Bug fixes
- `docs/*` - Documentation only
- `refactor/*` - Code refactoring
- `test/*` - Test additions/updates

## Development Setup

See [Getting Started Guide](docs/guides/setup.md) for detailed setup instructions.

Quick start:

```bash
composer install && npm ci
cp .env.example .env && php artisan key:generate
composer ci  # Verify everything works
```

## Quality Standards

All contributions must meet:

- ✅ **Tests**: 85%+ code coverage
- ✅ **Code Style**: PSR-12 via Laravel Pint
- ✅ **Static Analysis**: PHPStan level 2
- ✅ **Security**: No credentials, follow OWASP guidelines

Run locally: `composer ci`

## CI Pipeline

Your PR will be checked by:

- Dependency security review
- Composer security audit
- PHPUnit tests with coverage
- Laravel Pint (code style)
- PHPStan (static analysis)
- Frontend build validation

## Review Process

1. Maintainers review PRs within 3-5 business days
2. Address feedback professionally
3. Keep PRs focused and reasonably sized
4. Once approved and CI passes, maintainers will merge

## Getting Help

- 📖 [Documentation](docs/)
- 💬 [Discussions](https://github.com/proyectosbeta/TechWordTranslatorAPI/discussions)
- 🐛 [Issues](https://github.com/proyectosbeta/TechWordTranslatorAPI/issues)
