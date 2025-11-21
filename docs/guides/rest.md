# REST API Reference

## Pagination

All list endpoints use **offset-based pagination**:

- **Query Parameters**
  - `per_page` (integer, default: 15, max: 100)
  - `page` (integer, default: 1)
  - `search` (string, optional)

- **Response Format**
  ```json
  {
    "data": [
      {
        "id": 1,
        "word": "Computer",
        "created_at": "2025-11-21T13:20:22.000000Z",
        "updated_at": "2025-11-21T13:20:22.000000Z",
        "translations": [
          {
            "language": "en",
            "translation": "Computer"
          },
          {
            "language": "es",
            "translation": "Computadora / Ordenador"
          },
          {
            "language": "de",
            "translation": "Computer / Rechner"
          }
        ]
      }
    ],
    "links": {
      "first": "http://localhost:8000/api/v1/words?page=1",
      "last": "http://localhost:8000/api/v1/words?page=2",
      "prev": null,
      "next": "http://localhost:8000/api/v1/words?page=2"
    },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 2,
      "path": "http://localhost:8000/api/v1/words",
      "per_page": 15,
      "to": 15,
      "total": 25
    }
  }
  ```

- **Usage Flow**
  1. First request:
     ```
     GET /api/v1/words?per_page=15&page=1
     ```
  2. Next page:
     ```
     GET /api/v1/words?per_page=15&page=2
     ```
  3. With search:
     ```
     GET /api/v1/words?per_page=15&page=1&search=auth
     ```

---

## Words

### List words (paginated with search)

```
GET /api/v1/words
```

**Query Parameters**
- `per_page` (integer, optional, default: 15, max: 100) - Items per page
- `page` (integer, optional, default: 1) - Page number
- `search` (string, optional) - Search in english_word and all translations

**Examples**

Get all words (first page):
```bash
curl http://localhost:8000/api/v1/words
```

Search for "auth":
```bash
curl "http://localhost:8000/api/v1/words?search=auth"
```

Get page 2 with 20 items:
```bash
curl "http://localhost:8000/api/v1/words?per_page=20&page=2"
```

### Get single word

```
GET /api/v1/words/{id}
```

**Response**
```json
{
  "id": 1,
  "word": "Computer",
  "created_at": "2025-11-21T13:20:22.000000Z",
  "updated_at": "2025-11-21T13:20:22.000000Z",
  "translations": [
    {
      "language": "en",
      "translation": "Computer"
    },
    {
      "language": "es",
      "translation": "Computadora / Ordenador"
    },
    {
      "language": "de",
      "translation": "Computer / Rechner"
    }
  ]
}
```

### Create word

```
POST /api/v1/words
```

**Request Body**
```json
{
  "english_word": "Algorithm",
  "translations": {
    "es": "Algoritmo",
    "de": "Algorithmus"
  }
}
```

**Response** (201 Created)
```json
{
  "id": 26,
  "word": "Algorithm",
  "created_at": "2025-11-21T14:30:00.000000Z",
  "updated_at": "2025-11-21T14:30:00.000000Z",
  "translations": [
    {
      "language": "en",
      "translation": "Algorithm"
    },
    {
      "language": "es",
      "translation": "Algoritmo"
    },
    {
      "language": "de",
      "translation": "Algorithmus"
    }
  ]
}
```

### Update word

```
PUT /api/v1/words/{id}
```

**Request Body**
```json
{
  "english_word": "Updated Word"
}
```

### Delete word

```
DELETE /api/v1/words/{id}
```

**Response** (200 OK)
```json
{
  "message": "Word deleted successfully"
}
```

---

## Search Functionality

The `search` parameter searches across:
- English words (`english_word`)
- All translations in any language

**Example:** `search=auth` will find:
- "Authentication" (English word)
- "Autenticación" (Spanish translation)
- "Authentifizierung" (German translation)

---

## Translation Structure

Translations use ISO 639-1 language codes:
- `en` - English
- `es` - Spanish (Español)
- `de` - German (Deutsch)
- `fr` - French (Français)
- `it` - Italian (Italiano)
- `pt` - Portuguese (Português)

Each word includes an English translation by default.

---

## Auth

### Login
```
POST /api/v1/auth/login
```
**Body**  
```json
{ "email": "...", "password": "..." }
```
**Response**  
```json
{ "access_token": "...", "expires_in": 3600 }
```

### Refresh
```
POST /api/v1/auth/refresh
```
**Response**  
```json
{ "access_token": "...", "expires_in": 3600 }
```
