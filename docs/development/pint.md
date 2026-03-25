# Laravel Pint

Code style enforcer based on PHP-CS-Fixer. Config: `pint.json`. Preset: `laravel`.

## Commands

```bash
# Check only (used in CI — fails if any file needs changes)
composer pint-test

# Fix in place
composer pint-fix
```

## Key Rules

| Rule | Value | Why |
|------|-------|-----|
| `declare_strict_types` | `true` | **Required** in every PHP file |
| `single_quote` | `true` | Consistent string literals |
| `array_syntax` | `short` (`[]`) | Modern PHP |
| `ordered_imports` | `alpha` | Deterministic, diff-friendly |
| `no_unused_imports` | `true` | Keep files clean |
| `binary_operator_spaces` | align `=` | Readable assignments |

## CI Enforcement

`composer ci` runs `pint-test` as the first step. A PR with style violations will fail CI.

## Excluded paths

```json
"exclude": ["storage", "bootstrap/cache", "vendor"]
```

## Editor Integration

Most editors can run Pint on save via a file watcher or extension:
- **VS Code**: [Laravel Pint extension](https://marketplace.visualstudio.com/items?itemName=open-southeners.laravel-pint) or configure a task pointing to `./vendor/bin/pint`
- **PHPStorm**: External Tools → `./vendor/bin/pint $FilePath$`
