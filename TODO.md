# TODO

## Completed ✅

- ~~Add filter and order by name in endpoints~~ (v1.14.0 - Search functionality added)
- ~~Security improvements~~ (CORS, rate limiting, password validation, JWT TTL, security headers, CSP, per-email soft lockout, content audit trail — see SECURITY_AUDIT.md)
- ~~Add logs~~ (auth events + Word/Translation CRUD via Model Observers — fires for REST, GraphQL, and CLI)
- ~~Add cache for Translation endpoints~~ (CacheableTranslationRepository decorator, Redis keys `translation:{id}` + `translations:perPage:{n}:page:{n}`)
- ~~Add PHPUnit tests coverage~~ (196 tests, 566 assertions, 92.18% line coverage)
- ~~Integration tests for GraphQL queries and mutations~~ (WordGraphQLTest, TranslationGraphQLTest, MutationGraphQLTest — 34 tests)
- ~~GraphQL mutations~~ (createWord/updateWord/deleteWord + createTranslation/updateTranslation/deleteTranslation via custom resolvers → Services)
- ~~GraphQL cache~~ (`@cache(maxAge: 86400)` on all 5 query fields, Redis tagged cache)
- ~~GraphQL security limits~~ (`max_query_complexity: 200`, `max_query_depth: 5` via env vars)

## In Progress

- Add Opcache configuration
- Monitor (Grafana)
- Add Swagger/OpenAPI documentation
- Docker compose dev and prod environments
- ~~Add comprehensive docs for Xdebug configuration~~ (`docs/development/xdebug.md`)
- ~~Add comprehensive docs for Nginx setup~~ (`docs/development/nginx.md`)
- ~~Add comprehensive docs for Laravel Pint usage and configuration~~ (`docs/development/pint.md`)

- ~~Automatic thematic classification of words via Ollama (LLM)~~ (ClassificationService + CategoryRepository + many-to-many `categories`/`word_category`, 13 slugs, filter by `?category=` — 208 tests, 92.21% coverage)

## Recommended Next Steps 🚀

- Add Request validation for ISO 639-1 language codes
- Implement cache with tags for selective invalidation (REST layer)
- Unit tests for models and repositories
- E2E tests for REST API endpoints
- Performance benchmarks for search functionality
- Add husky
- Perfomance dockerfile dev app
