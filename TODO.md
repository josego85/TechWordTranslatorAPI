# TODO

## Completed ✅

- ~~Add filter and order by name in endpoints~~ (v1.14.0 - Search functionality added)
- ~~Security improvements~~ (CORS, rate limiting, password validation, JWT TTL, security headers, CSP, per-email soft lockout, content audit trail — see SECURITY_AUDIT.md)
- ~~Add logs~~ (auth events + Word/Translation CRUD mutations via Log:: — partition by date pending if volume requires it)
- ~~Add cache for Translation endpoints~~ (v1.17.0 - CacheableTranslationRepository decorator, Redis keys `translation:{id}` + `translations:perPage:{n}:page:{n}`, 8 unit tests, 92.18% coverage)

## In Progress

- Add PHPUnit tests coverage
- Add Opcache configuration
- Monitor (Grafana)
- Add Swagger/OpenAPI documentation
- Docker compose dev and prod environments
- Add comprehensive docs for Xdebug configuration
- Add comprehensive docs for Nginx setup
- Add comprehensive docs for Laravel Pint usage and configuration

## Recommended Next Steps 🚀

- Add Request validation for ISO 639-1 language codes
- Implement cache with tags for selective invalidation
- Unit tests for models and repositories
- Integration tests for GraphQL queries
- E2E tests for REST API endpoints
- Update Swagger/OpenAPI documentation with new schema
- Add GraphQL pagination tests
- Performance benchmarks for search functionality
