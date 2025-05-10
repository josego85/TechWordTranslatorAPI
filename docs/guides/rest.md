# REST API Reference

## Pagination

All list endpoints use **cursor-based pagination**:

- **Query Parameters**  
  - `per_page` (integer, default: 15)  
  - `cursor` (string, optional)

- **Response Format**  
  ```json
  {
    "data": [
      {
        "id": 1,
        "word": "Computer",
        "locale": {
          "es": "Computadora / Ordenador",
          "de": "Computer / Rechner"
        }
      },
      {
        "id": 2,
        "word": "Software",
        "locale": {
          "es": "Software",
          "de": "Software"
        }
      },
    ],
    "links": {
        "first": null,
        "last": null,
        "prev": [
          null,
          null
        ],
        "next": [
          null,
          null
        ]
      },
      "meta": {
        "path": "http://localhost:8000/api/words",
        "per_page": 15,
        "next_cursor": null,
        "prev_cursor": null
      }
  }
  ```

- **Usage Flow**  
  1. First request:  
     ```
     GET /api/v1/words?per_page=10
     ```  
  2. Next page:  
     ```
     GET /api/v1/words?per_page=10&cursor=<next_cursor>
     ```

---

## Words

### List words (paginated)
```
GET /api/v1/words
```
**Query Parameters** inherit the pagination spec above.

### Get single word
```
GET /api/v1/words/{id}
```
**Response**  
```json
{
  "id": 1,
  "word": "Computer",
  "locale": {
    "es": "Computadora / Ordenador",
    "de": "Computer / Rechner"
  }
}
```

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
