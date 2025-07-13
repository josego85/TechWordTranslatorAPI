# API Documentation

All API endpoints are served under `/api` and versioned in the path.

## Base URL

```
https://your-domain.com/api
```

## Authentication

- **Register**: `POST /user/register`  
- **Login**: `POST /user/login`
- **Refresh token**: `POST /auth/refresh`  
- Returns JWT in JSON payload. Send as `Authorization: Bearer <token>`.

## Format

- Request & response bodies use JSON.  
- All endpoints return appropriate HTTP status codes.

## Endpoints

See the dedicated guides for details:

- [REST API Reference](../guides/rest.md)  
- [GraphQL API Reference](../guides/graphql.md)
