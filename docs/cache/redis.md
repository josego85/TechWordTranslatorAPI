# Redis Cache Implementation

## Overview

This API implements a Redis caching layer to reduce database load for word queries and paginated listings.

## Configuration

### Environment Variables

```env
REDIS_HOST=localhost
REDIS_PORT=6379
```

## Cache Strategy

### Key Features
- Simple Redis caching implementation
- Automatic cache invalidation
- TTL-based cache expiration (24 hours)

### Cache Keys Structure
```
word:{id}                               // Single word cache
words:perPage:{perPage}:cursor:{cursor} // Paginated listings cache
```

### Settings
- Fixed cache TTL: 24 hours (1440 minutes, not configurable)

## Implementation Details

### Available Cache Methods
```php
// Remember a value in cache
remember(string $key, callable $callback)

// Remove value(s) from cache
forget(string|array $keys)

// Generate cache key for single word
generateWordKey(int $id)

// Generate cache key for paginated listings
generateWordsKey(int $perPage, ?string $cursor)
```

### Cache Invalidation
The cache is automatically invalidated in these scenarios:
- When a word record is updated via the WordService
- When paginated results are updated
