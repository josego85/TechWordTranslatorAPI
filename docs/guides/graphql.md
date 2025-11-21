# GraphQL API Reference

## Endpoint

```
POST /graphql
```

**Testing GraphQL queries:**
Install [Altair GraphQL Client](https://altairgraphql.dev/) browser extension:
- Chrome: [Altair GraphQL Client](https://chrome.google.com/webstore/detail/altair-graphql-client/flnheeellpciglgpaodhkhmapeljopja)
- Firefox: [Altair GraphQL Client](https://addons.mozilla.org/en-US/firefox/addon/altair-graphql-client/)
- Edge: Available in Microsoft Edge Add-ons

Then set endpoint to: `http://localhost:8000/graphql`

## Example Queries

### Fetch paginated words with search
```graphql
query {
  words(first: 15, page: 1, search: "auth") {
    data {
      id
      english_word
      translations {
        language
        translation
      }
    }
    paginatorInfo {
      currentPage
      lastPage
      total
      hasMorePages
    }
  }
}
```

### Fetch all words (default pagination)
```graphql
query {
  words {
    data {
      id
      english_word
      translations {
        language
        translation
      }
    }
    paginatorInfo {
      total
    }
  }
}
```

### Fetch single word
```graphql
query {
  word(id: 1) {
    id
    english_word
    translations {
      language
      translation
    }
  }
}
```

### Fetch translations by language
```graphql
query {
  translationsByLanguage(language: "es") {
    id
    translation
    word {
      english_word
    }
  }
}
```

### Fetch translations with filters
```graphql
query {
  translations(language: "de", word_id: 5) {
    id
    language
    translation
    word {
      english_word
    }
  }
}
```

## Schema Highlights

### Queries
- **words(first: Int, page: Int, search: String): [Word!]!**
  - `first`: Items per page (max 100, default 15)
  - `page`: Page number
  - `search`: Search in english_word and translations

- **word(id: ID!): Word**

- **translations(language: String, word_id: ID): [Translation!]!**

- **translation(id: ID!): Translation**

- **translationsByLanguage(language: String!): [Translation!]!**

### Types

**Word**
- `id: ID!`
- `english_word: String!`
- `translations: [Translation!]!`
- `created_at: DateTime!`
- `updated_at: DateTime!`

**Translation**
- `id: ID!`
- `word_id: ID!`
- `language: String!` (ISO 639-1: en, es, de, fr, etc)
- `translation: String!`
- `word: Word!`
- `created_at: DateTime!`
- `updated_at: DateTime!`

## Search Functionality

Search across English words and all translations:
```graphql
query {
  words(search: "auth") {
    data {
      english_word
      translations {
        language
        translation
      }
    }
  }
}
```

Finds: "Authentication", "Autenticación", "Authentifizierung", etc.
