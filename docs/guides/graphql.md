# GraphQL API Reference

## Endpoint

```
POST /graphql
```
Or open GraphQL Playground at `/graphql`.

## Example Queries

### Fetch paginated words
```graphql
query GetWords($perPage: Int!, $cursor: String) {
  words(perPage: $perPage, cursor: $cursor) {
    data {
      id
      english_word
      translations {
        spanish_word
        german_word
      }
    }
    nextCursor
    prevCursor
  }
}
```

### Fetch single word
```graphql
query {
  word(id: 1) {
    english_word
    translations {
      spanish_word
      german_word
    }
  }
}
```

## Schema Highlights

- **Query.words(perPage: Int, cursor: String): WordCursorPage**  
- **Query.word(id: ID!): Word**  
- **Word**  
  - `id: ID!`  
  - `english_word: String!`  
  - `translations: [Translation!]!`
