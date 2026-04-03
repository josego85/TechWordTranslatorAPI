# Automatic Thematic Classification

TechWordTranslatorAPI automatically classifies English technical words into thematic categories when they are created or updated. Classification is powered by a local LLM (Ollama) via **`prism-php/prism`**, a provider-agnostic Laravel package.

---

## How it works

1. A word is created (`POST /api/v1/words`) or updated (`PUT /api/v1/words/{id}`).
2. `ClassificationService` sends the English word to Ollama (llama3.2 by default) with a constrained prompt that restricts the output to the 13 allowed category slugs.
3. The LLM response is parsed; only valid slugs are kept.
4. Categories are synced to the `word_category` pivot table (many-to-many).
5. The word is returned with its `categories` array embedded.

If Ollama is unavailable, the word is saved without categories (classification is enrichment, not a critical path).

---

## Categories (v1 — 13 slugs)

| Slug | Display name |
|------|-------------|
| `networking` | Networking |
| `databases` | Databases |
| `security` | Security |
| `algorithms` | Algorithms |
| `data-structures` | Data Structures |
| `operating-systems` | Operating Systems |
| `programming-languages` | Programming Languages |
| `web` | Web |
| `cloud` | Cloud |
| `devops` | DevOps |
| `hardware` | Hardware |
| `artificial-intelligence` | Artificial Intelligence |
| `other` | Other |

A word can belong to **1–3 categories** (multi-label, many-to-many). Example:

| Word | Categories |
|------|-----------|
| API | `web`, `networking`, `programming-languages` |
| Docker | `devops`, `cloud`, `operating-systems` |
| TLS | `security`, `networking` |
| mutex | `algorithms`, `operating-systems` |

---

## Ollama setup (native install, NOT Docker)

Ollama must be installed natively on the host machine. Containerizing Ollama is not supported because GPU passthrough in Docker requires complex runtime configuration and breaks portability.

### Linux

```bash
curl -fsSL https://ollama.com/install.sh | sh
ollama pull llama3.2
```

Ollama runs as a systemd service on port `11434` automatically after install.

### macOS

```bash
brew install ollama
ollama pull llama3.2
ollama serve   # or use the Ollama.app
```

### Connecting from the Laravel Docker container

| Platform | `OLLAMA_URL` value |
|----------|-------------------|
| Mac / Windows | `http://host.docker.internal:11434` |
| Linux | `http://172.17.0.1:11434` (Docker bridge gateway) |

On Linux, add the following to `docker-compose.yml` so the container resolves `host.docker.internal`:

```yaml
extra_hosts:
  - "host.docker.internal:host-gateway"
```

---

## Environment variables

Add to `.env` (see `.env.example` for comments):

```bash
OLLAMA_URL=http://host.docker.internal:11434   # or 172.17.0.1:11434 on Linux
OLLAMA_MODEL=llama3.2
```

The model is configurable. Any Ollama-compatible model that follows instructions reliably can be used (e.g. `llama3.1`, `mistral`, `phi3`).

---

## Changing the LLM provider

`prism-php/prism` is provider-agnostic. To switch from Ollama to OpenAI or Anthropic (e.g. in production):

1. Install the provider SDK if required.
2. Update `config/prism.php` with the provider credentials.
3. Change `ClassificationService` to use `Provider::OpenAI` (or `Provider::Anthropic`).
4. Update the `OLLAMA_MODEL` env var (or add a separate `CLASSIFICATION_MODEL` env var).

No other code changes are required.

---

## REST API integration

### Response — word with categories

All word endpoints now include a `categories` array:

```json
{
  "id": 1,
  "word": "Docker",
  "categories": [
    { "slug": "devops", "name": "DevOps" },
    { "slug": "cloud", "name": "Cloud" },
    { "slug": "operating-systems", "name": "Operating Systems" }
  ],
  "translations": [ ... ],
  "created_at": "...",
  "updated_at": "..."
}
```

### Filter by category

```bash
GET /api/v1/words?category=networking
GET /api/v1/words?category=security&per_page=20
```

### Manual category override

Pass `categories` in the request body to skip automatic classification:

```bash
POST /api/v1/words
Authorization: Bearer <token>
Content-Type: application/json

{
  "english_word": "mutex",
  "translations": { "es": "mutex", "de": "Mutex" },
  "categories": ["algorithms", "operating-systems"]
}
```

Only slugs from the allowed list are accepted. Invalid slugs are ignored.

---

## GraphQL integration

### Query with category filter

```graphql
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
```

### Mutation with categories override

```graphql
mutation {
  createWord(
    english_word: "mutex"
    categories: ["algorithms", "operating-systems"]
  ) {
    id english_word
    categories { slug name }
  }
}
```

---

## Testing

All classification tests use `Prism::fake()` — no real Ollama connection required:

```bash
composer test
# or
vendor/bin/phpunit tests/Unit/Services/ClassificationServiceTest.php
vendor/bin/phpunit tests/Unit/Repositories/CategoryRepositoryTest.php
```

CI/CD does not require Ollama. The 208-test suite runs fully offline.

---

## Graceful degradation

| Scenario | Behaviour |
|----------|-----------|
| Ollama not running | Word saved without categories; error logged |
| LLM returns invalid slugs | Invalid slugs discarded; `['other']` used as fallback |
| LLM returns empty response | `['other']` fallback applied |
| Network timeout (30s) | Exception caught; word saved without categories |
| `categories` provided in request | Ollama skipped entirely; provided slugs used directly |
