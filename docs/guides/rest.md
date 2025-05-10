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
        "english_word": "apple",
        "translations": {
          "es": "manzana",
          "de": "Apfel"
        }
      }
    ],
    "next_cursor": "eyJpZCI6MTV9",
    "prev_cursor": null
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
  "english_word": "apple",
  "translations": {
    "es": "manzana",
    "de": "Apfel"
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
