# Security & Performance

## Authentication

- All protected endpoints require JWT  
- Send `Authorization: Bearer <token>`

## Content Security Policy

Recommended CSP header:

```
Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline';
```

## Rate Limiting

API routes are throttled at **60 reqs/min** per user/IP.  
Adjust in `app/Providers/RouteServiceProvider.php`.
