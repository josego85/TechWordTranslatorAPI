# GraphQL API

**Endpoint:** `POST /graphql` (also accepts `GET`)

**Client:** [Altair GraphQL Client](https://altairgraphql.dev/) — available for Chrome, Firefox, Edge.

Set endpoint to `http://localhost:8000/graphql`.

---

## Authentication

Mutations require a JWT token. Pass it as a header:

```
Authorization: Bearer <token>
```

Obtain a token via `POST /api/v1/user/login`. See [REST auth guide](rest.md).

---

## Queries (public, cached 24h)

### Words

```graphql
# Paginated + search
query {
  words(first: 15, page: 1, search: "auth") {
    data {
      id
      english_word
      categories { slug name }
      translations { language translation }
    }
    paginatorInfo { currentPage lastPage total hasMorePages }
  }
}

# Filter by category
query {
  words(first: 15, category: "networking") {
    data {
      id
      english_word
      categories { slug name }
      translations { language translation }
    }
    paginatorInfo { currentPage total }
  }
}

# Sort A→Z (default) or Z→A
query {
  words(first: 15, sort: ALPHA_ASC) {
    data { id english_word }
    paginatorInfo { currentPage total }
  }
}

# Combined — networking terms, Z→A
query {
  words(first: 15, category: "networking", sort: ALPHA_DESC) {
    data { id english_word categories { slug } }
    paginatorInfo { currentPage total }
  }
}

# Single word
query {
  word(id: 1) {
    id
    english_word
    categories { slug name }
    translations { language translation }
  }
}
```

`first` max: 100. Default: 15. Search matches English word and all translations.

`sort` accepts the `WordSort` enum: `ALPHA_ASC` (A→Z, default) or `ALPHA_DESC` (Z→A). Combinable with `search` and `category`.

### Translations

```graphql
# Filter by language and/or word
query {
  translations(language: "es", word_id: 5) {
    id language translation
    word { english_word }
  }
}

# All translations for a language
query {
  translationsByLanguage(language: "de") {
    id translation
    word { english_word }
  }
}

# Single translation
query {
  translation(id: 1) {
    id language translation
  }
}
```

---

## Mutations (JWT required)

### Words

```graphql
# Auto-classification (Ollama assigns categories automatically)
mutation {
  createWord(english_word: "Middleware") {
    id english_word
    categories { slug name }
    created_at
  }
}

# Manual category override
mutation {
  createWord(english_word: "mutex", categories: ["algorithms", "operating-systems"]) {
    id english_word
    categories { slug name }
  }
}

mutation {
  updateWord(id: 1, english_word: "API Gateway") {
    id english_word
    categories { slug name }
    updated_at
  }
}

mutation {
  deleteWord(id: 1) {
    id english_word
  }
}
```

### Translations

```graphql
mutation {
  createTranslation(word_id: 1, language: "es", translation: "Middleware") {
    id language translation
  }
}

mutation {
  updateTranslation(id: 1, language: "es", translation: "Intermediario") {
    id language translation updated_at
  }
}

mutation {
  deleteTranslation(id: 1) {
    id language translation
  }
}
```

---

## Schema

### Types

```graphql
type Category {
  id: ID!
  slug: String!
  name: String!
}

type Word {
  id: ID!
  english_word: String!
  categories: [Category!]!
  translations: [Translation!]!
  created_at: DateTime!
  updated_at: DateTime!
}

type Translation {
  id: ID!
  word_id: ID!
  language: String!   # ISO 639-1: en, es, de, fr, ...
  translation: String!
  word: Word!
  created_at: DateTime!
  updated_at: DateTime!
}
```

---

## Cache

All 5 query fields use `@cache(maxAge: 86400)` (24h, Redis tagged cache).

Mutations automatically invalidate the relevant cache entries via `@clearCache`. No manual intervention needed.

---

## Security Limits

Configured via environment variables (see `.env.example`):

| Setting | Default | Purpose |
|---------|---------|---------|
| `LIGHTHOUSE_MAX_QUERY_COMPLEXITY` | `200` | Prevents expensive nested queries |
| `LIGHTHOUSE_MAX_QUERY_DEPTH` | `5` | Limits nesting depth |
| `LIGHTHOUSE_SECURITY_DISABLE_INTROSPECTION` | `true` | Hides schema in production |

---

## Authorization

Mutations use the same dual-auth policy as REST:
- JWT user (`api` guard) → always allowed
- Sanctum token (`sanctum` guard) → must have `words:write` or `translations:write` ability

Unauthorized mutations return a `403` error in the GraphQL `errors` array.
