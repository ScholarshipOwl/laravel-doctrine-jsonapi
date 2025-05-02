# Filter Parsers

**Location:** `src/FilterParsers/`

Filter parsers are responsible for handling complex filtering logic on resource endpoints. They allow you to implement advanced filtering strategies for your JSON:API resources.

## Core Classes
- `ArrayFilterParser`: Parses array-based filters from the request (e.g., for field-based filtering).
- `SearchFilterParser`: Handles search queries on specific fields.
- `BuilderChain/Chain`: Enables chaining multiple filter parsers or filter members.

## Responsibilities
- Extract and process filter parameters from incoming requests.
- Apply filtering logic to Doctrine queries or resource collections.

## Example Usage
```php
$parser = new ArrayFilterParser($request, ['name', 'email']);
$filtered = $parser->apply($queryBuilder);
```

## Extension Points
- Create your own filter parser by extending `AbstractFilterParser`.
- Combine multiple parsers using the `BuilderChain` for advanced scenarios.
